<?php

namespace Database\Seeders;

use App\Models\Engagement;
use App\Models\Membre;
use App\Models\Cotisation;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EngagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $membres = Membre::where('statut', 'actif')->get();
        $cotisations = Cotisation::where('actif', true)->get();
        
        if ($membres->isEmpty()) {
            $this->command->warn('Aucun membre actif trouvé. Veuillez d\'abord exécuter MembreSeeder.');
            return;
        }
        
        if ($cotisations->isEmpty()) {
            $this->command->warn('Aucune cotisation active trouvée. Veuillez d\'abord exécuter CotisationSeeder.');
            return;
        }

        $this->command->info('Création des engagements...');

        // Récupérer les tags d'engagements
        $tags = Tag::where('type', 'engagement')->pluck('nom')->toArray();

        $periodicites = ['mensuelle', 'trimestrielle', 'semestrielle', 'annuelle', 'unique'];
        $statuts = ['en_cours', 'termine', 'annule'];

        $created = 0;
        $nbEngagements = 50; // Générer 50 engagements

        for ($i = 0; $i < $nbEngagements; $i++) {
            $membre = $membres->random();
            $cotisation = $cotisations->random();
            $periodicite = $periodicites[array_rand($periodicites)];
            
            // Générer des dates de période selon la périodicité
            $debut = Carbon::now()->subMonths(rand(1, 12));
            $fin = clone $debut;
            
            switch ($periodicite) {
                case 'mensuelle':
                    $fin->addMonths(rand(6, 24));
                    break;
                case 'trimestrielle':
                    $fin->addMonths(rand(6, 24));
                    break;
                case 'semestrielle':
                    $fin->addMonths(rand(12, 36));
                    break;
                case 'annuelle':
                    $fin->addYears(rand(1, 3));
                    break;
                case 'unique':
                    $fin = clone $debut;
                    break;
            }
            
            // Montant engagé selon le type de cotisation
            $montantEngage = $cotisation->montant ?? rand(10000, 50000);
            
            $statut = (rand(1, 10) <= 7) ? 'en_cours' : ($statuts[array_rand($statuts)]);
            
            // Assigner un tag aléatoirement à 70% des engagements
            $tag = (rand(1, 10) <= 7 && !empty($tags)) ? $tags[array_rand($tags)] : null;
            
            $engagement = Engagement::create([
                'numero' => 'ENG-' . strtoupper(Str::random(6)),
                'membre_id' => $membre->id,
                'cotisation_id' => $cotisation->id,
                'montant_engage' => $montantEngage,
                'periodicite' => $periodicite,
                'periode_debut' => $debut,
                'periode_fin' => $fin,
                'statut' => $statut,
                'tag' => $tag,
                'notes' => rand(1, 3) === 1 ? 'Notes sur l\'engagement' : null,
                'created_at' => $debut,
                'updated_at' => $debut,
            ]);

            $created++;
        }

        $this->command->info("{$created} engagement(s) créé(s) avec succès.");
    }
}
