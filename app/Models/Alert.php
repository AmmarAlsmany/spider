<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alert extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'type',
        'contract_id',
        'message',
        'status'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function contract()
    {
        return $this->belongsTo(contracts::class);
    }
}
