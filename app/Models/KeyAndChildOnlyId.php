<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class KeyAndChildOnlyId extends Key
{
    use SoftDeletes;

    protected $table = 'keys';

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($builder) {
            $builder->select('id','parent')->with('keys');
        });
    }


    public function keys()
    {
        return $this->hasMany(KeyAndChild::class, 'parent', 'id');
    }
}
