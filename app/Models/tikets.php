<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tikets extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiket_number',
        'tiket_title',
        'tiket_description',
        'status',
        'who_solved_it',
        'ticket_priority',
        'ticket_type',
        'customer_id',
    ];

    public function client_info()
    {
        return $this->belongsTo(client::class, 'customer_id');
    }

    public function whoSolvedIt()
    {
        return $this->belongsTo(User::class, 'who_solved_it');
    }
}
