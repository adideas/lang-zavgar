<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Models\Helpers\FileTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes, Filterable, FileTrait;

    protected $fillable = [
        'name',
        'description',
        'path',
        'is_file',
        'parent',
        'file_type'
    ];

    public function parent_file()
    {
        return $this->hasOne(File::class, 'id', 'parent')->withTrashed();
    }

    public function children()
    {
        return $this->hasMany(File::class, 'parent', 'id')->withTrashed();
    }

    public function type()
    {
        return $this->hasOne(FileType::class, 'id', 'file_type');
    }
}
