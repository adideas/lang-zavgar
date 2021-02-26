<?php
namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
/**
 * @method static filter(\App\Http\Filters\Filter\TranslateFilter $translateFilter)
 */
trait Filterable
{
    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        $filter->apply($builder);
    }
}
