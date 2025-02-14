<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitSchedule extends Model
{
    use HasFactory;

    protected $table = 'visit_schedules';

    protected $fillable = [
        'contract_id',
        'branch_id',
        'team_id',
        'visit_date',
        'visit_time',
        'status',
        'visit_number',
        'visit_type',
        'change_requested_at',
    ];

    protected $casts = [
        'visit_number' => 'integer',
    ];

    public function contract()
    {
        return $this->belongsTo(contracts::class, 'contract_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function branch()
    {
        return $this->belongsTo(branchs::class, 'branch_id');
    }

    public function report()
    {
        return $this->hasOne(VisitReport::class, 'visit_id');
    }

    public function changeRequest()
    {
        return $this->hasOne(VisitChangeRequest::class, 'visit_id');
    }
}
