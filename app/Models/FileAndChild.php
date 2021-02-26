<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Models\Helpers\FileTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

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
            $builder->with('children');
        });
    }

    public function children()
    {
        return $this->hasMany(FileAndChild::class, 'parent', 'id');
    }
}
