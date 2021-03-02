<?php

namespace App\Models;

use App\classes\FileCoder\NOPUBLISHFileCoder;
use App\Http\Filters\Filterable;
use App\Models\Helpers\FileTrait;
use App\Observers\TranslateObserver;
use App\Scopes\TranslateScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Translate extends Model
{
    use SoftDeletes, FileTrait, Filterable;

    protected $fillable = [
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
