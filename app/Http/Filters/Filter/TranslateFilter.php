<?php

namespace App\Http\Filters\Filter;

use App\Http\Filters\QueryFilter;

class TranslateFilter extends QueryFilter
{
    public function key_id(int $key_id) {
        $this->builder->where('key_id', $key_id);
    }
}
