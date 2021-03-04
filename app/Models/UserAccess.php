<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $hidden = ['updated_at', 'created_at'];

    protected $fillable = [
        'user_id', 'access_id'
    ];
}
