<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Models\Helpers\FileTrait;
use App\Observers\HistoryTranslateObserver;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Translate extends Model
{
    use SoftDeletes, FileTrait, Filterable, Searchable;

    public $searchable = [
        'key_id' => 'key.name,description',
        'file_id' => 'file.name,description,path'
    ];

    protected $fillable = [
        'key_id',
        'file_id',
        'user_id',
    ];

    const FILLABLE = [
        'key_id',
        'file_id',
        'user_id',
    ];

    //
    static protected $fillableScope = [
        'key_id',
        'file_id',
        'user_id',
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();
        self::observe(new HistoryTranslateObserver());
    }

    public function getFillable(): array
    {
        return collect(DB::select("SHOW COLUMNS FROM " . 'translates'))->pluck('Field')
            ->filter(fn($x) => is_numeric($x))
            ->merge($this->fillable)
            ->toArray();
    }

    public function file()
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    public function key()
    {
        return $this->hasOne(Key::class, 'id', 'key_id');
    }
}
