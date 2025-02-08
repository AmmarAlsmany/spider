<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostponementRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'requested_date',
        'status',
        'reason',
        'approved_at',
        'approved_by'
    ];

    public function payment()
    {
        return $this->belongsTo(payments::class, 'payment_id');
    }
}
