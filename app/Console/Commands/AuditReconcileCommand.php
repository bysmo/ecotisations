<?php

namespace App\Console\Commands;

use App\Models\Caisse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Réconciliation : compare pour chaque caisse le solde calculé (solde_initial + mouvements)
 * au solde "livre" (solde_initial actuel). Si écart > seuil → alerte critique + optionnellement gel des comptes.
 */
class AuditReconcileCommand extends Command
{
    protected $signature = 'audit:reconcile
                            {--threshold= : Seuil d\'écart (défaut: config audit.reconciliation_alert_threshold)}
                            {--freeze : Geler les caisses en alerte (sinon selon config)}';

    protected $description = 'Réconciliation des soldes (calculé vs livre) ; alerte si écart > seuil';

    public function handle(): int
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('audit_balances_calculated') ||
            ! \Illuminate\Support\Facades\Schema::hasTable('audit_reconciliation_snapshots')) {
            $this->warn('Tables d\'audit réconciliation absentes. Exécutez les migrations.');
            return self::FAILURE;
        }

        $threshold = $this->option('threshold') !== null
            ? (float) $this->option('threshold')
            : (float) config('audit.reconciliation_alert_threshold', 0.01);
        $freeze = $this->option('freeze') ?: config('audit.freeze_accounts_on_alert', false);
        $now = now();

        $caisses = Caisse::all();
        $alertCount = 0;

        foreach ($caisses as $caisse) {
            $soldeLivre = (float) $caisse->solde_initial;
            $soldeCalcule = $this->computeSolde($caisse->id);
            $ecart = round($soldeCalcule - $soldeLivre, 4);
            $alerteCritique = abs($ecart) > $threshold;

            DB::table('audit_balances_calculated')->insert([
                'caisse_id'     => $caisse->id,
                'solde_calcule' => $soldeCalcule,
                'computed_at'   => $now,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);

            DB::table('audit_balances_book')->insert([
                'caisse_id'    => $caisse->id,
                'solde_livre'  => $soldeLivre,
                'checked_at'   => $now,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            DB::table('audit_reconciliation_snapshots')->insert([
                'caisse_id'        => $caisse->id,
                'solde_calcule'    => $soldeCalcule,
                'solde_livre'      => $soldeLivre,
                'ecart'            => $ecart,
                'alerte_critique'  => $alerteCritique,
                'checked_at'       => $now,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            if ($alerteCritique) {
                $alertCount++;
                $message = sprintf(
                    'Réconciliation: écart critique caisse "%s" (id=%d): calculé=%s, livre=%s, écart=%s',
                    $caisse->nom,
                    $caisse->id,
                    $soldeCalcule,
                    $soldeLivre,
                    $ecart
                );
                \Illuminate\Support\Facades\Log::critical($message);

                DB::table('audit_alertes')->insert([
                    'type'           => 'reconciliation_ecart',
                    'caisse_id'      => $caisse->id,
                    'ecart'          => $ecart,
                    'message'        => $message,
                    'comptes_geles'  => false,
                    'created_at'     => $now,
                ]);

                if ($freeze) {
                    $caisse->update(['statut' => 'gelée']);
                    DB::table('audit_alertes')->where('caisse_id', $caisse->id)->where('type', 'reconciliation_ecart')->orderBy('id', 'desc')->limit(1)->update(['comptes_geles' => true]);
                    $this->error("Alerte + gel: {$message}");
                } else {
                    $this->warn($message);
                }
            }
        }

        if ($alertCount === 0) {
            $this->info('Réconciliation OK pour ' . $caisses->count() . ' caisse(s).');
        } else {
            $this->warn("Réconciliation: {$alertCount} alerte(s) critique(s).");
        }

        return self::SUCCESS;
    }

    /**
     * Solde calculé = solde_initial de la caisse + sum(entrees) - sum(sorties) sur mouvements_caisse.
     * On utilise la même logique que Caisse::getSoldeActuelAttribute mais en requête pour cohérence.
     */
    protected function computeSolde(int $caisseId): float
    {
        $caisse = Caisse::find($caisseId);
        if (! $caisse) {
            return 0.0;
        }
        return (float) $caisse->solde_actuel;
    }
}
