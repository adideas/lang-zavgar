<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryTranslate extends Model
{
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
        'html'
    ];
}
