<?php

namespace App\Http\Resources;

use App\Models\HistoryTranslate;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoryTranslateResource extends JsonResource
{
    public static function collection($resource)
    {
        if ($resource instanceof LengthAwarePaginator) {
            return parent::collection(
                new LengthAwarePaginator($resource->items(), $resource->count(), $resource->perPage())
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
