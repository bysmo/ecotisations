<?php

namespace App\Services;

use App\Models\NanoCredit;
use App\Models\NanoCreditEcheance;
use App\Models\NanoCreditPalier;
use App\Notifications\NanoCreditOctroyeNotification;
use App\Models\Caisse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NanoCreditService
{
    /**
     * Décaisser un nano-crédit via PayDunya.
     * Cette méthode peut être appelée manuellement par l'admin ou automatiquement après validation des garants.
     */
    public function debourser(NanoCredit $nanoCredit, string $telephone, string $withdrawMode): array
    {
        if ($nanoCredit->statut === 'debourse' || $nanoCredit->statut === 'success') {
            return ['success' => false, 'message' => 'Ce crédit est déjà décaissé.'];
        }

        try {
            $paydunya = app(PayDunyaService::class);
        } catch (\Exception $e) {
            Log::error('NanoCreditService debourser: PayDunya non configuré', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'PayDunya n\'est pas configuré.'];
        }

        $callbackUrl = url()->route('paydunya.disburse.callback');
        $montant = (int) $nanoCredit->montant;

        // 1. Créer la facture de déboursement
        $result = $paydunya->createDisburseInvoice(
            $telephone,
            $montant,
            $withdrawMode,
            $callbackUrl
        );

        if (!$result['success']) {
            return ['success' => false, 'message' => $result['message'] ?? 'Erreur lors de la création du déboursement.'];
        }

        // 2. Mettre à jour avec le token temporaire
        $nanoCredit->update([
            'telephone' => $telephone,
            'withdraw_mode' => $withdrawMode,
            'disburse_token' => $result['disburse_token'],
            'disburse_id' => (string) $nanoCredit->id,
        ]);

        // 3. Soumettre la facture pour exécution immédiate
        $submit = $paydunya->submitDisburseInvoice($result['disburse_token'], (string) $nanoCredit->id);

        if (!$submit['success']) {
            $nanoCredit->update([
                'error_message' => $submit['message'] ?? 'Erreur à la soumission',
            ]);
            return ['success' => false, 'message' => $submit['message'] ?? 'Soumission échouée.'];
        }

        // 4. Finaliser localement (statut debourse en attendant le callback de confirmation finale)
        $dateOctroi = now()->toDateString();
        $palier = $nanoCredit->palier;
        $dateFinRemb = $palier ? Carbon::parse($dateOctroi)->addDays((int) $palier->duree_jours)->toDateString() : null;

        $nanoCredit->update([
            'statut' => 'debourse',
            'date_octroi' => $dateOctroi,
            'date_fin_remboursement' => $dateFinRemb,
            'transaction_id' => $submit['transaction_id'] ?? null,
            'provider_ref' => $submit['provider_ref'] ?? null,
            'error_message' => null,
        ]);

        // 4.5. Automatisation des comptes liés au crédit
        $this->associerComptesFinanciers($nanoCredit);

        // 5. Générer les échéances
        if ($palier) {
            $this->genererEcheances($nanoCredit);
        }

        // 6. Notifications
        $nanoCredit->membre->notify(new NanoCreditOctroyeNotification($nanoCredit));
        app(EmailService::class)->sendNanoCreditOctroyeEmail($nanoCredit);

        return ['success' => true, 'message' => 'Crédit décaissé avec succès.'];
    }

    /**
     * Génère les échéances de remboursement selon le palier.
     */
    public function genererEcheances(NanoCredit $nanoCredit)
    {
        $palier = $nanoCredit->palier;
        if (!$palier) return;

        // Supprimer les anciennes échéances si existantes
        $nanoCredit->echeances()->delete();

        $amortissement = $palier->calculAmortissement((float) $nanoCredit->montant);
        $nbEcheances = $amortissement['nombre_echeances'];
        $montantEcheance = $amortissement['montant_echeance'];
        $frequence = $palier->frequence_remboursement;

        $dateBase = Carbon::parse($nanoCredit->date_octroi);

        for ($i = 1; $i <= $nbEcheances; $i++) {
            $dateEcheance = match ($frequence) {
                'journalier' => $dateBase->copy()->addDays($i),
                'hebdomadaire' => $dateBase->copy()->addWeeks($i),
                'mensuel' => $dateBase->copy()->addMonths($i),
                'trimestriel' => $dateBase->copy()->addMonths($i * 3),
                default => $dateBase->copy()->addMonths($i),
            };

            NanoCreditEcheance::create([
                'nano_credit_id' => $nanoCredit->id,
                'numero_echeance' => $i,
                'date_echeance' => $dateEcheance->toDateString(),
                'montant_du' => $montantEcheance,
                'montant_paye' => 0,
                'statut' => 'a_venir',
            ]);
        }
    }

    /**
     * Crée et associe les comptes de gestion (Crédit, Impayés) et lie le compte courant de remboursement.
     */
    private function associerComptesFinanciers(NanoCredit $nanoCredit): void
    {
        $membre = $nanoCredit->membre;

        // A. Compte de remboursement (Premier compte Courant du client)
        $compteCourant = $membre->compteCourant;
        
        // B. Création du compte de Crédit (Dette principale)
        $compteCredit = Caisse::create([
            'membre_id'    => $membre->id,
            'nom'          => 'Compte Crédit (#' . $nanoCredit->id . ') - ' . $membre->nom_complet,
            'numero'       => $this->generateNumeroCaisse(),
            'solde_init'   => 0,
            'solde_actuel' => (float) $nanoCredit->montant, // On commence avec le montant dû
            'type'         => 'credit',
            'actif'        => true,
        ]);

        // C. Création du compte des Impayés
        $compteImpaye = Caisse::create([
            'membre_id'    => $membre->id,
            'nom'          => 'Compte Impayés (#' . $nanoCredit->id . ') - ' . $membre->nom_complet,
            'numero'       => $this->generateNumeroCaisse(),
            'solde_init'   => 0,
            'solde_actuel' => 0,
            'type'         => 'impayes',
            'actif'        => true,
        ]);

        // D. Liaison finale au dossier nano-crédit
        $nanoCredit->update([
            'compte_remboursement_id' => $compteCourant?->id,
            'compte_credit_id'        => $compteCredit->id,
            'compte_impaye_id'        => $compteImpaye->id,
        ]);
    }

    /**
     * Générer un numéro de compte unique
     */
    private function generateNumeroCaisse(): string
    {
        do {
            $part1 = strtoupper(Str::random(4));
            $part2 = strtoupper(Str::random(4));
            $numero = $part1 . '-' . $part2;
        } while (Caisse::where('numero', $numero)->exists());

        return $numero;
    }
}
