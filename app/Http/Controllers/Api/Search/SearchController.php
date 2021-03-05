<?php

namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Controller;
use App\Models\Key;
use App\Models\Search;
use App\Models\Translate;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request) {
        $entity = [];
        if ($request->input('filter', []) && count($request->input('filter', []))) {
            $entity = $request->input('filter', []);
        }

        $access = [
            ...auth()->user()->getAccess()['show'],
            ...[
                Translate::class,
                Key::class
            ]
        ];

        if(!auth()->user()->getAccess()['root']) {
            $entity = array_intersect($access, $entity);
        }

        $search = Search::search($request->input('search'))->with('model')->orderByDesc('entity');
        if (count($entity)) {
            $search->whereIn('entity', $entity);
        }
        $search = $search->get();
        if (count($search) < 1 && strlen($request->input('search')) > 3) {
            $new_search = $request->input('search');
            $new_search = implode(' ', array_filter(explode(' ', $new_search), fn($x) => iconv_strlen($x) >= 3));

            $search = Search::search($new_search)->with('model')->orderByDesc('entity');
            if (count($entity)) {
                $search->whereIn('entity', $entity);
            }
            $search = $search->get();
        }
        return $search;
    }
}
