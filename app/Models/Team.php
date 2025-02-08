<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\VisitSchedule;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'team_leader_id',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'team_members')
                    ->withTimestamps();
    }

    public function visitSchedules()
    {
        return $this->hasMany(VisitSchedule::class);
    }
}
