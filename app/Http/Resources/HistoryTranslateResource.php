<?php

namespace App\Http\Resources;

use App\Http\Filters\Filter\HistoryTranslateFilter;
use App\Models\HistoryTranslate;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoryTranslateResource extends JsonResource
{
    public static function collection($resource)
    {
        if ($resource instanceof LengthAwarePaginator) {
            $count = HistoryTranslate::distinct('date', 'user_id')
                ->filter(new HistoryTranslateFilter(request()))
                ->get()->count();
            return parent::collection(
                new LengthAwarePaginator($resource->items(), $count, $resource->perPage())
            );
        }

        return parent::collection($resource);
    }

    public function toArray($request)
    {
        $history = HistoryTranslate::where(['user_id' => $this->user->id, 'date' => $this->date]);

        return [
            'date'                => $this->date,
            'user_id'             => $this->user->id,
            'user_name'           => $this->user->name,
            'count_with_space'    => $history->sum('count_new_symbol_with_space'),
            'count_without_space' => $history->sum('count_new_symbol_without_space'),
        ];
    }
}
