<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'path',
        'is_file',
        'parent',
    ];

    public function parent_file()
    {
        return $this->hasOne(File::class, 'id', 'parent');
    }

    public function type()
    {
        return $this->hasOne(FileType::class, 'id', 'file_type');
    }
}
