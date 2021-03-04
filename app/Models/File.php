<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Models\Helpers\FileTrait;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes, Filterable, FileTrait, Searchable;

    protected $fillable = [
        'name',
        'description',
        'path',
        'is_file',
        'parent',
        'file_type'
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function (File $file) {

            // стираем подчиненные файлы
            $files = $file->children;
            if (count($files)) {
                foreach ($files as $_key => $file) {
                    $file->delete();
                }
            }

            // стираем подчиненные ключи только основные дочерние сами
            $keys = $file->parent_keys;
            if (count($keys)) {
                foreach ($keys as $_key => $key) {
                    $key->delete();
                }
            }
        });
    }

    public function parent_file()
    {
        return $this->hasOne(File::class, 'id', 'parent')->withTrashed();
    }

    public function children()
    {
        return $this->hasMany(File::class, 'parent', 'id')->withTrashed();
    }

    public function keys()
    {
        return $this->hasMany(Key::class, 'file_id', 'id')->withTrashed();
    }

    public function parent_keys()
    {
        return $this->hasMany(Key::class, 'file_id', 'id')->withTrashed()->whereNull('parent');
    }

    public function translates()
    {
        return $this->hasMany(Translate::class, 'file_id', 'id')->withTrashed();
    }

    public function type()
    {
        return $this->hasOne(FileType::class, 'id', 'file_type');
    }
}
