<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function equipmentContracts()
    {
        return $this->hasMany(EquipmentContract::class);
    }
}
