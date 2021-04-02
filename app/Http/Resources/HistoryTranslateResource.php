<?php

namespace App\Http\Resources;

use App\Models\HistoryTranslate;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryTranslateResource extends JsonResource
{
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'date'      => $this->date,
            'user_id'   => $this->user->id,
            'user_name' => $this->user->name,
            'count_with_space'     => HistoryTranslate::where(
                [
                    'user_id' => $this->user->id,
                    'date' => $this->date
                ]
            )->sum('count_new_symbol_with_space'),
            'count_without_space'     => HistoryTranslate::where(
                [
                    'user_id' => $this->user->id,
                    'date' => $this->date
                ]
            )->sum('count_new_symbol_without_space'),
        ];
    }
}
