<?php

namespace App\Models;

class FileAndChildOnlyId extends File
{
    protected $table = 'files';

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($builder) {
            $builder->select('id', 'parent')->with('files');
        });
    }

    public function files()
    {
        return $this->hasMany(FileAndChildOnlyId::class, 'parent', 'id');
    }
}
