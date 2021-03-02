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
        static::deleting(function (Key $key) {
            // стираем подчиненные переводы
            $translates = $key->translates;
            if (count($translates)) {
                foreach ($translates as $_key => $translate) {
                    $translate->delete();
                }
            }
            $keys = $key->keys;
            if (count($keys)) {
                foreach ($keys as $_key => $key) {
                    $key->delete();
                }
            }
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
        return $this->hasMany(Key::class, 'parent', 'id');
    }

    public function file() {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    public function translates()
    {
        return $this->hasMany(Translate::class, 'key_id', 'id')->withTrashed();
    }
}
