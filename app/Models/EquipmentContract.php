<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EquipmentType;

class EquipmentContract extends Model
{
    protected $fillable = [
        'contract_id',
        'equipment_type',
        'equipment_model',
        'equipment_quantity',
        'equipment_description',
        'unit_price',
        'total_price',
        'vat_amount',
        'total_with_vat',
    ];

    public function contract()
    {
        return $this->belongsTo(contracts::class, 'contract_id');
    }

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class, 'equipment_type');
    }
}
