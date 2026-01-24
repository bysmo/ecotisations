<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Création des tags...');

        // Tags pour les cotisations
        $tagsCotisations = [
            [
                'nom' => 'Premium',
                'type' => 'cotisation',
                'description' => 'Tag pour les cotisations premium',
            ],
            [
                'nom' => 'Standard',
                'type' => 'cotisation',
                'description' => 'Tag pour les cotisations standard',
            ],
            [
                'nom' => 'VIP',
                'type' => 'cotisation',
                'description' => 'Tag pour les cotisations VIP',
            ],
            [
                'nom' => 'Étudiant',
                'type' => 'cotisation',
                'description' => 'Tag pour les cotisations étudiantes',
            ],
            [
                'nom' => 'Senior',
                'type' => 'cotisation',
                'description' => 'Tag pour les cotisations seniors',
            ],
            [
                'nom' => 'Famille',
                'type' => 'cotisation',
                'description' => 'Tag pour les cotisations familiales',
            ],
            [
                'nom' => 'Entreprise',
                'type' => 'cotisation',
                'description' => 'Tag pour les cotisations entreprises',
            ],
            [
                'nom' => 'Bienfaiteur',
                'type' => 'cotisation',
                'description' => 'Tag pour les cotisations bienfaiteurs',
            ],
            [
                'nom' => 'Événement',
                'type' => 'cotisation',
                'description' => 'Tag pour les cotisations liées aux événements',
            ],
            [
                'nom' => 'Urgence',
                'type' => 'cotisation',
                'description' => 'Tag pour les cotisations d\'urgence',
            ],
        ];

        // Tags pour les engagements
        $tagsEngagements = [
            [
                'nom' => 'Mensuel',
                'type' => 'engagement',
                'description' => 'Tag pour les engagements mensuels',
            ],
            [
                'nom' => 'Trimestriel',
                'type' => 'engagement',
                'description' => 'Tag pour les engagements trimestriels',
            ],
            [
                'nom' => 'Annuel',
                'type' => 'engagement',
                'description' => 'Tag pour les engagements annuels',
            ],
            [
                'nom' => 'Projet',
                'type' => 'engagement',
                'description' => 'Tag pour les engagements liés aux projets',
            ],
            [
                'nom' => 'Formation',
                'type' => 'engagement',
                'description' => 'Tag pour les engagements de formation',
            ],
            [
                'nom' => 'Infrastructure',
                'type' => 'engagement',
                'description' => 'Tag pour les engagements d\'infrastructure',
            ],
            [
                'nom' => 'Social',
                'type' => 'engagement',
                'description' => 'Tag pour les engagements sociaux',
            ],
            [
                'nom' => 'Innovation',
                'type' => 'engagement',
                'description' => 'Tag pour les engagements d\'innovation',
            ],
        ];

        // Créer les tags de cotisations
        foreach ($tagsCotisations as $tag) {
            Tag::firstOrCreate(
                ['nom' => $tag['nom'], 'type' => $tag['type']],
                $tag
            );
        }

        // Créer les tags d'engagements
        foreach ($tagsEngagements as $tag) {
            Tag::firstOrCreate(
                ['nom' => $tag['nom'], 'type' => $tag['type']],
                $tag
            );
        }

        $this->command->info(count($tagsCotisations) . ' tags de cotisations créés.');
        $this->command->info(count($tagsEngagements) . ' tags d\'engagements créés.');
    }
}
