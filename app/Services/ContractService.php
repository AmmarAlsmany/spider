<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\contracts;
use App\Models\User;
use App\Notifications\SalesNotification;
use Illuminate\Support\Facades\Log;

class ContractService
{
    /**
     * Update contract status to completed if end date matches today
     * 
     * @return void
     */
    public function checkAndUpdateCompletedContracts()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        // Get contracts ending today or already ended but not marked as completed
        $endingContracts = contracts::where(function($query) use ($today) {
                $query->where('contract_end_date', $today)
                      ->orWhere('contract_end_date', '>', $today);
            })
            ->where('contract_status', '!=', 'completed')
            ->get();
            
        // Send renewal notifications to sales representatives
        foreach ($endingContracts as $contract) {
            try {
                $this->notifySalesAboutRenewal($contract);
            } catch (\Exception $e) {
                Log::error('Failed to send renewal notification for contract #' . $contract->contract_number . ': ' . $e->getMessage());
            }
        }
        
        // Update contract status to completed
        contracts::where(function($query) use ($today) {
                $query->where('contract_end_date', $today)
                      ->orWhere('contract_end_date', '>', $today);
            })
            ->where('contract_status', '!=', 'completed')
            ->update(['contract_status' => 'completed']);
    }
    
    /**
     * Notify sales representative about contract renewal
     * 
     * @param contracts $contract
     * @return void
     */
    private function notifySalesAboutRenewal($contract)
    {
        // Get the sales representative
        $salesRep = User::find($contract->sales_id);
        
        if (!$salesRep) {
            Log::error('Sales representative not found for contract #' . $contract->contract_number);
            return;
        }
        
        // Get customer name
        $customerName = $contract->customer ? $contract->customer->name : 'Unknown Customer';
        
        // Create renewal URL
        $renewalUrl = route('contract.renewal.form', ['id' => $contract->id]);
        
        // Prepare notification data
        $notificationData = [
            'title' => 'Contract Renewal Required',
            'message' => "Contract #{$contract->contract_number} with {$customerName} ends today. Do you want to renew it?",
            'url' => $renewalUrl,
            'type' => 'warning',
            'priority' => 'high'
        ];
        
        // Send notification to sales representative
        $salesRep->notify(new SalesNotification($notificationData));
    }
}
