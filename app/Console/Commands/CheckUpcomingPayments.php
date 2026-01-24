<?php

namespace App\Console\Commands;

use App\Models\Cotisation;
use App\Models\Membre;
use App\Models\Paiement;
use App\Models\NotificationLog;
use App\Models\AppSetting;
use App\Jobs\SendUpcomingPaymentReminderJob;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckUpcomingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-upcoming-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les paiements de cotisations à venir et envoyer des rappels avant échéance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des paiements de cotisations à venir...');

        // Récupérer le nombre de jours avant échéance pour les rappels (par défaut 3 jours)
        $joursAvantEcheance = AppSetting::get('jours_rappel_paiement', 3);

        // Récupérer toutes les cotisations actives avec une fréquence (non-unique)
        $cotisations = Cotisation::where('actif', true)
            ->whereNotNull('frequence')
            ->where('frequence', '!=', 'unique')
            ->get();

        $count = 0;

        foreach ($cotisations as $cotisation) {
            // Récupérer tous les membres actifs qui peuvent voir cette cotisation
            $membres = Membre::where('statut', 'actif')
                ->where(function($query) use ($cotisation) {
                    // Membre sans segment ou avec segment correspondant
                    $cotisationSegment = trim($cotisation->segment ?? '');
                    if ($cotisationSegment === '') {
                        // Cotisation générale, accessible à tous
                        $query->whereNull('segment')
                              ->orWhere('segment', '');
                    } else {
                        // Cotisation spécifique à un segment
                        $query->where('segment', $cotisationSegment);
                    }
                })
                ->get();

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
                    if ($membre->date_adhesion) {
                        $dateEcheance = $this->calculerDateEcheance($membre->date_adhesion, $cotisation->frequence);
                    } else {
                        continue; // Pas de date d'adhésion, on passe
                    }
                }

                if (!$dateEcheance) {
                    continue;
                }

                // Vérifier si on est dans la période de rappel (X jours avant l'échéance)
                $joursRestants = now()->diffInDays($dateEcheance, false);

                // On envoie un rappel si on est exactement à J-X jours (X étant configurable)
                if ($joursRestants == $joursAvantEcheance) {
                    // Vérifier qu'il n'y a pas eu de paiement depuis le dernier paiement
                    $paiementRecent = Paiement::where('membre_id', $membre->id)
                        ->where('cotisation_id', $cotisation->id)
                        ->where('date_paiement', '>', $dernierPaiement ? $dernierPaiement->date_paiement : $membre->date_adhesion)
                        ->exists();

                    if (!$paiementRecent) {
                        // Vérifier qu'on n'a pas déjà envoyé ce rappel récemment (1 jour)
                        if (!NotificationLog::where('type', NotificationLog::TYPE_UPCOMING_PAYMENT)
                            ->where('recipient_type', 'membre')
                            ->where('recipient_id', $membre->id)
                            ->where('status', NotificationLog::STATUS_SENT)
                            ->whereJsonContains('metadata->cotisation_id', $cotisation->id)
                            ->whereJsonContains('metadata->jours_avant', $joursAvantEcheance)
                            ->where('created_at', '>=', now()->subDay())
                            ->exists()) {
                            // Envoyer le rappel
                            SendUpcomingPaymentReminderJob::dispatch($membre, $cotisation, $dateEcheance, $joursAvantEcheance);
                            $count++;
                        }
                    }
                }
            }
        }

        $this->info("{$count} rappel(s) de paiement à venir programmé(s).");
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
                return $date->copy()->addMonth();
            case 'trimestrielle':
                return $date->copy()->addMonths(3);
            case 'semestrielle':
                return $date->copy()->addMonths(6);
            case 'annuelle':
                return $date->copy()->addYear();
            default:
                return null;
        }
    }
}
