<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class branchs extends Model
{
    use HasFactory;
    protected $table = 'branchs';
    protected $fillable = [
        'branch_name',
        'branch_manager_name',
        'branch_manager_phone',
        'branch_address',
        'branch_city',
        'contracts_id',
        'annex_id',
    ];

    // Relationships

    public function contract()
    {
        return $this->belongsTo(contracts::class, 'contracts_id');
    }

    public function annex()
    {
        return $this->belongsTo(ContractAnnex::class, 'annex_id');
    }
}
