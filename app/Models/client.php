<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\DatabaseNotification;

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

    public function payments()
    {
        return $this->hasMany(payments::class, 'customer_id');
    }

    /**
     * Get the entity's notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    /**
     * Get the entity's unread notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function unreadNotifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc');
    }
}
