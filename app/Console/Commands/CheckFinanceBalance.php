<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MouvementCaisse;
use Illuminate\Support\Facades\DB;

class CheckFinanceBalance extends Command
{
    protected $signature = 'finance:check-balance';
    protected $description = 'Vérifie l\'équilibre débit/crédit de toutes les transactions';

    public function handle()
    {
        $this->info("Analyse de l'intégrité financière...");

        // On groupe par référence pour vérifier le balancement
        $transactions = MouvementCaisse::select('reference_type', 'reference_id', 
            DB::raw("SUM(CASE WHEN sens = 'entree' THEN montant ELSE 0 END) as total_debit"),
            DB::raw("SUM(CASE WHEN sens = 'sortie' THEN montant ELSE 0 END) as total_credit")
        )
        ->groupBy('reference_type', 'reference_id')
        ->get();

        $errors = 0;
        foreach ($transactions as $tx) {
            $diff = abs((float)$tx->total_debit - (float)$tx->total_credit);
            if ($diff > 0.01) {
                $this->error("Déséquilibre sur {$tx->reference_type} #{$tx->reference_id} : Différence de {$diff}");
                $errors++;
            }
        }

        if ($errors === 0) {
            $this->info("Succès : Toutes les écritures enregistrées sont équilibrées.");
        } else {
            $this->warn("Total de {$errors} transaction(s) déséquilibrée(s).");
        }

        return $errors === 0 ? 0 : 1;
    }
}
