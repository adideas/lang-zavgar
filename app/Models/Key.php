<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Key extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'file_id', 'name', 'description', 'indexed', 'parent'
    ];

    protected $casts = [
        'indexed' => 'array'
    ];

    public function children()
    {
        return $this->hasMany(Key::class, 'parent', 'id');
    }

    public function file() {
        return $this->hasOne(File::class, 'id', 'file_id');
    }
}
