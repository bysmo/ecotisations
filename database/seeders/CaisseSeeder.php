<?php

namespace Database\Seeders;

use App\Models\Caisse;
use Illuminate\Database\Seeder;

class CaisseSeeder extends Seeder
{
    /**
     * Générer un numéro de caisse unique au format XXXX-XXXX (alphanumérique)
     */
    private function generateNumeroCaisse(): string
    {
        do {
            // Générer 4 caractères alphanumériques (majuscules et chiffres)
            $part1 = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4));
            $part2 = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4));
            $numero = $part1 . '-' . $part2;
        } while (Caisse::where('numero', $numero)->exists());

        return $numero;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caisses = [
            [
                'nom' => 'Caisse Principale',
                'description' => 'Caisse principale de l\'organisation pour les cotisations générales',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Événements',
                'description' => 'Caisse dédiée aux événements et activités de l\'organisation',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Urgences',
                'description' => 'Caisse de réserve pour les situations d\'urgence',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Projets',
                'description' => 'Caisse pour financer les projets de l\'organisation',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Sociale',
                'description' => 'Caisse pour les actions sociales et solidarité',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Formation',
                'description' => 'Caisse dédiée aux formations et développement des compétences',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Infrastructure',
                'description' => 'Caisse pour les investissements en infrastructure',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Communication',
                'description' => 'Caisse pour les activités de communication et marketing',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Partenariats',
                'description' => 'Caisse pour gérer les partenariats et collaborations',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Innovation',
                'description' => 'Caisse pour financer les projets innovants',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Développement',
                'description' => 'Caisse pour le développement organisationnel',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Réserve',
                'description' => 'Caisse de réserve stratégique',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Opérationnelle',
                'description' => 'Caisse pour les opérations courantes',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Investissement',
                'description' => 'Caisse pour les investissements à long terme',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Activités',
                'description' => 'Caisse pour les activités régulières',
                'statut' => 'active',
            ],
            [
                'nom' => 'Caisse Archive 1',
                'description' => 'Ancienne caisse archivée',
                'statut' => 'inactive',
            ],
            [
                'nom' => 'Caisse Archive 2',
                'description' => 'Ancienne caisse archivée',
                'statut' => 'inactive',
            ],
            [
                'nom' => 'Caisse Temporaire',
                'description' => 'Caisse temporaire pour événement ponctuel',
                'statut' => 'inactive',
            ],
            [
                'nom' => 'Caisse Test',
                'description' => 'Caisse de test et développement',
                'statut' => 'inactive',
            ],
            [
                'nom' => 'Caisse Fermée',
                'description' => 'Caisse fermée définitivement',
                'statut' => 'inactive',
            ],
        ];

        foreach ($caisses as $caisseData) {
            // Générer un numéro unique
            $caisseData['numero'] = $this->generateNumeroCaisse();
            // Le solde initial est toujours 0
            $caisseData['solde_initial'] = 0;

            Caisse::create($caisseData);
        }
    }
}
