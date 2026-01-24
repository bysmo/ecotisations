<?php

namespace Database\Seeders;

use App\Models\EmailCampaign;
use App\Models\EmailLog;
use App\Models\Engagement;
use App\Models\Membre;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmailLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $membres = Membre::all();
        $paiements = Paiement::all();
        $engagements = Engagement::all();
        $campagnes = EmailCampaign::all();

        if ($membres->isEmpty()) {
            $this->command->warn('Aucun membre trouvé. Veuillez d\'abord exécuter MembreSeeder.');
            return;
        }

        $this->command->info('Création des logs d\'emails...');

        $types = [
            EmailLog::TYPE_CAMPAGNE,
            EmailLog::TYPE_PAIEMENT,
            EmailLog::TYPE_ENGAGEMENT,
            EmailLog::TYPE_FIN_MOIS,
            EmailLog::TYPE_RAPPEL,
            EmailLog::TYPE_AUTRE,
        ];

        $sujets = [
            'Rappel de paiement',
            'Confirmation de paiement',
            'Rappel d\'engagement',
            'Notification fin de mois',
            'Information importante',
            'Message automatique',
        ];

        $erreurs = [
            'Connexion SMTP échouée',
            'Adresse email invalide',
            'Timeout lors de l\'envoi',
            'Boîte mail destinataire pleine',
            'Erreur inconnue',
        ];

        $created = 0;
        $nbLogs = 50;

        for ($i = 0; $i < $nbLogs; $i++) {
            $type = $types[array_rand($types)];

            $membre = $membres->random();
            $destEmail = $membre->email ?: ('test' . rand(1, 9999) . '@example.com');

            // Statut réaliste
            $statutRand = rand(1, 100);
            $statut = $statutRand <= 80 ? EmailLog::STATUT_ENVOYE : ($statutRand <= 95 ? EmailLog::STATUT_ECHEC : EmailLog::STATUT_EN_ATTENTE);

            $envoyeAt = null;
            $erreur = null;
            if ($statut === EmailLog::STATUT_ENVOYE) {
                $envoyeAt = Carbon::now()->subDays(rand(0, 60))->subMinutes(rand(0, 1440));
            } elseif ($statut === EmailLog::STATUT_ECHEC) {
                $envoyeAt = Carbon::now()->subDays(rand(0, 60))->subMinutes(rand(0, 1440));
                $erreur = $erreurs[array_rand($erreurs)];
            }

            $campagneId = null;
            $paiementId = null;
            $engagementId = null;

            if ($type === EmailLog::TYPE_CAMPAGNE && $campagnes->isNotEmpty()) {
                $campagneId = $campagnes->random()->id;
            }
            if ($type === EmailLog::TYPE_PAIEMENT && $paiements->isNotEmpty()) {
                $paiementId = $paiements->random()->id;
            }
            if ($type === EmailLog::TYPE_ENGAGEMENT && $engagements->isNotEmpty()) {
                $engagementId = $engagements->random()->id;
            }

            $sujet = $sujets[array_rand($sujets)];

            EmailLog::create([
                'type' => $type,
                'campagne_id' => $campagneId,
                'membre_id' => $membre->id,
                'paiement_id' => $paiementId,
                'engagement_id' => $engagementId,
                'destinataire_email' => $destEmail,
                'sujet' => $sujet,
                'message' => "Bonjour {$membre->prenom} {$membre->nom},\n\nCeci est un email de test ({$type}).\n\nCordialement,\nL'équipe",
                'statut' => $statut,
                'erreur' => $erreur,
                'envoye_at' => $envoyeAt,
                'metadata' => [
                    'seed' => true,
                    'source' => 'EmailLogSeeder',
                ],
                'created_at' => $envoyeAt ?? Carbon::now()->subDays(rand(0, 60)),
                'updated_at' => $envoyeAt ?? Carbon::now()->subDays(rand(0, 60)),
            ]);

            $created++;
        }

        $this->command->info("{$created} log(s) d'email créé(s) avec succès.");
    }
}

