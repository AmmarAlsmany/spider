<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PaymentService;

class UpdateOverduePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:update-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update payment statuses to overdue when due date has passed';

    /**
     * Execute the console command.
     */
    public function handle(PaymentService $paymentService)
    {
        $updatedCount = $paymentService->updateOverduePayments();
        $this->info("Updated {$updatedCount} payments to overdue status.");
    }
}
