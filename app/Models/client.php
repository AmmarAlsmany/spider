<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class client extends Authenticatable
{
    use Notifiable, HasFactory, SoftDeletes;
    protected $guard = 'client';

    protected $table = 'clients';
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'phone',
        'mobile', 
        'zip_code',
        'tax_number',
        'address', 
        'city', 
        'state', 
        'role',
        'sales_id',
        'last_login_at', 
        'last_login_ip'
    ];
    protected $hidden = ['password'];

    public function contracts()
    {
        return $this->hasMany(contracts::class, 'customer_id');
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function tickets()
    {
        return $this->hasMany(tikets::class, 'customer_id');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
