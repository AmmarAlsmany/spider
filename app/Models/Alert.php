<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    
    public function contract(): BelongsTo
    {
        return $this->belongsTo(contracts::class);
    }

    public function readByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'alert_reads')
            ->withTimestamps();
    }

    public function isReadByUser($userId): bool
    {
        return $this->readByUsers()->where('user_id', $userId)->exists();
    }
}
