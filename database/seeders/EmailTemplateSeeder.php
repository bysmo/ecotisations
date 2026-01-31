<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'nom' => 'Activation de compte',
                'sujet' => 'Activation de votre compte - {{nom_site}}',
                'corps' => "Bonjour {{prenom}} {{nom}},\n\nMerci de vous être inscrit sur {{nom_site}}.\n\nVotre compte a été créé avec succès. Pour l'activer, veuillez utiliser le code de vérification suivant :\n\nCode : {{otp}}\n\nSi vous n'avez pas créé de compte, vous pouvez ignorer cet email.\n\nCordialement,\nL'équipe {{nom_site}}",
                'type' => 'activation',
                'actif' => true,
            ],
            [
                'nom' => 'Confirmation de paiement',
                'sujet' => 'Reçu de paiement - {{numero_paiement}}',
                'corps' => "Bonjour {{prenom}} {{nom}},\n\nNous confirmons la réception de votre paiement de {{montant}} pour la cotisation {{cotisation}}.\n\nDate : {{date_paiement}}\nMode : {{mode_paiement}}\n\nVous trouverez votre reçu en pièce jointe.\n\nCordialement,\nL'équipe {{nom_site}}",
                'type' => 'paiement',
                'actif' => true,
            ],
            [
                'nom' => 'Notification d\'engagement',
                'sujet' => 'Détails de votre engagement - {{numero_engagement}}',
                'corps' => "Bonjour {{prenom}} {{nom}},\n\nVotre engagement de {{montant_engage}} pour la cotisation {{cotisation}} a bien été enregistré.\n\nPériodicité : {{periodicite}}\nDu {{periode_debut}} au {{periode_fin}}\n\nCordialement,\nL'équipe {{nom_site}}",
                'type' => 'engagement',
                'actif' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['nom' => $template['nom']],
                $template
            );
        }
    }
}
