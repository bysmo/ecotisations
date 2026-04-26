<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Paiement;
use App\Services\PiSpiService;
use Illuminate\Support\Facades\Log;

class CheckPendingPiSpiPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pispi:check-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier le statut des paiements Pi-SPI en attente de confirmation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des paiements Pi-SPI en attente...');

        $pispiService = app(PiSpiService::class);
        $pendingPayments = Paiement::where('statut', 'en_attente')
            ->where('mode_paiement', 'pispi')
            ->where('created_at', '>=', now()->subDays(2)) // On ne vérifie que les récents
            ->get();

        if ($pendingPayments->isEmpty()) {
            $this->info('Aucun paiement en attente trouvé.');
            return 0;
        }

        $bar = $this->output->createProgressBar($pendingPayments->count());
        $bar->start();

        foreach ($pendingPayments as $paiement) {
            try {
                // On utilise la même logique de troncature que lors de l'initiation
                $txId = substr((string)$paiement->reference, 0, 16);
                
                $result = $pispiService->checkPaymentStatus($txId);

                if ($result['success']) {
                    $statut = strtoupper($result['status']);
                    Log::info("Pi-SPI Cron Check: TX={$txId}, Status={$statut}");

                    if (in_array($statut, ['SUCCES', 'VALIDE', 'COMPLETED'])) {
                        $this->validatePayment($paiement, $result['data']);
                    } elseif (in_array($statut, ['EXPIRE', 'REJETE', 'ECHEC', 'CANCELLED'])) {
                        $paiement->update([
                            'statut' => 'echoue',
                            'commentaire' => $paiement->commentaire . "\n[Pi-SPI Cron: Statut " . $statut . " le " . now()->toDateTimeString() . "]"
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Pi-SPI Cron Error for Payment #{$paiement->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Vérification terminée.');

        return 0;
    }

    /**
     * Valider le paiement (Logique identique au Webhook)
     */
    private function validatePayment($paiement, $data)
    {
        if ($paiement->statut === 'valide') return;

        $paiement->update([
            'statut' => 'valide',
            'date_paiement' => now(),
            'commentaire' => $paiement->commentaire . "\n[Pi-SPI Cron Auto-Check OK: " . now()->toDateTimeString() . "]"
        ]);
        
        // Mise à jour de l'adhésion si c'est une cotisation/cagnotte
        if ($paiement->cotisation_id && $paiement->membre_id) {
            $adhesion = \App\Models\CotisationAdhesion::where('membre_id', $paiement->membre_id)
                ->where('cotisation_id', $paiement->cotisation_id)
                ->first();
            
            if ($adhesion && $adhesion->statut !== 'accepte') {
                $adhesion->update(['statut' => 'accepte']);
            }
        }

        // Gestion des écheances tontines
        if ($paiement->metadata && isset($paiement->metadata['echeance_id'])) {
            $echeance = \App\Models\EpargneEcheance::find($paiement->metadata['echeance_id']);
            if ($echeance) {
                $echeance->update(['statut' => 'payee']);
            }
        }
    }
}
