<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class KeyAndChild extends Key
{
    use SoftDeletes;

    protected $table = 'keys';

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($builder) {
            $builder->with('keys', 'translate');
        });
    }

    protected $fillable = [
        'file_id', 'name', 'description', 'indexed', 'parent'
    ];

    protected $casts = [
        'indexed' => 'array'
    ];

    public function keys()
    {
        return $this->hasMany(KeyAndChild::class, 'parent', 'id');
    }

    public function translate() {
        return $this->hasOne(Translate::class, 'key_id', 'id');
    }
}
