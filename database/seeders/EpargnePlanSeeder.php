<?php

namespace Database\Seeders;

use App\Models\EpargnePlan;
use App\Models\Caisse;
use Illuminate\Database\Seeder;

class EpargnePlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Création des plans d\'épargne...');

        $caisse = Caisse::first();
        $caisseId = $caisse ? $caisse->id : null;

        $plans = [
            [
                'nom' => 'Épargne Journalière Flexible',
                'description' => 'Épargnez chaque jour selon vos moyens pour réaliser vos projets.',
                'montant_min' => 500,
                'montant_max' => 50000,
                'frequence' => 'quotidien',
                'taux_remuneration' => 2.00,
                'duree_mois' => 3,
                'caisse_id' => $caisseId,
                'actif' => true,
                'ordre' => 1,
            ],
            [
                'nom' => 'Épargne Hebdomadaire Sérénité',
                'description' => 'Un versement par semaine pour une épargne régulière sans stress.',
                'montant_min' => 5000,
                'montant_max' => 200000,
                'frequence' => 'hebdomadaire',
                'taux_remuneration' => 3.50,
                'duree_mois' => 6,
                'caisse_id' => $caisseId,
                'actif' => true,
                'ordre' => 2,
            ],
            [
                'nom' => 'Plan Épargne Mensuel Projet',
                'description' => 'Le plan idéal pour les grands projets avec une rémunération attractive.',
                'montant_min' => 25000,
                'montant_max' => null,
                'frequence' => 'mensuel',
                'taux_remuneration' => 5.00,
                'duree_mois' => 12,
                'caisse_id' => $caisseId,
                'actif' => true,
                'ordre' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            EpargnePlan::create($plan);
        }

        $this->command->info('Plans d\'épargne créés avec succès.');
    }
}
