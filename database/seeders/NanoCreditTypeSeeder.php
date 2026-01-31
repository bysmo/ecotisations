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
        // Supprimer les anciens types pour repartir sur la nouvelle logique de jours et fréquences
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        NanoCreditType::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $types = [
            [
                'nom' => 'Dépannage Urgent',
                'description' => 'Besoin de trésorerie immédiat pour imprévus (remboursement journalier).',
                'montant_min' => 10000,
                'montant_max' => 20000,
                'duree_jours' => 3,
                'taux_interet' => 2.00,
                'frequence_remboursement' => 'journalier',
                'actif' => true,
                'ordre' => 1,
            ],
            [
                'nom' => 'Mini-Crédit Hebdo',
                'description' => 'Petit stock journalier remboursable en fin de semaine.',
                'montant_min' => 5000,
                'montant_max' => 50000,
                'duree_jours' => 7,
                'taux_interet' => 3.00,
                'frequence_remboursement' => 'hebdomadaire',
                'actif' => true,
                'ordre' => 2,
            ],
            [
                'nom' => 'Micro-Business 30J',
                'description' => 'Développement d\'activité sur un cycle de 30 jours (remboursement hebdo).',
                'montant_min' => 50000,
                'montant_max' => 200000,
                'duree_jours' => 30,
                'taux_interet' => 5.00,
                'frequence_remboursement' => 'hebdomadaire',
                'actif' => true,
                'ordre' => 3,
            ],
        ];

        foreach ($types as $type) {
            NanoCreditType::create($type);
        }

        $this->command->info('Types de nano-crédits (mode journalier/30j) créés avec succès.');
    }
}
