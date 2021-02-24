<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden   = [
        'password',
        'remember_token',
    ];

    protected $casts    = [
        'email_verified_at' => 'datetime',
    ];

    public function access()
    {
        return $this->hasMany(UserAccess::class, 'user_id', 'id');
    }
}
