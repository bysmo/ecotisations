<?php

namespace App\Console\Commands;

use App\Services\ParrainageService;
use Illuminate\Console\Command;

class ActiverCommissionsParrainage extends Command
{
    protected $signature   = 'parrainage:activer-commissions';
    protected $description = 'Active les commissions de parrainage dont le délai de validation est écoulé';

    public function handle(ParrainageService $service): int
    {
        $count = $service->activerCommissionsEcheantes();
        $this->info("{$count} commission(s) activée(s).");
        return Command::SUCCESS;
    }
}
