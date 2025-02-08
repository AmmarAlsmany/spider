<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractHistory extends Model
{
    protected $table = 'contract_history';
    
    protected $fillable = [
        'contract_id',
        'action',
        'notes',
    ];

    /**
     * Get the contract that owns this history record.
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(contracts::class, 'contract_id');
    }
}
