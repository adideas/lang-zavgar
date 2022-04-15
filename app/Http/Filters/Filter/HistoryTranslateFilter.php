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

    public function dateBetween($data): void {
        if (is_array($data)) {
            $timezone = request()->timezone ?? 0;
            $data = array_map(
                fn($x) => date("Y-m-d",($x + $timezone) / 1000),
                $data
            );

            $this->builder->whereBetween("date", $data);
        }
    }
}
