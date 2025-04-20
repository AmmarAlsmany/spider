<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AlertController;
use App\Services\ContractService;
use App\Services\PaymentService;

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

        // Test due payments check
        $this->info('2. Testing due payments check...');
        app(AlertController::class)->checkDuePayments();
        $this->info('✓ Due payments check completed');

        // Test renewal needed check
        $this->info('3. Testing renewal needed check...');
        app(AlertController::class)->reminderRenewal();
        $this->info('✓ Renewal needed check completed');

        // Test monthly report generation
        $this->info('4. Testing monthly report generation...');
        app(AlertController::class)->generateMonthlyReport();
        $this->info('✓ Monthly report generation completed');

        // Test overdue payments check
        $this->info('5. Testing overdue payments check...');
        app(PaymentService::class)->updateOverduePayments();
        $this->info('✓ Overdue payments check completed');

        // Test completed contracts check
        $this->info('6. Testing completed contracts check...');
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
            case 'due-payments':
                app(AlertController::class)->checkDuePayments();
                break;
            case 'renewal-needed':
                app(AlertController::class)->reminderRenewal();
                break;
            case 'monthly-report':
                app(AlertController::class)->generateMonthlyReport();
                break;
            case 'overdue-payments':
                app(PaymentService::class)->updateOverduePayments();
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
