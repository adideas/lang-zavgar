<?php

namespace App\Http\Controllers\Api\Translate;

use App\Http\Controllers\Controller;
use App\Http\Resources\HistoryTranslateResource;
use App\Models\HistoryTranslate;
use Illuminate\Http\Request;

class HistoryTranslateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('date') && $request->has('user_id')) {
            return HistoryTranslate::where($request->all())->get();
        }

        $data = HistoryTranslate::distinct('date', 'user_id')->with('user')->get();

        return HistoryTranslateResource::collection($data);
        //
    }
}
