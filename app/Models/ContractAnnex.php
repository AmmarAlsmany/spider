<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractAnnex extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'annex_number',
        'annex_date',
        'additional_amount',
        'number_of_payments',
        'payment_type',
        'description',
        'status',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'annex_date' => 'date',
        'approved_at' => 'datetime',
        'additional_amount' => 'decimal:2'
    ];

    public function contract()
    {
        return $this->belongsTo(contracts::class, 'contract_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
