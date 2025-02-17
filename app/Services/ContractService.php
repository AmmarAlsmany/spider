<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\contracts;

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
        
        contracts::where('contract_end_date', $today)
            ->where('contract_status', '!=', 'completed')
            ->update(['contract_status' => 'completed']);
    }
}
