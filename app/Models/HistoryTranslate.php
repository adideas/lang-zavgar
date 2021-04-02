<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static distinct(array ...$select) : Builder
 */
class HistoryTranslate extends Model
{
    use Filterable;
    protected $fillable = [
        'user_id',
        'language_id',
        'key_id',
        'file_id',
        'old',
        'new',
        'count_symbol_with_space',
        'count_symbol_without_space',
        'count_new_symbol_with_space',
        'count_new_symbol_without_space',
        'date',
        'html',
    ];

    public function scopeDistinct(Builder $builder, ...$select) : Builder
    {
        if (count($select) == 1 && isset($select[0]) && is_array($select[0])) {
            $select = $select[0];
        }

        return $builder->selectRaw("DISTINCT " . '`' . implode('`,`', $select) . '`');
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
