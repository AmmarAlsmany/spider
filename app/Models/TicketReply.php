<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'reply'
    ];

    /**
     * Get the ticket that owns the reply.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class);
    }

    /**
     * Get the user who created the reply.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
