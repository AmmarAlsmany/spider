<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payments extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $fillable = [
        'customer_id',
        'contract_id',
        'due_date',
        'payment_amount',
        'payment_method',
        'payment_status',
        'paid_at',
        'payment_description',
        'invoice_number',
        'annex_id',
    ];

    protected $dates = [
        'due_date',
        'paid_at',
    ];

    public function customer()
    {
        return $this->belongsTo(client::class, 'customer_id');
    }

    public function contract()
    {
        return $this->belongsTo(contracts::class, 'contract_id');
    }

    public function postponementRequests()
    {
        return $this->hasMany(PostponementRequest::class, 'payment_id');
    }

    public function getPaymentNumberAttribute()
    {
        // Get all payments for this contract ordered by due date and then by ID
        $payments = self::where('contract_id', $this->contract_id)
                      ->orderBy('due_date', 'asc')
                      ->orderBy('id', 'asc')
                      ->get();
        
        // Find the position of the current payment in the ordered list
        foreach ($payments as $index => $payment) {
            if ($payment->id === $this->id) {
                return $index + 1; // Add 1 because array indices start at 0
            }
        }
        
        // Fallback in case the payment isn't found (shouldn't happen)
        return null;
    }
    
    /**
     * Get the total number of payments for this contract
     */
    public function getTotalPaymentsAttribute()
    {
        return self::where('contract_id', $this->contract_id)->count();
    }

    public function getContractNumberAttribute()
    {
        return $this->contract ? $this->contract->contract_number : null;
    }
}
