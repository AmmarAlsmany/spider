<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ContractService;

class CheckContractsStatus extends Command
{
    protected $signature = 'contracts:check-status';
    protected $description = 'Check and update completed contracts based on end date';

    public function handle()
    {
        $contractService = new ContractService();
        $contractService->checkAndUpdateCompletedContracts();
        $this->info('Contracts status check completed successfully.');
    }
}
