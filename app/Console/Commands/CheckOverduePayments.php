<?php

namespace App\Console\Commands;

use App\Models\Cotisation;
use App\Models\Membre;
use App\Models\Paiement;
use App\Models\NotificationLog;
use App\Jobs\SendPaymentReminderJob;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckOverduePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-overdue-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les paiements en retard et envoyer des rappels par email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des paiements en retard...');

        $cotisations = Cotisation::where('actif', true)
            ->whereNotNull('frequence')
            ->get();

        $count = 0;

        foreach ($cotisations as $cotisation) {
            // Récupérer tous les membres actifs
            $membres = Membre::where('statut', 'actif')->get();

            foreach ($membres as $membre) {
                if (!$membre->email) {
                    continue;
                }

                // Calculer la date d'échéance attendue
                $dernierPaiement = Paiement::where('membre_id', $membre->id)
                    ->where('cotisation_id', $cotisation->id)
                    ->orderBy('date_paiement', 'desc')
                    ->first();

                $dateEcheance = null;

                if ($dernierPaiement) {
                    $dateEcheance = $this->calculerDateEcheance($dernierPaiement->date_paiement, $cotisation->frequence);
                } else {
                    // Si aucun paiement, utiliser la date d'adhésion
                    $dateEcheance = $this->calculerDateEcheance($membre->date_adhesion, $cotisation->frequence);
                }

                // Vérifier si le paiement est en retard
                if ($dateEcheance && $dateEcheance->isPast()) {
                    // Vérifier qu'il n'y a pas eu de paiement depuis l'échéance
                    $paiementRecent = Paiement::where('membre_id', $membre->id)
                        ->where('cotisation_id', $cotisation->id)
                        ->where('date_paiement', '>', $dateEcheance)
                        ->exists();

                    if (!$paiementRecent) {
                        // Vérifier qu'on n'a pas déjà envoyé un rappel récemment (7 jours)
                        if (!NotificationLog::hasRecentNotification(
                            NotificationLog::TYPE_PAYMENT_REMINDER,
                            'membre',
                            $membre->id,
                            7
                        )) {
                            // Envoyer le rappel
                            SendPaymentReminderJob::dispatch($membre, $cotisation, $dateEcheance);
                            $count++;
                        }
                    }
                }
            }
        }

        $this->info("{$count} rappel(s) de paiement en retard programmé(s).");
        return Command::SUCCESS;
    }

    /**
     * Calculer la date d'échéance selon la fréquence
     */
    private function calculerDateEcheance($dateDebut, $frequence)
    {
        $date = Carbon::parse($dateDebut);

        switch ($frequence) {
            case 'mensuelle':
                return $date->addMonth();
            case 'trimestrielle':
                return $date->addMonths(3);
            case 'semestrielle':
                return $date->addMonths(6);
            case 'annuelle':
                return $date->addYear();
            default:
                return null;
        }
    }
}
