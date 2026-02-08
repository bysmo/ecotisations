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
                'description' => 'Petit crédit court terme, remboursement mensuel. Idéal pour un besoin ponctuel.',
                'montant_min' => 5000,
                'montant_max' => 25000,
                'duree_mois' => 3,
                'taux_interet' => 5.0,
                'frequence_remboursement' => 'mensuel',
                'actif' => true,
                'ordre' => 1,
            ],
            [
                'nom' => 'Nano Crédit Standard',
                'description' => 'Crédit sur 6 mois avec remboursement mensuel. Taux préférentiel.',
                'montant_min' => 10000,
                'montant_max' => 100000,
                'duree_mois' => 6,
                'taux_interet' => 6.0,
                'frequence_remboursement' => 'mensuel',
                'actif' => true,
                'ordre' => 2,
            ],
            [
                'nom' => 'Nano Crédit Annuel',
                'description' => 'Crédit sur 12 mois. Remboursement mensuel ou trimestriel selon besoin.',
                'montant_min' => 25000,
                'montant_max' => 200000,
                'duree_mois' => 12,
                'taux_interet' => 7.0,
                'frequence_remboursement' => 'mensuel',
                'actif' => true,
                'ordre' => 3,
            ],
            [
                'nom' => 'Nano Crédit Hebdomadaire',
                'description' => 'Très court terme, remboursement hebdomadaire. Pour les petits montants.',
                'montant_min' => 2000,
                'montant_max' => 15000,
                'duree_mois' => 2,
                'taux_interet' => 4.0,
                'frequence_remboursement' => 'hebdomadaire',
                'actif' => true,
                'ordre' => 4,
            ],
            [
                'nom' => 'Nano Crédit Trimestriel',
                'description' => 'Remboursement trimestriel pour alléger les échéances. Montants moyens.',
                'montant_min' => 50000,
                'montant_max' => 300000,
                'duree_mois' => 12,
                'taux_interet' => 7.5,
                'frequence_remboursement' => 'trimestriel',
                'actif' => true,
                'ordre' => 5,
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
