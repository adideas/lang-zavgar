<?php

namespace App;

use App\Models\Access;
use App\Models\UserAccess;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Bridge\AccessToken;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Passport;

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

    public function validateForPassportPasswordGrant($password)
    {
        // DB::table('oauth_access_tokens')->where('user_id', $this->id)->delete();
        if (Hash::check($password, $this->getAuthPassword())) {
            return true;
        }

        return false;
    }

    public function findForPassport($username)
    {
        return $this->where('email', $username)->first();
    }

    public function access()
    {
        return $this->belongsToMany(
            Access::class,
            UserAccess::class,
            'user_id',
            'access_id',
            'id',
            'id'
        );
    }
}
