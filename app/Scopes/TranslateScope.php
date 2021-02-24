<?php

namespace App\Scopes;

use App\Models\Language;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TranslateScope implements Scope
{
    protected array $fillable = [];

    public function __construct(array $fillable)
    {
        $this->fillable = $fillable;
    }

    public function apply(Builder $builder, Model $model)
    {
        $languages = Language::pluck('id')->transform(fn($x) => '0' . $x)
            ->merge($this->fillable)
            ->toArray();
        $builder->select($languages);
    }
}
