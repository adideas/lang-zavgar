<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Access extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        "name", "entity", "data"
    ];

    protected $casts = [
        "data" => "json"
    ];

    public function getNameAttribute()
    {
        if($this->entity == Translate::class) {

            $name = Cache::remember(Access::class . Translate::class . $this->id, 1, function () {
                $language = Language::find(intval($this->data[1]));
                return "$language->description [$language->name] {$this->data[0]}";
            });

            if ($this->attributes['name'] != $name) {
                $this->update(['name' => $name]);
            }

            return $name;
        }
        return $this->attributes['name'];
    }
}
