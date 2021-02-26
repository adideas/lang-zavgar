<?php

namespace App\Http\Controllers\Api\Translate;

use App\Http\Controllers\Controller;
use App\Http\Filters\Filter\TranslateFilter;
use App\Http\Requests\Api\Translate\TranslateRequest;
use App\Http\Resources\Api\Translate\TranslateResource;
use App\Models\Translate;
use Illuminate\Http\Request;

class TranslateController extends Controller
{
    public function index(Request $request, TranslateFilter $translateFilter)
    {
        $translate = Translate::filter($translateFilter)->with('key:id,name,description')->get();

        return TranslateResource::collection($translate);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(TranslateRequest $request, Translate $translate)
    {
        //
        $translate->update(
            [
                '0' . $request->input('language_id') => $request->input('value', null),
            ]
        );
    }

    public function destroy($id)
    {
        //
    }
}
