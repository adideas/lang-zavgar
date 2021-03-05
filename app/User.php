<?php

namespace App;

use App\Models\Access;
use App\Models\Translate;
use App\Models\UserAccess;
use App\Traits\Searchable;
use Composer\Cache;
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
    use HasApiTokens, Notifiable, SoftDeletes, Searchable;

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

    public function getAccess() {
        return \cache()->rememberForever('getAccess_user_'.$this->id, function () {
            $_access = [
                'root' => false,
                'store' => [],
                'show' => [],
                'update' => [],
                'destroy' => [],
                'translate' => [
                    'show' => [],
                    'update' => []
                ]
            ];

            foreach ($this->access as $key => $item) {
                if ($item->entity == Translate::class) {
                    array_push($_access['translate'][$item->data[0]], $item->data[1]);
                } else {
                    if ($item->entity == User::class) {
                        if ($item->data[0] == 'root') {
                            $_access['root'] = true;
                        } else {
                            array_push($_access[$item->data[0]], $item->entity);
                            array_push($_access['show'], $item->entity);
                        }
                    } else {
                        array_push($_access[$item->data[0]], $item->entity);
                        array_push($_access['show'], $item->entity);
                    }
                }
                //
            }
            $_access['show'] = array_values(array_unique($_access['show']));
            return $_access;
        });
    }
}
