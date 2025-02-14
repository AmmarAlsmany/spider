<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'client_id',
        'visit_date',
        'visit_time'
    ];

    public function visit()
    {
        return $this->belongsTo(VisitSchedule::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
