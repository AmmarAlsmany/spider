<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AlertController;
use App\Services\ContractService;

class TestAutomaticFunctions extends Command
{
    protected $signature = 'test:automatic-functions {function? : Specific function to test}';
    protected $description = 'Test all or specific automatic functions';

    public function handle()
    {
        $function = $this->argument('function');
        
        if ($function) {
            $this->testSpecificFunction($function);
        } else {
            $this->testAllFunctions();
        }
    }

    private function testAllFunctions()
    {
        $this->info('Testing all automatic functions...');
        $this->info('----------------------------------------');

        // Test expired contracts check
        $this->info('1. Testing expired contracts check...');
        app(AlertController::class)->checkExpiredContracts();
        $this->info('✓ Expired contracts check completed');

        // Test due payments check
        $this->info('2. Testing due payments check...');
        app(AlertController::class)->checkDuePayments();
        $this->info('✓ Due payments check completed');

        // Test renewal needed check
        $this->info('3. Testing renewal needed check...');
        app(AlertController::class)->checkRenewalNeeded();
        $this->info('✓ Renewal needed check completed');

        // Test monthly report generation
        $this->info('4. Testing monthly report generation...');
        app(AlertController::class)->generateMonthlyReport();
        $this->info('✓ Monthly report generation completed');

        // Test completed contracts check
        $this->info('5. Testing completed contracts check...');
        app(ContractService::class)->checkAndUpdateCompletedContracts();
        $this->info('✓ Completed contracts check completed');

        $this->info('----------------------------------------');
        $this->info('All automatic functions tested successfully!');
    }

    private function testSpecificFunction($function)
    {
        $this->info("Testing specific function: {$function}");
        $this->info('----------------------------------------');

        switch ($function) {
            case 'expired-contracts':
                app(AlertController::class)->checkExpiredContracts();
                break;
            case 'due-payments':
                app(AlertController::class)->checkDuePayments();
                break;
            case 'renewal-needed':
                app(AlertController::class)->checkRenewalNeeded();
                break;
            case 'monthly-report':
                app(AlertController::class)->generateMonthlyReport();
                break;
            case 'completed-contracts':
                app(ContractService::class)->checkAndUpdateCompletedContracts();
                break;
            default:
                $this->error("Unknown function: {$function}");
                return;
        }

        $this->info('✓ Function tested successfully!');
    }
}
