<?php

namespace App\Http\Filters\Filter;

use App\Http\Filters\QueryFilter;

class HistoryTranslateFilter extends QueryFilter
{
    public function user_id(string $id) : void
    {
        if ($id) {
            $this->builder->where('user_id', $id);
        }
    }
}
