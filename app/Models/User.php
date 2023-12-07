<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'api_token',
        'remember_token',
    ];

    // Relationships

    /**
     * Get the sent connection requests for the user.
     */
    public function sentRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Request::class, 'sender_id');
    }

    /**
     * Get the received connection requests for the user.
     */
    public function receivedRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Request::class, 'receiver_id');
    }

    /**
     * Get the connections for the user.
     */
    public function connections(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'connections', 'user_id', 'connected_user_id')
            ->withTimestamps('created_at', 'updated_at');
    }

    /**
     * Get the common connections for the user.
     */
    public function commonConnections(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CommonConnection::class, 'user_id');
    }
}
