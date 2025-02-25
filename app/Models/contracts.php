<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ContractUpdateRequest;
use App\Models\contracts_types;

class contracts extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contracts';
    protected $fillable = [
        'contract_number',
        'contract_start_date',
        'contract_end_date',
        'is_finish',
        'contract_status',
        'contract_type',
        'contract_description',
        'warranty',
        'number_of_visits',
        'customer_id',
        'sales_id',
        'Property_type',
        'contract_price',
        'Payment_type',
        'number_Payments',
        'is_multi_branch',
        'rejection_reason',
    ];

    public function salesRepresentative()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function customer()
    {
        return $this->belongsTo(client::class, 'customer_id');
    }

    public function branchs()
    {
        return $this->hasMany(branchs::class, 'contracts_id');
    }

    public function visitSchedules()
    {
        return $this->hasMany(VisitSchedule::class, 'contract_id');
    }

    public function payments()
    {
        return $this->hasMany(payments::class, 'contract_id');
    }

    public function updateRequests()
    {
        return $this->hasMany(ContractUpdateRequest::class, 'contract_id');
    }

    public function type()
    {
        return $this->belongsTo(contracts_types::class, 'contract_type');
    }

    public function history()
    {
        return $this->hasMany(ContractHistory::class, 'contract_id')->orderBy('created_at', 'desc');
    }

    public function visits()
    {
        return $this->hasMany(VisitSchedule::class, 'contract_id');
    }

    public function annexes()
    {
        return $this->hasMany(ContractAnnex::class, 'contract_id');
    }

    public function equipment()
    {
        return $this->hasOne(EquipmentContract::class, 'contract_id');
    }
}
