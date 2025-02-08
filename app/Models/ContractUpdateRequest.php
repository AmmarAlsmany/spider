<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractUpdateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'client_id',
        'request_details',
        'status',
        'response',
    ];

    // Relationship with Contract
    public function contract()
    {
        return $this->belongsTo(contracts::class, 'contract_id');
    }

    // Relationship with Client
    public function client()
    {
        return $this->belongsTo(client::class, 'client_id');
    }
}
