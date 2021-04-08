<?php

namespace App\Http\Filters\Filter;

use App\Http\Filters\QueryFilter;
use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;

class TranslateFilter extends QueryFilter
{
    public function key_id(int $key_id)
    {
        $this->builder->where('key_id', $key_id);
    }

    public function filter(array $filter)
    {
        $this->builder->where(
            function (Builder $builder) use ($filter) {
                if (isset($filter['select'])) {
                    Language::when(
                        isset($filter['language']) && count($filter['language']) > 0,
                        fn($builder) => $builder->whereIn('id', $filter['language'])
                    )->each(
                        function (Language $language) use (&$builder, $filter) {
                            if($filter['select'] == 'is_not_translate') {
                                $builder->orWhereNull('0' . $language->id);
                            }
                            if($filter['select'] == 'is_translate') {
                                $builder->whereNotNull('0' . $language->id);
                            }
                        }
                    );
                }
            }
        );
    }
}
