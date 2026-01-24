<?php

namespace App\Console\Commands;

use App\Models\Caisse;
use App\Models\User;
use App\Models\NotificationLog;
use App\Jobs\SendLowBalanceAlertJob;
use Illuminate\Console\Command;
use App\Models\AppSetting;

class CheckLowBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-low-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les caisses avec solde faible et envoyer des alertes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des soldes de caisses...');

        // Récupérer le seuil d'alerte (par défaut 50000 XOF)
        $seuilSetting = AppSetting::where('cle', 'seuil_solde_alerte')->first();
        $seuil = $seuilSetting ? (int)$seuilSetting->valeur : 50000;

        $caisses = Caisse::where('statut', 'active')->get();
        $count = 0;

        foreach ($caisses as $caisse) {
            $soldeActuel = $caisse->solde_actuel;

            if ($soldeActuel < $seuil) {
                // Récupérer les administrateurs
                $admins = User::whereHas('roles', function ($query) {
                    $query->where('slug', 'admin')->where('actif', true);
                })->get();

                foreach ($admins as $admin) {
                    // Vérifier qu'on n'a pas déjà envoyé une alerte récemment (24h)
                    if (!NotificationLog::hasRecentNotification(
                        NotificationLog::TYPE_LOW_BALANCE,
                        'user',
                        $admin->id,
                        1
                    )) {
                        // Envoyer l'alerte
                        SendLowBalanceAlertJob::dispatch($admin, $caisse, $soldeActuel, $seuil);
                        $count++;
                    }
                }
            }
        }

        $this->info("{$count} alerte(s) de solde faible programmée(s).");
        return Command::SUCCESS;
    }
}
