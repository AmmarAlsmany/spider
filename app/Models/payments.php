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
}
