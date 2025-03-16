<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contracts_types extends Model
{
    use HasFactory;
    protected $table = 'contracts_types';
    protected $fillable = ['name', 'description', 'status', 'Warranty', 'Visit', 'avator'];
    
    /**
     * Get the contracts associated with this contract type.
     */
    public function contracts()
    {
        return $this->hasMany(contracts::class, 'contract_type');
    }
}
