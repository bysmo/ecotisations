<?php

namespace Database\Seeders;

use App\Models\NanoCreditType;
use Illuminate\Database\Seeder;

class NanoCreditTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprimer les anciens types pour repartir sur la nouvelle logique de jours
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        NanoCreditType::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $types = [
            [
                'nom' => 'Dépannage Urgent',
                'description' => 'Besoin de trésorerie immédiat pour imprévus.',
                'montant_min' => 10000,
                'montant_max' => 20000,
                'duree_jours' => 3,
                'taux_interet' => 2.00,
                'frequence_remboursement' => 'hebdomadaire',
                'actif' => true,
                'ordre' => 1,
            ],
            [
                'nom' => 'Mini-Crédit Commerce',
                'description' => 'Petit stock journalier pour vendeurs ambulants et étals.',
                'montant_min' => 5000,
                'montant_max' => 50000,
                'duree_jours' => 5,
                'taux_interet' => 3.00,
                'frequence_remboursement' => 'hebdomadaire',
                'actif' => true,
                'ordre' => 2,
            ],
            [
                'nom' => 'Avance sur Recettes',
                'description' => 'Financement court terme basé sur les ventes hebdo.',
                'montant_min' => 20000,
                'montant_max' => 100000,
                'duree_jours' => 7,
                'taux_interet' => 4.00,
                'frequence_remboursement' => 'hebdomadaire',
                'actif' => true,
                'ordre' => 3,
            ],
        ];

        foreach ($types as $type) {
            NanoCreditType::create($type);
        }

        $this->command->info('Types de nano-crédits courte durée créés avec succès.');
    }
}
