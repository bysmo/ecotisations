<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AuditFinancierService;

class AuditVerifyChainCommand extends Command
{
    protected $signature = 'audit:verify-chain';
    protected $description = 'Verify integrity of audit_financier hash chain';

    public function handle(): int
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('audit_financier')) {
            $this->warn('Table audit_financier missing.');
            return self::FAILURE;
        }
        $service = app(AuditFinancierService::class);
        $result = $service->verifyChain();
        if ($result['valid']) {
            $this->info('Chain integrity OK.');
            return self::SUCCESS;
        }
        $this->error('Chain broken at record id: ' . $result['first_broken_id']);
        return self::FAILURE;
    }
}
