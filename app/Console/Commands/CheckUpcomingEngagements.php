<?php

namespace App\Console\Commands;

use App\Models\Engagement;
use App\Models\NotificationLog;
use App\Jobs\SendEngagementReminderJob;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckUpcomingEngagements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-upcoming-engagements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les engagements arrivant à échéance et envoyer des rappels';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des engagements à échéance...');

        $engagements = Engagement::where('statut', 'actif')
            ->whereNotNull('periode_fin')
            ->get();

        $count = 0;
        $joursRappels = [7, 3, 1]; // Rappels à J-7, J-3, J-1

        foreach ($engagements as $engagement) {
            $dateFin = Carbon::parse($engagement->periode_fin);
            $joursRestants = now()->diffInDays($dateFin, false);

            foreach ($joursRappels as $jours) {
                // Si on est exactement à J-X jours de l'échéance
                if ($joursRestants == $jours) {
                    $membre = $engagement->membre;
                    
                    if ($membre && $membre->email) {
                        // Vérifier qu'on n'a pas déjà envoyé ce rappel
                        $metadata = ['jours' => $jours];
                        $recentNotification = NotificationLog::where('type', NotificationLog::TYPE_ENGAGEMENT_DUE)
                            ->where('recipient_type', 'membre')
                            ->where('recipient_id', $membre->id)
                            ->where('status', NotificationLog::STATUS_SENT)
                            ->whereJsonContains('metadata->jours', $jours)
                            ->where('created_at', '>=', now()->subDay())
                            ->exists();

                        if (!$recentNotification) {
                            // Envoyer le rappel
                            SendEngagementReminderJob::dispatch($membre, $engagement, $jours);
                            $count++;
                        }
                    }
                }
            }
        }

        $this->info("{$count} rappel(s) d'engagement programmé(s).");
        return Command::SUCCESS;
    }
}
