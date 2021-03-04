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
        'icon_class'
    ];

    public static function search($search)
    {
        if (strlen($search) > 2) {
            if (strlen($search) <= 7 && iconv_strlen($search) <= 5) {
                return self::selectRaw("account_id, entity_id, entity, searchable")
                    ->whereRaw("searchable LIKE '%$search%'");
            } else {
                $search = preg_replace("/[^\w\x7F-\xFF\s]/", " ", strval($search));
                $search = "+*$search*";

                //return self::selectRaw("account_id, entity_id, entity, searchable, MATCH (searchable) AGAINST ('+*$search*' IN BOOLEAN MODE) `match`")
                return self::selectRaw("account_id, entity_id, entity, searchable")
                    ->whereRaw("MATCH (searchable) AGAINST ('$search' IN BOOLEAN MODE)");
            }
        } else {
            die();
        }
    }

    public function model() {
        return $this->morphTo(null, 'entity', 'entity_id');
    }
}
