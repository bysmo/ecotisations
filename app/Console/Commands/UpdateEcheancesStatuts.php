<?php

namespace App\Console\Commands;

use App\Models\EpargneEcheance;
use App\Models\NanoCreditEcheance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Migration et nettoyage des statuts d'échéances.
 * Le statut temporel (en retard, etc.) est désormais calculé dynamiquement par le modèle.
 * Le champ 'statut' en base de données ne sert plus qu'à l'état de paiement (en_attente, en_cours, payee).
 */
class UpdateEcheancesStatuts extends Command
{
    protected $signature = 'echeances:update-statuts';

    protected $description = 'Migre les anciens statuts temporels (a_venir/en_retard) vers le nouvel état de paiement (en_attente).';

    public function handle(): int
    {
        $this->info('=== Migration des statuts d\'échéances vers le nouvel état (en_attente/payee) ===');

        // 1. Échéances tontines
        $nbEpargne = EpargneEcheance::whereIn('statut', ['a_venir', 'en_retard'])
            ->update(['statut' => 'en_attente']);
        
        $this->line("  ✅ Tontines  : {$nbEpargne} échéance(s) migrées vers 'en_attente'.");

        // 2. Échéances nano-crédits
        $nbNc = 0;
        if (class_exists(NanoCreditEcheance::class)) {
            $nbNc = NanoCreditEcheance::whereIn('statut', ['a_venir', 'en_retard'])
                ->update(['statut' => 'en_attente']);
            $this->line("  ✅ Crédits   : {$nbNc} échéance(s) migrées vers 'en_attente'.");
        }

        $total = $nbEpargne + $nbNc;
        $this->info("Migration terminée : {$total} au total. Le statut temporel est désormais calculé dynamiquement.");

        return Command::SUCCESS;
    }
}
