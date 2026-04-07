<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NanoCreditPaliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // On vide la table avant de remplir pour éviter les doublons
        //DB::table('nano_credit_paliers')->truncate();

        $paliers = [
            // --- PALIER 1 : Rencontre ---
            [
                'numero' => 1,
                'nom' => 'Rencontre',
                'description' => 'Niveau d\'entrée pour les nouveaux membres. Faibles montants, pas de garants requis.',
                
                // Conditions d'accession
                'min_credits_rembourses' => 0,
                'min_montant_total_rembourse' => 0,
                'min_epargne_cumulee' => 0, // Ex: 5 000 FCFA d'épargne minimale
                'min_epargne_percent' => 0, // Doit avoir 100% du montant en épargne (garantie totale)

                // Paramètres du crédit
                'montant_plafond' => 50000,
                'nombre_garants' => 1,
                'duree_jours' => 30, // 1 mois
                'taux_interet' => 3.00, // 5%
                'frequence_remboursement' => 'journalier',
                'penalite_par_jour' => 0.50,
                'jours_avant_prelevement_garant' => 3,

                // Extras (ajouts récents)
                'min_garant_qualite' => 1,
                'pourcentage_partage_garant' => 2.50,

                // Conséquences impayés
                'downgrade_en_cas_impayes' => false, // Pas de niveau inférieur
                'jours_impayes_pour_downgrade' => 0,
                'interdiction_en_cas_recidive' => true,
                'nb_recidives_pour_interdiction' => 2,

                'actif' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // --- PALIER 2 : Seduction ---
            [
                'numero' => 2,
                'nom' => 'Seduction',
                'description' => 'Pour les membres ayant prouvé leur fiabilité. Montant modéré avec un garant.',
                
                'min_credits_rembourses' => 1,
                'min_montant_total_rembourse' => 5000,
                'min_epargne_cumulee' => 1500,
                'min_epargne_percent' => 10, // Doit avoir 50% du montant en épargne

                'montant_plafond' => 15000,
                'nombre_garants' => 2,
                'duree_jours' => 60, // 2 mois
                'taux_interet' => 4.50,
                'frequence_remboursement' => 'hebdomadaire',
                'penalite_par_jour' => 1.50,
                'jours_avant_prelevement_garant' => 14,

                'min_garant_qualite' => 1, // Le garant doit être au moins palier 1
                'pourcentage_partage_garant' => 5.00, // 5% des intérêts au garant

                'downgrade_en_cas_impayes' => true,
                'jours_impayes_pour_downgrade' => 15,
                'interdiction_en_cas_recidive' => true,
                'nb_recidives_pour_interdiction' => 2,

                'actif' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // --- PALIER 3 : CONFIANCE ---
            [
                'numero' => 3,
                'nom' => 'Confiance',
                'description' => 'Niveau intermédiaire. Accès à des montants plus conséquents.',
                
                'min_credits_rembourses' => 3,
                'min_montant_total_rembourse' => 200000,
                'min_epargne_cumulee' => 50000,
                'min_epargne_percent' => 40,

                'montant_plafond' => 500000,
                'nombre_garants' => 2,
                'duree_jours' => 90, // 3 mois
                'taux_interet' => 4.00,
                'frequence_remboursement' => 'hebdomadaire',
                'penalite_par_jour' => 2.00,
                'jours_avant_prelevement_garant' => 21,

                'min_garant_qualite' => 2, // Garants doit être palier 2+
                'pourcentage_partage_garant' => 10.00,

                'downgrade_en_cas_impayes' => true,
                'jours_impayes_pour_downgrade' => 15,
                'interdiction_en_cas_recidive' => false,
                'nb_recidives_pour_interdiction' => 3,

                'actif' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // --- PALIER 4 : Partenariat ---
            [
                'numero' => 4,
                'nom' => 'Partenariat',
                'description' => 'Pour les membres actifs et réguliers. Grande flexibilité.',
                
                'min_credits_rembourses' => 5,
                'min_montant_total_rembourse' => 600000,
                'min_epargne_cumulee' => 100000,
                'min_epargne_percent' => 30,

                'montant_plafond' => 1000000,
                'nombre_garants' => 2,
                'duree_jours' => 180, // 6 mois
                'taux_interet' => 3.50,
                'frequence_remboursement' => 'mensuel',
                'penalite_par_jour' => 2.50,
                'jours_avant_prelevement_garant' => 30,

                'min_garant_qualite' => 3,
                'pourcentage_partage_garant' => 10.00,

                'downgrade_en_cas_impayes' => true,
                'jours_impayes_pour_downgrade' => 30,
                'interdiction_en_cas_recidive' => false,
                'nb_recidives_pour_interdiction' => 3,

                'actif' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // --- PALIER 5 : Prestige ---
            [
                'numero' => 5,
                'nom' => 'Prestige',
                'description' => 'Niveau prestige pour les meilleurs historiques. Taux préférentiels.',
                
                'min_credits_rembourses' => 10,
                'min_montant_total_rembourse' => 1500000,
                'min_epargne_cumulee' => 250000,
                'min_epargne_percent' => 20, // Faible contrainte d'épargne vs crédit

                'montant_plafond' => 2500000,
                'nombre_garants' => 3,
                'duree_jours' => 365, // 1 an
                'taux_interet' => 3.00,
                'frequence_remboursement' => 'mensuel',
                'penalite_par_jour' => 3.00,
                'jours_avant_prelevement_garant' => 45,

                'min_garant_qualite' => 4, // Garants de haut niveau
                'pourcentage_partage_garant' => 15.00,

                'downgrade_en_cas_impayes' => true,
                'jours_impayes_pour_downgrade' => 30,
                'interdiction_en_cas_recidive' => false,
                'nb_recidives_pour_interdiction' => 3,

                'actif' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('nano_credit_paliers')->insert($paliers);
    }
}