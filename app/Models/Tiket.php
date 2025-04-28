<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tiket extends Model
{
    protected $fillable = [
        'tiket_number',
        'tiket_title',
        'tiket_description',
        'status',
        'priority',
        'ticket_type',
        'solver_id',
        'customer_id',
        'created_by',
        'solved_at'
    ];

    protected $dates = [
        'solved_at'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(client::class, 'customer_id');
    }

    public function solver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solver_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    public function ticketReplies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }
}
