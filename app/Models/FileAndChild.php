<?php

namespace App\Models;

use App\Http\Filters\Filterable;

class FileAndChild extends File
{
    protected $table = 'files';

    use Filterable;

    protected $fillable = [
        'name',
        'description',
        'path',
        'is_file',
        'parent',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($builder) {
            $builder->with('files','keys');
        });
    }

    public function files()
    {
        return $this->hasMany(FileAndChild::class, 'parent', 'id');
    }

    public function keys()
    {
        return $this->hasMany(KeyAndChild::class, 'file_id', 'id')->whereNull('parent');
    }
}
