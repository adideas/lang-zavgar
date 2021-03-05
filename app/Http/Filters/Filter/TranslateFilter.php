<?php

namespace App\Http\Filters\Filter;

use App\Http\Filters\QueryFilter;
use App\Models\Language;

class TranslateFilter extends QueryFilter
{
    public function key_id(int $key_id)
    {
        $this->builder->where('key_id', $key_id);
    }

    public function filter(array $filter)
    {
        if (isset($filter['select']) && $filter['select']) {
            switch ($filter['select']) {
                case 'is_not_translate':
                    Language::when(
                        isset($filter['language']) && count($filter['language']) > 0,
                        fn($builder) => $builder->whereIn('id', $filter['language'])
                    )->pluck('id')
                        ->map(fn($x) => '0' . $x)
                        ->each(fn($lang) => $this->builder->orWhereNull($lang));
                    break;
                case 'is_translate':

                    Language::when(
                        isset($filter['language']) && count($filter['language']) > 0,
                        fn($builder) => $builder->whereIn('id', $filter['language'])
                    )->pluck('id')
                        ->map(fn($x) => '0' . $x)
                        ->each(fn($lang) => $this->builder->whereNotNull($lang));

                    break;
                default:
                    break;
            }
        }
    }
}
