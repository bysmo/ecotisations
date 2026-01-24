<?php

namespace Database\Seeders;

use App\Models\Caisse;
use App\Models\SortieCaisse;
use App\Models\MouvementCaisse;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SortieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caisses = Caisse::where('statut', 'active')->get();
        
        if ($caisses->isEmpty()) {
            $this->command->warn('Aucune caisse active trouvée. Veuillez d\'abord exécuter CaisseSeeder et ApprovisionnementSeeder.');
            return;
        }

        $this->command->info('Création des sorties de caisses...');

        $motifs = [
            'Achat de matériel',
            'Frais de fonctionnement',
            'Paiement de facture',
            'Dépense administrative',
            'Achat fournitures',
            'Frais de transport',
            'Maintenance équipement',
            'Frais de communication',
            'Dépense événementielle',
            'Frais de formation',
            'Achat de services',
            'Dépense urgente',
            'Frais de location',
            'Achat de logiciel',
            'Dépense marketing',
        ];

        $notes = [
            'Dépense approuvée',
            'Facture n°',
            'Bon de commande',
            'Urgent',
            'Budget alloué',
            null,
            null,
            null,
        ];

        $created = 0;
        $nbSorties = 50; // Générer 50 sorties

        for ($i = 0; $i < $nbSorties; $i++) {
            // Rafraîchir les caisses pour avoir les soldes à jour
            $caisses = $caisses->fresh();
            
            // Sélectionner une caisse aléatoire avec des fonds
            $caisse = $caisses->where('solde_actuel', '>', 10000)->random();
            
            if (!$caisse || $caisse->solde_actuel < 10000) {
                continue;
            }
            
            $montant = rand(5000, min(150000, (int)$caisse->solde_actuel));
            $motif = $motifs[array_rand($motifs)];
            $note = $notes[array_rand($notes)];
            if ($note && strpos($note, 'n°') !== false) {
                $note = $note . ' ' . rand(1000, 9999);
            }
            
            $joursAgo = rand(1, 60);
            $dateSortie = Carbon::now()->subDays($joursAgo);
            
            // Créer la sortie
            $sortie = SortieCaisse::create([
                'caisse_id' => $caisse->id,
                'montant' => $montant,
                'motif' => $motif,
                'notes' => $note,
                'date_sortie' => $dateSortie,
                'created_at' => $dateSortie,
                'updated_at' => $dateSortie,
            ]);

            // Mettre à jour le solde de la caisse
            $caisse->solde_initial -= $montant;
            $caisse->save();

            // Créer le mouvement de caisse
            MouvementCaisse::create([
                'caisse_id' => $caisse->id,
                'type' => 'sortie',
                'sens' => 'sortie',
                'montant' => $montant,
                'date_operation' => $dateSortie,
                'libelle' => 'Sortie de caisse' . ($motif ? ' - ' . $motif : ''),
                'notes' => $note,
                'reference_type' => SortieCaisse::class,
                'reference_id' => $sortie->id,
            ]);

            $created++;
        }

        $this->command->info("{$created} sortie(s) créée(s) avec succès.");
    }
}
