<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Search extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'searchable',
        'entity',
        'entity_id',
        'icon_class',
        'language_id'
    ];

    public static function search($search)
    {
        $search = preg_replace("/[^\w\x7F-\xFF\s]/", "%", strval($search));

        // Прямое попадание
        if(Search::where('searchable', 'LIKE', "$search ")->count() > 0) {
            return self::selectRaw("id, entity_id, entity, searchable, language_id")
                ->orWhereRaw("searchable LIKE '$search '");
        }

        // Почти точное попадание
        if(Search::where('searchable', 'LIKE', "%".str_replace(' ', '%', $search)."%")->count() > 0) {
            return self::selectRaw("id, entity_id, entity, searchable, language_id")
                ->orWhereRaw("searchable LIKE '%$search%'");
        }

        // trait.PartController.store.add
        $search = str_replace('.',' ', $search);

        $search = "+*$search*";

        return self::selectRaw("id, entity_id, entity, searchable, language_id")
            ->whereRaw("MATCH (searchable) AGAINST ('$search' IN BOOLEAN MODE)")
            ->orWhereRaw("searchable LIKE '%$search%'");

    }

    public function model() {
        return $this->morphTo(null, 'entity', 'entity_id');
    }
}
