<?php

namespace App\Services;

use App\Models\payments;
use Carbon\Carbon;
use App\Traits\NotificationDispatcher;

class PaymentService
{
    use NotificationDispatcher;

    /**
     * Update payment statuses based on due dates
     * 
     * @return int Number of payments updated to overdue status
     */
    public function updateOverduePayments()
    {
        $now = Carbon::now();

        // Get all eligible payments where due date has passed
        $overduePayments = payments::whereIn('payment_status', ['unpaid'])
            ->where('due_date', '<', $now)
            ->with('customer') // Eager load customer relationship
            ->get();

        $updatedCount = 0;

        foreach ($overduePayments as $payment) {
            // Double check the payment hasn't been processed by another process
            if ($payment->payment_status === 'unpaid') {
                $payment->payment_status = 'overdue';
                $payment->updated_at = $now;
                $payment->save();
                
                // Send overdue payment notification to customer
                $customer = $payment->customer;
                $this->notifyRoles(['client','finance','sales'], [
                    'title' => 'Overdue Payment',
                    'message' => 'Your payment of ' . $payment->payment_amount . ' SAR is overdue. Please make the payment as soon as possible.',
                    'type' => 'info',
                    'priority' => 'normal',
                ], $customer->id, $payment->sales_id);
                
                $updatedCount++;
            }
        }

        return $updatedCount;
    }
}
