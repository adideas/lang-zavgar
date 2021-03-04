<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Key extends Model
{
    use SoftDeletes, Searchable;

    public $searchable = [
        'file_id' => 'file.name,description,path'
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(
            function (Key $key) {
                if ($key->parent_key) {
                    Translate::where(
                        [
                            'key_id'  => $key->parent_key->id,
                            'file_id' => $key->parent_key->file_id,
                        ]
                    )->forceDelete();
                }
                $string = 'file_controller_create_key_translate_'.auth()->user()->id;
                $value = Cache::get($string, null);
                Cache::forget($string);
                if (!$value) {
                    Translate::create(
                        [
                            'key_id'  => $key->id,
                            'file_id' => $key->file_id,
                            'user_id' => auth()->user()->id,
                        ]
                    );
                }
            }
        );
        static::deleting(
            function (Key $key) {
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
            }
        );
    }

    protected $fillable = [
        'file_id',
        'name',
        'description',
        'indexed',
        'parent',
    ];

    protected $casts    = [
        'indexed' => 'array',
    ];

    public function keys()
    {
        return $this->hasMany(Key::class, 'parent', 'id');
    }

    public function parent_key()
    {
        return $this->hasOne(Key::class, 'id', 'parent');
    }

    public function file()
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    public function translates()
    {
        return $this->hasMany(Translate::class, 'key_id', 'id')->withTrashed();
    }
}
