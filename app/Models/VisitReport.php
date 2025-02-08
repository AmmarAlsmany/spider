<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'time_in',
        'time_out',
        'visit_type',
        'target_insects',
        'pesticides_used',
        'recommendations',
        'customer_notes',
        'customer_signature',
        'phone_signature',
        'created_by'
    ];

    protected $casts = [
        'target_insects' => 'array',
        'pesticides_used' => 'array',
    ];

    public function visit()
    {
        return $this->belongsTo(VisitSchedule::class, 'visit_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function visitSchedule()
    {
        return $this->belongsTo(VisitSchedule::class, 'visit_id');
    }
}
