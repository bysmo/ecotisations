<?php

namespace Database\Seeders;

use App\Models\Membre;
use Illuminate\Database\Seeder;

class SegmentMembreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Assignation des segments aux membres...');

        // Segments disponibles avec répartition cible
        $segments = [
            'VIP' => 0.15,        // 15% des membres
            'Premium' => 0.20,    // 20% des membres
            'Standard' => 0.25,   // 25% des membres
            'Basique' => 0.15,    // 15% des membres
            'Entreprise' => 0.10, // 10% des membres
            // 15% restants sans segment
        ];

        // Récupérer tous les membres
        $membres = Membre::all();
        $totalMembres = $membres->count();

        if ($totalMembres === 0) {
            $this->command->warn('Aucun membre trouvé. Veuillez d\'abord exécuter MembreSeeder.');
            return;
        }

        // Calculer le nombre de membres par segment
        $repartition = [];
        $totalAlloue = 0;
        foreach ($segments as $segment => $pourcentage) {
            $repartition[$segment] = (int) round($totalMembres * $pourcentage);
            $totalAlloue += $repartition[$segment];
        }
        
        // Ajuster pour que le total soit égal au nombre de membres (arrondi)
        $diff = $totalMembres - $totalAlloue;
        if ($diff !== 0) {
            // Ajuster le segment Standard (le plus grand) pour compenser
            $repartition['Standard'] += $diff;
        }

        // Mélanger les membres pour une distribution aléatoire
        $membres = $membres->shuffle();
        
        $index = 0;
        $stats = [];

        // Assigner les segments
        foreach ($repartition as $segment => $nombre) {
            $stats[$segment] = 0;
            for ($i = 0; $i < $nombre && $index < $totalMembres; $i++) {
                $membre = $membres[$index];
                $membre->segment = $segment;
                $membre->save();
                $stats[$segment]++;
                $index++;
            }
        }

        // Les membres restants n'ont pas de segment
        $sansSegment = 0;
        while ($index < $totalMembres) {
            $membre = $membres[$index];
            $membre->segment = null;
            $membre->save();
            $sansSegment++;
            $index++;
        }

        // Afficher les statistiques
        $this->command->info("Assignation terminée pour {$totalMembres} membre(s).");
        $this->command->info("Répartition par segment :");
        foreach ($stats as $segment => $nombre) {
            $pourcentage = $totalMembres > 0 ? round(($nombre / $totalMembres) * 100, 1) : 0;
            $this->command->info("  - {$segment}: {$nombre} membre(s) ({$pourcentage}%)");
        }
        if ($sansSegment > 0) {
            $pourcentage = round(($sansSegment / $totalMembres) * 100, 1);
            $this->command->info("  - Sans segment: {$sansSegment} membre(s) ({$pourcentage}%)");
        }

        // Afficher quelques exemples de membres par segment pour les tests
        $this->command->info("\nExemples de membres par segment (pour tests) :");
        foreach ($segments as $segment => $pourcentage) {
            $exemple = Membre::where('segment', $segment)->first();
            if ($exemple) {
                $this->command->info("  - {$segment}: {$exemple->numero} ({$exemple->nom_complet})");
            }
        }
    }
}
