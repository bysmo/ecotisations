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

        $montant = (int) $nanoCredit->montant;

        // --- CAS PI-SPI ---
        if ($withdrawMode === 'pispi') {
            try {
                $piSpi = app(\App\Services\PiSpiService::class);
            } catch (\Exception $e) {
                return ['success' => false, 'message' => 'Pi-SPI n\'est pas configuré ou activé.'];
            }

            $alias = $nanoCredit->membre->defaultWalletAlias();
            if (!$alias) {
                return ['success' => false, 'message' => 'Le membre n\'a aucun alias Pi-SPI configuré pour recevoir les fonds.'];
            }

            $txId = 'NANO-' . $nanoCredit->id . '-' . time();
            $result = $piSpi->sendPayment($txId, $alias->alias, $montant, 'nano_credit');

            if (!$result['success']) {
                $nanoCredit->update(['error_message' => $result['message']]);
                return ['success' => false, 'message' => 'Échec de l\'envoi Pi-SPI : ' . $result['message']];
            }

            // Mise à jour immédiate car Pi-SPI B2P est généralement synchrone (ou géré via webhook/polling)
            $dateOctroi = now()->toDateString();
            $palier = $nanoCredit->palier;
            $dateFinRemb = $palier ? Carbon::parse($dateOctroi)->addDays((int) $palier->duree_jours)->toDateString() : null;

            $nanoCredit->update([
                'statut' => 'debourse',
                'date_octroi' => $dateOctroi,
                'date_fin_remboursement' => $dateFinRemb,
                'transaction_id' => $txId,
                'provider_ref' => $result['data']['reference'] ?? null,
                'withdraw_mode' => 'pispi',
                'telephone' => $alias->alias, // On stocke l'alias dans le champ téléphone pour la traçabilité
            ]);

            // Finalisation financière
            $this->finaliserFinancesDeboursement($nanoCredit);

            return ['success' => true, 'message' => 'Fonds envoyés avec succès via Pi-SPI.'];
        }

        // --- CAS PAYDUNYA ---
        try {
            $paydunya = app(PayDunyaService::class);
        } catch (\Exception $e) {
            Log::error('NanoCreditService debourser: PayDunya non configuré', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'PayDunya n\'est pas configuré.'];
        }

        $callbackUrl = url()->route('paydunya.disburse.callback');

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
            // Cas particulier : La facture a déjà été soumise (ex: timeout ou double clic)
            $isAlreadySubmitted = str_contains(strtolower($submit['message'] ?? ''), 'already submitted');
            
            if ($isAlreadySubmitted) {
                Log::info('NanoCreditService: Facture déjà soumise pour #' . $nanoCredit->id . '. Vérification du statut...');
                $verify = $paydunya->checkDisburseStatus($result['disburse_token']);
                
                if ($verify['success'] && in_array(strtolower($verify['status']), ['success', 'pending', 'completed'])) {
                    $submit = [
                        'success' => true,
                        'status' => $verify['status'],
                        'transaction_id' => $verify['transaction_id'] ?? null,
                    ];
                } else {
                    $nanoCredit->update(['error_message' => $submit['message']]);
                    return ['success' => false, 'message' => $submit['message']];
                }
            } else {
                $nanoCredit->update(['error_message' => $submit['message'] ?? 'Erreur à la soumission']);
                return ['success' => false, 'message' => $submit['message'] ?? 'Soumission échouée.'];
            }
        }

        // 4. Finaliser localement
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

        $this->finaliserFinancesDeboursement($nanoCredit);

        return ['success' => true, 'message' => 'Déboursement PayDunya initié.'];
    }

    /**
     * Centralise les écritures comptables du déboursement
     */
    private function finaliserFinancesDeboursement(NanoCredit $nanoCredit)
    {
        $palier = $nanoCredit->palier;

        // 4.5. Automatisation des comptes liés au crédit
        $this->associerComptesFinanciers($nanoCredit);

        // 4.6. Écritures comptables de décaissement (Double Entrée via FinanceService)
        app(\App\Services\FinanceService::class)->logNanoCreditOctroi($nanoCredit);

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
                'date_echeance' => $dateEcheance->toDateString(),
                'montant' => $montantEcheance,
                'statut' => 'en_attente',
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
            'numero'       => Caisse::generateNumeroCompte(),
            'solde_initial'   => 0,
            'statut'       => 'active',
            'type'         => 'credit',
        ]);

        // C. Création du compte des Impayés
        $compteImpaye = Caisse::create([
            'membre_id'    => $membre->id,
            'nom'          => 'Compte Impayés (#' . $nanoCredit->id . ') - ' . $membre->nom_complet,
            'numero'       => Caisse::generateNumeroCompte(),
            'solde_initial'   => 0,
            'statut'       => 'active',
            'type'         => 'impayes',
        ]);

        // D. Liaison finale au dossier nano-crédit
        $nanoCredit->update([
            'compte_remboursement_id' => $compteCourant?->id,
            'compte_credit_id'        => $compteCredit->id,
            'compte_impaye_id'        => $compteImpaye->id,
        ]);
    }
}
