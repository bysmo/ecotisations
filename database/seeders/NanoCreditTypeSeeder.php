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
        $types = [
            [
                'nom' => 'Nano Crédit Express',
                'description' => 'Petit journalier, remboursement journalier. Idéal pour un besoin ponctuel.',
                'montant_min' => 5000,
                'montant_max' => 25000,
                'duree_jours' => 1,
                'taux_interet' => 5.0,
                'frequence_remboursement' => 'journalier',
                'actif' => true,
                'ordre' => 1,
            ],
            [
                'nom' => 'Nano Crédit Standard',
                'description' => 'Crédit sur 5 jours avec remboursement journalier. Taux préférentiel.',
                'montant_min' => 10000,
                'montant_max' => 100000,
                'duree_jours' => 5,
                'taux_interet' => 6.0,
                'frequence_remboursement' => 'journalier',
                'actif' => true,
                'ordre' => 2,
            ],
        ];

        foreach ($types as $data) {
            NanoCreditType::updateOrCreate(
                ['nom' => $data['nom']],
                $data
            );
        }

        $this->command->info(count($types) . ' type(s) de nano crédit créé(s) ou mis à jour.');
    }
}
