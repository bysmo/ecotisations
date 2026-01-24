<?php

namespace Database\Seeders;

use App\Models\Cotisation;
use App\Models\Caisse;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CotisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caisses = Caisse::where('statut', 'active')->get();
        
        if ($caisses->isEmpty()) {
            $this->command->warn('Aucune caisse active trouvée. Veuillez d\'abord exécuter CaisseSeeder.');
            return;
        }

        $this->command->info('Création des cotisations...');

        $types = ['reguliere', 'ponctuelle', 'exceptionnelle'];
        $frequences = ['mensuelle', 'trimestrielle', 'semestrielle', 'annuelle', 'unique'];
        $typeMontants = ['libre', 'fixe'];

        $nomsCotisations = [
            'Cotisation Mensuelle Standard',
            'Cotisation Trimestrielle',
            'Adhésion Annuelle',
            'Participation Événements',
            'Cotisation Jeunes',
            'Cotisation Seniors',
            'Participation Projets',
            'Fonds Social',
            'Cotisation Familiale',
            'Participation Formation',
            'Cotisation Étudiant',
            'Don Libre',
            'Participation Infrastructure',
            'Cotisation Bienfaiteur',
            'Participation Communication',
            'Fonds d\'Urgence',
            'Cotisation Partenaire',
            'Participation Innovation',
            'Cotisation Membre Actif',
            'Don Exceptionnel',
        ];

        // Récupérer les tags de cotisations
        $tags = Tag::where('type', 'cotisation')->pluck('nom')->toArray();
        
        // Segments disponibles pour les cotisations
        $segments = ['VIP', 'Premium', 'Standard', 'Basique', 'Entreprise', null];
        
        $created = 0;
        $nbCotisations = 50; // Générer 50 cotisations

        for ($i = 0; $i < $nbCotisations; $i++) {
            $caisse = $caisses->random();
            $type = $types[array_rand($types)];
            $frequence = $frequences[array_rand($frequences)];
            $typeMontant = $typeMontants[array_rand($typeMontants)];
            $montant = ($typeMontant === 'fixe') ? rand(5000, 100000) : null;
            $actif = (rand(1, 10) <= 8) ? true : false; // 80% actifs
            
            // Assigner un tag aléatoirement à 70% des cotisations
            $tag = (rand(1, 10) <= 7 && !empty($tags)) ? $tags[array_rand($tags)] : null;
            
            // Assigner un segment : 60% sans segment (accessibles à tous), 40% avec segment
            $segment = (rand(1, 10) <= 6) ? null : $segments[array_rand(array_filter($segments))];
            
            $nom = $nomsCotisations[$i] ?? 'Cotisation ' . ($i + 1);
            
            $cotisation = Cotisation::create([
                'numero' => 'COT-' . strtoupper(Str::random(6)),
                'nom' => $nom,
                'caisse_id' => $caisse->id,
                'type' => $type,
                'frequence' => $frequence,
                'type_montant' => $typeMontant,
                'montant' => $montant,
                'description' => 'Description de la cotisation: ' . $nom,
                'notes' => rand(1, 2) === 1 ? 'Notes additionnelles pour cette cotisation' : null,
                'actif' => $actif,
                'tag' => $tag,
                'segment' => $segment,
                'created_at' => Carbon::now()->subDays(rand(1, 180)),
                'updated_at' => Carbon::now()->subDays(rand(1, 180)),
            ]);

            $created++;
        }
        
        // Afficher les statistiques par segment
        $statsSegments = Cotisation::select('segment')
            ->selectRaw('COUNT(*) as nombre')
            ->where('actif', true)
            ->groupBy('segment')
            ->get();
        
        $this->command->info("Répartition des cotisations actives par segment :");
        foreach ($statsSegments as $stat) {
            $segmentName = $stat->segment ?? 'Sans segment (Tous)';
            $this->command->info("  - {$segmentName}: {$stat->nombre} cotisation(s)");
        }

        $this->command->info("{$created} cotisation(s) créée(s) avec succès.");
    }
}
