<?php

namespace App\Traits;

use App\Observers\SearchableObserver;

trait Searchable
{
    /**
     * @property array $searchable
     */

    public static function bootSearchable()
    {
        self::observe(new SearchableObserver);
    }
}
