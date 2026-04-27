<?php

namespace App\Services;

use App\Models\Caisse;
use App\Models\MouvementCaisse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinanceService
{
    /**
     * Enregistre une écriture comptable balancée (Partie double)
     * Pour chaque mouvement, 'entree' = Débit, 'sortie' = Crédit.
     * La somme des montants en 'entree' doit égaler la somme des montants en 'sortie'.
     *
     * @param array $entries Liste des mouvements [caisse_id, type, sens, montant, libelle, notes, reference_type, reference_id]
     */
    public function recordDoubleEntry(array $entries): void
    {
        DB::transaction(function () use ($entries) {
            $totalIn = 0;
            $totalOut = 0;

            foreach ($entries as $entry) {
                if ($entry['sens'] === 'entree') $totalIn += (float) $entry['montant'];
                else $totalOut += (float) $entry['montant'];

                MouvementCaisse::create($entry);
            }

            // Vérification de l'équilibre (Tolérance 0.01 pour les arrondis)
            if (abs($totalIn - $totalOut) > 0.01) {
                Log::error("FinanceService: Déséquilibre détecté lors d'une écriture", ['in' => $totalIn, 'out' => $totalOut, 'entries' => $entries]);
                throw new \Exception("Écriture comptable déséquilibrée : Différence de " . ($totalIn - $totalOut));
            }
        });
    }

    /**
     * Logique financière d'octroi de nano-credit (Deblocage)
     * - Débit : Compte Système Global (SYS-NAN-CRD)
     * - Crédit : Compte Crédit du Client
     * (Éventuellement Cash/Wallet s'ils sont impliqués)
     */
    public function logNanoCreditOctroi(\App\Models\NanoCredit $nc): void
    {
        $palier = $nc->palier;
        if (!$palier) return;

        $amort = $palier->calculAmortissement((float) $nc->montant);
        $totalDu = (float) $amort['montant_total_du'];
        $principal = (float) $amort['montant_emprunte'];

        $caisseGlobal = Caisse::getCaisseNanoCredit();
        if (!$caisseGlobal || !$nc->compte_credit_id) {
            Log::warning("FinanceService: Comptes manquants pour octroi nano-credit #{$nc->id}");
            return;
        }

        $this->recordDoubleEntry([
            // 1. Débit Asset Système (On augmente notre créance globale)
            [
                'caisse_id'      => $caisseGlobal->id,
                'type'           => 'deboursement_credit',
                'sens'           => 'entree', // DEBIT
                'montant'        => $totalDu,
                'date_operation' => now(),
                'libelle'        => 'DEBIT SYSTÈME NANO: #' . $nc->id,
                'reference_type' => \App\Models\NanoCredit::class,
                'reference_id'   => $nc->id,
            ],
            // 2. Crédit Compte Client (Initialisation de sa dette)
            [
                'caisse_id'      => $nc->compte_credit_id,
                'type'           => 'deboursement_credit',
                'sens'           => 'sortie', // CREDIT
                'montant'        => $totalDu,
                'date_operation' => now(),
                'libelle'        => 'Octroi Nano-crédit #' . $nc->id,
                'reference_type' => \App\Models\NanoCredit::class,
                'reference_id'   => $nc->id,
            ]
        ]);

        // Optionnel : Si le compte de remboursement (courant) reçoit les fonds, on ajoute une écriture Cash/Liquide
        if ($nc->compte_remboursement_id) {
            $caisseGlobal = Caisse::getCaisseNanoCredit();
            // Note: Ceci est un transfert de liquidité séparé du mouvement de créance ci-dessus
            // Normalement, ça devrait être : Debit Compte Courant / Credit Wallet Global
            $this->recordDoubleEntry([
                [
                    'caisse_id'      => $nc->compte_remboursement_id,
                    'type'           => 'deboursement_credit_fonds',
                    'sens'           => 'entree', // DEBIT (Increases balance)
                    'montant'        => $principal,
                    'date_operation' => now(),
                    'libelle'        => 'Fonds reçus Nano-crédit #' . $nc->id,
                    'reference_type' => \App\Models\NanoCredit::class,
                    'reference_id'   => $nc->id,
                ],
                [
                    'caisse_id'      => $caisseGlobal ? $caisseGlobal->id : $nc->compte_credit_id, // Fallback
                    'type'           => 'deboursement_credit_fonds',
                    'sens'           => 'sortie', // CREDIT (Decreases liquidity)
                    'montant'        => $principal,
                    'date_operation' => now(),
                    'libelle'        => 'GLOBAL - Sortie fonds Nano-crédit #' . $nc->id,
                    'reference_type' => \App\Models\NanoCredit::class,
                    'reference_id'   => $nc->id,
                ]
            ]);
        }
    }

    /**
     * Logique de remboursement de nano-credit
     * - Débit : Compte Crédit Client (Diminution dette)
     * - Crédit : Compte Système Global (Diminution créance)
     * - Crédit : Compte Produits (Intérêts gagnés)
     */
    public function logNanoCreditRemboursement(\App\Models\NanoCreditVersement $versement): void
    {
        $nc = $versement->nanoCredit;
        $palier = $nc->palier;
        if (!$palier) return;

        $amountTotal = (float) $versement->montant;
        
        // On calcule la part d'intérêt prorata ou fixe
        $decomp = $palier->decomposeEcheance((float) $nc->getRawOriginal('montant'));
        $interestPerEcheance = (float) $decomp['interet_unitaire'];
        $capitalPerEcheance = (float) $decomp['capital_unitaire'];
        
        // Ratio pour ventiler si le montant ne correspond pas exactement à une échéance
        $ratio = $amountTotal / ($interestPerEcheance + $capitalPerEcheance);
        $interestPart = (float) round($interestPerEcheance * $ratio, 2);
        $capitalPart = $amountTotal - $interestPart;

        $caisseGlobal = Caisse::getCaisseNanoCredit();
        $caisseProd = Caisse::getCaisseProduit();

        $entries = [
            // 1. Débit Compte Client (Réduit sa dette)
            [
                'caisse_id'      => $nc->compte_credit_id,
                'type'           => 'remboursement_nano',
                'sens'           => 'entree', // DEBIT (augmente balance car balance est négative)
                'montant'        => $amountTotal,
                'date_operation' => $versement->date_versement,
                'libelle'        => 'Remboursement Nano-crédit #' . $nc->id,
                'reference_type' => \App\Models\NanoCreditVersement::class,
                'reference_id'   => $versement->id,
            ],
            // 2. Crédit Système Nano (Réduit l'asset créance)
            [
                'caisse_id'      => $caisseGlobal ? $caisseGlobal->id : null,
                'type'           => 'remboursement_nano',
                'sens'           => 'sortie', // CREDIT
                'montant'        => $capitalPart,
                'date_operation' => $versement->date_versement,
                'libelle'        => 'Retour Capital Nano #' . $nc->id,
                'reference_type' => \App\Models\NanoCreditVersement::class,
                'reference_id'   => $versement->id,
            ],
            // 3. Crédit Produit (Reconnaissance de revenu)
            [
                'caisse_id'      => $caisseProd ? $caisseProd->id : null,
                'type'           => 'produit_interet',
                'sens'           => 'sortie', // CREDIT (augmente profit)
                'montant'        => $interestPart,
                'date_operation' => $versement->date_versement,
                'libelle'        => 'Intérêts perçus Nano #' . $nc->id,
                'reference_type' => \App\Models\NanoCreditVersement::class,
                'reference_id'   => $versement->id,
            ]
        ];

        // Filtrer les entrées sans caisse (au cas où les comptes système manquent)
        $cleanEntries = array_filter($entries, fn($e) => $e['caisse_id'] !== null);
        
        $this->recordDoubleEntry($cleanEntries);
    }

    /**
     * Paiement de commission de parrainage
     * - Double écriture balancée :
     *   1. Flux Sortant : Débit Charge / Crédit Caisse Parrainage
     *   2. Flux Beneficiaire : Débit Caisse Parrainage / Crédit Compte Client
     */
    public function logParrainagePaiement(\App\Models\ParrainageCommission $commission): void
    {
        $caisseCharge = Caisse::getCaisseCharge();
        $caissePar = Caisse::getCaisseParrainage();
        $compteCourant = $commission->parrain->compteCourant;

        if (!$caisseCharge || !$caissePar || !$compteCourant) return;

        $amount = (float) $commission->montant;
        $refType = \App\Models\ParrainageCommission::class;
        $refId = $commission->id;

        $this->recordDoubleEntry([
            // 1. Débit Compte de charge (On dépense de l'argent)
            [
                'caisse_id'      => $caisseCharge->id,
                'type'           => 'charge_parrainage',
                'sens'           => 'entree', // DEBIT 
                'montant'        => $amount,
                'date_operation' => now(),
                'libelle'        => 'CHARGE GAIN PARRAINAGE: Com #' . $refId,
                'reference_type' => $refType,
                'reference_id'   => $refId,
            ],
            // 2. Crédit Système Parrainage (Sortie physique du pool)
            [
                'caisse_id'      => $caissePar->id,
                'type'           => 'charge_parrainage',
                'sens'           => 'sortie', // CREDIT
                'montant'        => $amount,
                'date_operation' => now(),
                'libelle'        => 'Sortie pool Parrainage #' . $refId,
                'reference_type' => $refType,
                'reference_id'   => $refId,
            ],
            // 3. Débit Système Parrainage (Transit vers client)
            [
                'caisse_id'      => $caissePar->id,
                'type'           => 'gain_parrainage',
                'sens'           => 'entree', // DEBIT
                'montant'        => $amount,
                'date_operation' => now(),
                'libelle'        => 'Distribution gain parrainage #' . $refId,
                'reference_type' => $refType,
                'reference_id'   => $refId,
            ],
            // 4. Crédit Compte Courant Parrain (Reçoit les fonds sur son balance)
            [
                'caisse_id'      => $compteCourant->id,
                'type'           => 'gain_parrainage',
                'sens'           => 'sortie', // CREDIT increases client balance (Liability for us)
                'montant'        => $amount,
                'date_operation' => now(),
                'libelle'        => 'Commission de parrainage - Filleul: ' . ($commission->filleul->nom_complet ?? 'N/A'),
                'reference_type' => $refType,
                'reference_id'   => $refId,
            ]
        ]);
    }
    /**
     * Enregistrement générique équilibré entre deux comptes
     */
    public function logGenericBalancedEntry(
        \App\Models\Caisse $caisseDebit,
        \App\Models\Caisse $caisseCredit,
        float $amount,
        string $typeOperation,
        string $libelle,
        $reference = null,
        ?string $notes = null
    ): void {
        $this->recordDoubleEntry([
            [
                'caisse_id'      => $caisseDebit->id,
                'type'           => $typeOperation,
                'sens'           => 'entree', // DEBIT
                'montant'        => $amount,
                'date_operation' => now(),
                'libelle'        => $libelle,
                'notes'          => $notes,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id'   => $reference ? $reference->id : null,
            ],
            [
                'caisse_id'      => $caisseCredit->id,
                'type'           => $typeOperation,
                'sens'           => 'sortie', // CREDIT
                'montant'        => $amount,
                'date_operation' => now(),
                'libelle'        => $libelle,
                'notes'          => $notes,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id'   => $reference ? $reference->id : null,
            ]
        ]);
    }

    /**
     * Logique de remboursement (Annulation de versement / Trop-perçu)
     * Inverse de logFluxTontineCagnotte
     * - Débit : Compte individuel du membre (Réduction de son avoir)
     * - Crédit : Compte Système Global (Sortie physique des fonds)
     */
    public function logFluxRemboursement(\App\Models\Caisse $compteMembre, float $amount, string $typeOperation, string $libelle, $reference = null): void
    {
        $caisseGlobal = null;
        
        if ($compteMembre->type === 'tontine') {
            $caisseGlobal = Caisse::getCaisseTontineCli();
        } elseif ($compteMembre->type === 'cagnotte') {
            $caisseGlobal = Caisse::getCaisseCagnottePub(); 
        } elseif ($compteMembre->type === 'epargne') {
            $caisseGlobal = Caisse::getCaisseEpargneLibre();
        }

        if (!$caisseGlobal) {
            Log::warning("FinanceService: Compte global introuvable pour le remboursement {$compteMembre->type}");
            return;
        }

        $this->recordDoubleEntry([
            // 1. Débit Compte Membre (On lui "reprend" l'argent de son solde virtuel)
            [
                'caisse_id'      => $compteMembre->id,
                'type'           => $typeOperation,
                'sens'           => 'entree', // DEBIT 
                'montant'        => $amount,
                'date_operation' => now(),
                'libelle'        => $libelle,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id'   => $reference ? $reference->id : null,
            ],
            // 2. Crédit Système (Sortie physique de l'argent du pool)
            [
                'caisse_id'      => $caisseGlobal->id,
                'type'           => $typeOperation,
                'sens'           => 'sortie', // CREDIT
                'montant'        => $amount,
                'date_operation' => now(),
                'libelle'        => 'GLOBAL - ' . $libelle,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id'   => $reference ? $reference->id : null,
            ]
        ]);
    }

    /**
     * Logique de réception de fonds (Tontines / Cagnottes)
     * - Débit : Compte Système Global (SYS-TON-CLI / SYS-CAG-PUB/PRV)
     * - Crédit : Compte individuel de la tontine/cagnotte du membre
     */
    public function logFluxTontineCagnotte(\App\Models\Caisse $compteMembre, float $amount, string $typeOperation, string $libelle, $reference = null): void
    {
        $caisseGlobal = null;
        
        // Détermination du compte global selon le type de compte membre
        if ($compteMembre->type === 'tontine') {
            $caisseGlobal = Caisse::getCaisseTontineCli();
        } elseif ($compteMembre->type === 'cagnotte') {
            // Pour les cagnottes, on pourrait affiner selon visibilité, mais par défaut on prend le global
            $caisseGlobal = Caisse::getCaisseCagnottePub(); 
        } elseif ($compteMembre->type === 'epargne') {
            $caisseGlobal = Caisse::getCaisseEpargneLibre();
        }

        if (!$caisseGlobal) {
            Log::warning("FinanceService: Compte global introuvable pour le type {$compteMembre->type}");
            // On peut quand même enregistrer le mouvement sur le compte membre
            $compteMembre->mouvements()->create([
                'type' => $typeOperation,
                'sens' => 'entree',
                'montant' => $amount,
                'date_operation' => now(),
                'libelle' => $libelle
            ]);
            return;
        }

        $this->recordDoubleEntry([
            // 1. Débit Système (On reçoit physiquement l'argent dans le pool)
            [
                'caisse_id'      => $caisseGlobal->id,
                'type'           => $typeOperation,
                'sens'           => 'entree', // DEBIT increases asset
                'montant'        => $amount,
                'date_operation' => now(),
                'libelle'        => 'GLOBAL - ' . $libelle,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id'   => $reference ? $reference->id : null,
            ],
            // 2. Crédit Compte Membre (On lui doit cet argent : Passif)
            [
                'caisse_id'      => $compteMembre->id,
                'type'           => $typeOperation,
                'sens'           => 'sortie', // CREDIT increases liability (logic user: balance increases by sortie?)
                // Note: Si le solde est solde + entree - sortie, alors sortie diminue le solde.
                // Le user a dit : "credit du compte client". 
                // Pour que le solde client AUGMENTE avec un crédit, il faut que sa balance soit négative
                // OU que la formule du solde change.
                'montant'        => $amount,
                'date_operation' => now(),
                'libelle'        => $libelle,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id'   => $reference ? $reference->id : null,
            ]
        ]);
    }
}
