<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditMerkleRootCommand extends Command
{
    protected $signature = 'audit:merkle
                            {--period=1 : Hours to cover (default 1)}
                            {--verify : Verify chain before computing}';

    protected $description = 'Compute and store Merkle root of audit_financier journal (run hourly)';

    public function handle(): int
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('audit_financier')) {
            $this->warn('Table audit_financier missing. Run migrations.');
            return self::FAILURE;
        }

        if ($this->option('verify')) {
            $service = app(\App\Services\AuditFinancierService::class);
            $result = $service->verifyChain();
            if (! $result['valid']) {
                $this->error('Audit chain invalid (first broken id: ' . $result['first_broken_id'] . ').');
                return self::FAILURE;
            }
            $this->info('Audit chain verified.');
        }

        $periodHours = (int) $this->option('period');
        $periodEnd = now();
        $periodStart = now()->subHours($periodHours);

        $rows = DB::table('audit_financier')
            ->where('created_at', '>=', $periodStart)
            ->where('created_at', '<=', $periodEnd)
            ->orderBy('id')
            ->get(['id', 'hash_chain']);

        if ($rows->isEmpty()) {
            $this->info('No records in period. Skipping.');
            return self::SUCCESS;
        }

        $leaves = $rows->pluck('hash_chain')->values()->all();
        $root = $this->merkleRoot($leaves);

        if (\Illuminate\Support\Facades\Schema::hasTable('audit_merkle_roots')) {
            DB::table('audit_merkle_roots')->insert([
                'period_start'  => $periodStart,
                'period_end'    => $periodEnd,
                'merkle_root'   => $root,
                'record_count'  => $rows->count(),
                'created_at'    => now(),
            ]);
        }

        $this->info('Merkle root: ' . $root . ' (' . $rows->count() . ' records).');

        $driver = config('audit.merkle_export_driver');
        if ($driver === 'file') {
            $path = config('audit.merkle_export_path', storage_path('app/audit_merkle'));
            if (! is_dir($path)) {
                mkdir($path, 0755, true);
            }
            $file = $path . '/merkle_' . $periodStart->format('Y-m-d_H') . '.txt';
            file_put_contents($file, $periodStart->toIso8601String() . "\n" . $periodEnd->toIso8601String() . "\n" . $root . "\n" . $rows->count());
            $this->info('Exported to: ' . $file);
        }

        return self::SUCCESS;
    }

    protected function merkleRoot(array $leaves): string
    {
        if (empty($leaves)) {
            return hash('sha256', '');
        }
        $level = $leaves;
        while (count($level) > 1) {
            $next = [];
            for ($i = 0; $i < count($level); $i += 2) {
                $left = $level[$i];
                $right = $i + 1 < count($level) ? $level[$i + 1] : $left;
                $next[] = hash('sha256', $left . $right);
            }
            $level = $next;
        }
        return $level[0];
    }
}
