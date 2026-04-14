<?php

namespace Database\Seeders;

use App\Models\Membre;
use App\Models\Cotisation;
use Illuminate\Database\Seeder;

class UpdateSegmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Mise à jour des segments des clients et cotisations...');

        // Segments disponibles
        $segments = ['VIP', 'Premium', 'Standard', 'Basique', 'Entreprise'];
        
        // 1. Mettre à jour les segments des clients
        $clients = Membre::all();
        $totalClients = $clients->count();

        if ($totalClients === 0) {
            $this->command->warn('Aucun client trouvé.');
            return;
        }

        // Répartition des clients par segment
        $repartition = [
            'VIP' => (int) round($totalClients * 0.15),
            'Premium' => (int) round($totalClients * 0.20),
            'Standard' => (int) round($totalClients * 0.25),
            'Basique' => (int) round($totalClients * 0.15),
            'Entreprise' => (int) round($totalClients * 0.10),
        ];
        
        // Ajuster pour que le total soit égal au nombre de clients
        $totalAlloue = array_sum($repartition);
        $diff = $totalClients - $totalAlloue;
        if ($diff !== 0) {
            $repartition['Standard'] += $diff;
        }

        // Mélanger les clients
        $clients = $clients->shuffle();
        
        $index = 0;
        $statsClients = [];

        // Assigner les segments aux clients
        foreach ($repartition as $segment => $nombre) {
            $statsClients[$segment] = 0;
            for ($i = 0; $i < $nombre && $index < $totalClients; $i++) {
                $client = $clients[$index];
                $client->segment = $segment;
                $client->save();
                $statsClients[$segment]++;
                $index++;
            }
        }

        // Les clients restants n'ont pas de segment
        $sansSegment = 0;
        while ($index < $totalClients) {
            $client = $clients[$index];
            $client->segment = null;
            $client->save();
            $sansSegment++;
            $index++;
        }

        $this->command->info("Segments des clients mis à jour.");
        $this->command->info("Répartition par segment :");
        foreach ($statsClients as $segment => $nombre) {
            $pourcentage = $totalClients > 0 ? round(($nombre / $totalClients) * 100, 1) : 0;
            $this->command->info("  - {$segment}: {$nombre} client(s) ({$pourcentage}%)");
        }
        if ($sansSegment > 0) {
            $pourcentage = round(($sansSegment / $totalClients) * 100, 1);
            $this->command->info("  - Sans segment: {$sansSegment} client(s) ({$pourcentage}%)");
        }

        // 2. Mettre à jour les cotisations
        $cotisations = Cotisation::all();
        $totalCotisations = $cotisations->count();

        if ($totalCotisations === 0) {
            $this->command->warn('Aucune cotisation trouvée.');
            return;
        }

        // Répartition des cotisations par segment
        // On veut exactement 3 cotisations VIP
        $repartitionCotisations = [
            'VIP' => 3,
            'Premium' => (int) round($totalCotisations * 0.20),
            'Standard' => (int) round($totalCotisations * 0.25),
            'Basique' => (int) round($totalCotisations * 0.15),
            'Entreprise' => (int) round($totalCotisations * 0.10),
        ];
        
        // Le reste sera réparti dans les autres segments
        $totalAlloueCotisations = array_sum($repartitionCotisations);
        $diffCotisations = $totalCotisations - $totalAlloueCotisations;
        if ($diffCotisations !== 0) {
            $repartitionCotisations['Standard'] += $diffCotisations;
        }

        // Mélanger les cotisations
        $cotisations = $cotisations->shuffle();
        
        $indexCotisations = 0;
        $statsCotisations = [];

        // Assigner les segments aux cotisations
        foreach ($repartitionCotisations as $segment => $nombre) {
            $statsCotisations[$segment] = 0;
            for ($i = 0; $i < $nombre && $indexCotisations < $totalCotisations; $i++) {
                $cotisation = $cotisations[$indexCotisations];
                $cotisation->segment = $segment;
                $cotisation->save();
                $statsCotisations[$segment]++;
                $indexCotisations++;
            }
        }

        // S'assurer qu'il n'y a pas de cotisations sans segment
        // Les cotisations restantes auront le segment Standard
        while ($indexCotisations < $totalCotisations) {
            $cotisation = $cotisations[$indexCotisations];
            $cotisation->segment = 'Standard';
            $cotisation->save();
            $statsCotisations['Standard']++;
            $indexCotisations++;
        }

        // Vérifier qu'il n'y a plus de cotisations sans segment
        $cotisationsSansSegment = Cotisation::where(function($q) {
            $q->whereNull('segment')
              ->orWhere('segment', '');
        })->count();

        if ($cotisationsSansSegment > 0) {
            // Mettre à jour les cotisations restantes
            Cotisation::where(function($q) {
                $q->whereNull('segment')
                  ->orWhere('segment', '');
            })->update(['segment' => 'Standard']);
            $statsCotisations['Standard'] += $cotisationsSansSegment;
        }

        $this->command->info("\nSegments des cotisations mis à jour.");
        $this->command->info("Répartition par segment :");
        foreach ($statsCotisations as $segment => $nombre) {
            $pourcentage = $totalCotisations > 0 ? round(($nombre / $totalCotisations) * 100, 1) : 0;
            $this->command->info("  - {$segment}: {$nombre} cotisation(s) ({$pourcentage}%)");
        }

        // Vérification finale
        $cotisationsVIP = Cotisation::where('segment', 'VIP')->where('actif', true)->count();
        $this->command->info("\n✓ Vérification : {$cotisationsVIP} cotisation(s) VIP active(s)");

        // Afficher un exemple de client VIP pour les tests
        $clientVIP = Membre::where('segment', 'VIP')->first();
        if ($clientVIP) {
            $this->command->info("\nExemple de client VIP pour test :");
            $this->command->info("  - Numéro: {$clientVIP->numero}");
            $this->command->info("  - Nom: {$clientVIP->nom_complet}");
            $this->command->info("  - Segment: {$clientVIP->segment}");
        }
    }
}
