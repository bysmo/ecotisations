<?php

namespace Database\Seeders;

use App\Models\Caisse;
use App\Models\Transfert;
use App\Models\MouvementCaisse;
use Illuminate\Database\Seeder;

class TransfertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer toutes les caisses actives
        $caisses = Caisse::where('statut', 'active')->get();
        
        if ($caisses->count() < 2) {
            $this->command->warn('Il faut au moins 2 comptes actifs pour créer des transferts.');
            return;
        }
        
        // Rafraîchir les caisses pour avoir les soldes à jour après les approvisionnements
        $caisses = $caisses->fresh();
        
        $this->command->info('Création des transferts entre comptes...');
        
        // Créer des transferts entre les caisses
        $transferts = [
            [
                'caisse_source' => 'Compte Principal',
                'caisse_destination' => 'Compte Événements',
                'montant' => 50000,
                'motif' => 'Transfert pour organisation d\'événement',
            ],
            [
                'caisse_source' => 'Compte Principal',
                'caisse_destination' => 'Compte Projets',
                'montant' => 100000,
                'motif' => 'Financement de projet',
            ],
            [
                'caisse_source' => 'Compte Événements',
                'caisse_destination' => 'Compte Sociale',
                'montant' => 30000,
                'motif' => 'Transfert pour actions sociales',
            ],
            [
                'caisse_source' => 'Compte Projets',
                'caisse_destination' => 'Compte Formation',
                'montant' => 75000,
                'motif' => 'Financement de formation',
            ],
            [
                'caisse_source' => 'Compte Principal',
                'caisse_destination' => 'Compte Infrastructure',
                'montant' => 200000,
                'motif' => 'Investissement infrastructure',
            ],
            [
                'caisse_source' => 'Compte Sociale',
                'caisse_destination' => 'Compte Urgences',
                'montant' => 25000,
                'motif' => 'Alimentation compte d\'urgence',
            ],
            [
                'caisse_source' => 'Compte Communication',
                'caisse_destination' => 'Compte Événements',
                'montant' => 40000,
                'motif' => 'Support événementiel',
            ],
            [
                'caisse_source' => 'Compte Principal',
                'caisse_destination' => 'Compte Innovation',
                'montant' => 150000,
                'motif' => 'Financement innovation',
            ],
        ];
        
        $created = 0;
        
        foreach ($transferts as $transfertData) {
            $caisseSource = $caisses->firstWhere('nom', $transfertData['caisse_source']);
            $caisseDestination = $caisses->firstWhere('nom', $transfertData['caisse_destination']);
            
            if (!$caisseSource || !$caisseDestination) {
                continue;
            }
            
            // Vérifier que la caisse source a suffisamment de fonds
            if ($caisseSource->solde_actuel < $transfertData['montant']) {
                continue;
            }
            
            // Générer une date aléatoire pour le transfert
            $dateOperation = now()->subDays(rand(1, 30));
            
            // Créer le transfert
            $transfert = Transfert::create([
                'caisse_source_id' => $caisseSource->id,
                'caisse_destination_id' => $caisseDestination->id,
                'montant' => $transfertData['montant'],
                'motif' => $transfertData['motif'],
                'created_at' => $dateOperation,
                'updated_at' => $dateOperation,
            ]);
            
            // Mettre à jour les soldes des caisses
            $caisseSource->solde_initial -= $transfertData['montant'];
            $caisseSource->save();
            
            $caisseDestination->solde_initial += $transfertData['montant'];
            $caisseDestination->save();
            
            // Créer les mouvements de caisse
            MouvementCaisse::create([
                'caisse_id' => $caisseSource->id,
                'type' => 'transfert_out',
                'sens' => 'sortie',
                'montant' => $transfertData['montant'],
                'date_operation' => $dateOperation,
                'libelle' => 'Transfert vers: ' . $caisseDestination->nom,
                'notes' => $transfertData['motif'],
                'reference_type' => Transfert::class,
                'reference_id' => $transfert->id,
            ]);
            
            MouvementCaisse::create([
                'caisse_id' => $caisseDestination->id,
                'type' => 'transfert_in',
                'sens' => 'entree',
                'montant' => $transfertData['montant'],
                'date_operation' => $dateOperation,
                'libelle' => 'Transfert depuis: ' . $caisseSource->nom,
                'notes' => $transfertData['motif'],
                'reference_type' => Transfert::class,
                'reference_id' => $transfert->id,
            ]);
            
            $created++;
        }
        
        // Générer des transferts supplémentaires pour atteindre plus de 15
        $motifs = [
            'Transfert de fonds',
            'Réallocation budgétaire',
            'Financement projet',
            'Transfert opérationnel',
            'Répartition des ressources',
            'Alimentation compte',
            'Transfert stratégique',
            'Rééquilibrage financier',
        ];
        
        $nbTransfertsSupp = max(42, 50 - $created); // S'assurer d'avoir au moins 50 transferts au total
        
        for ($i = 0; $i < $nbTransfertsSupp; $i++) {
            // Rafraîchir les caisses pour avoir les soldes à jour
            $caisses = $caisses->fresh();
            
            if ($caisses->count() < 2) {
                break;
            }
            
            // Sélectionner deux caisses différentes aléatoirement
            $caisseSource = $caisses->random();
            $caisseDestination = $caisses->where('id', '!=', $caisseSource->id)->random();
            
            if (!$caisseSource || !$caisseDestination) {
                continue;
            }
            
            // Vérifier que la caisse source a des fonds
            if ($caisseSource->solde_actuel < 10000) {
                continue;
            }
            
            $montant = rand(10000, min(200000, (int)$caisseSource->solde_actuel));
            $motif = $motifs[array_rand($motifs)];
            $joursAgo = rand(1, 60);
            $dateOperation = now()->subDays($joursAgo);
            
            // Créer le transfert
            $transfert = Transfert::create([
                'caisse_source_id' => $caisseSource->id,
                'caisse_destination_id' => $caisseDestination->id,
                'montant' => $montant,
                'motif' => $motif,
                'created_at' => $dateOperation,
                'updated_at' => $dateOperation,
            ]);
            
            // Mettre à jour les soldes des caisses
            $caisseSource->solde_initial -= $montant;
            $caisseSource->save();
            
            $caisseDestination->solde_initial += $montant;
            $caisseDestination->save();
            
            // Créer les mouvements de caisse
            MouvementCaisse::create([
                'caisse_id' => $caisseSource->id,
                'type' => 'transfert_out',
                'sens' => 'sortie',
                'montant' => $montant,
                'date_operation' => $dateOperation,
                'libelle' => 'Transfert vers: ' . $caisseDestination->nom,
                'notes' => $motif,
                'reference_type' => Transfert::class,
                'reference_id' => $transfert->id,
            ]);
            
            MouvementCaisse::create([
                'caisse_id' => $caisseDestination->id,
                'type' => 'transfert_in',
                'sens' => 'entree',
                'montant' => $montant,
                'date_operation' => $dateOperation,
                'libelle' => 'Transfert depuis: ' . $caisseSource->nom,
                'notes' => $motif,
                'reference_type' => Transfert::class,
                'reference_id' => $transfert->id,
            ]);
            
            $created++;
        }
        
        $this->command->info("{$created} transfert(s) créé(s) avec succès.");
    }
}
