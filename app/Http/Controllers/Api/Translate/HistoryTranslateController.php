<?php

namespace App\Http\Controllers\Api\Translate;

use App\Http\Controllers\Controller;
use App\Http\Filters\Filter\HistoryTranslateFilter;
use App\Http\Resources\HistoryTranslateResource;
use App\Models\HistoryTranslate;
use Illuminate\Http\Request;

class HistoryTranslateController extends Controller
{
    public function index(Request $request, HistoryTranslateFilter $filter)
    {
        if ($request->has('date') && $request->has('user_id')) {
            return HistoryTranslate::where($request->all())->get();
        }

        $data = HistoryTranslate::distinct('date', 'user_id')
            ->filter($filter)
            ->with('user')
            ->paginate($request->to ?: 10);

        return HistoryTranslateResource::collection($data);
        //
    }
}
