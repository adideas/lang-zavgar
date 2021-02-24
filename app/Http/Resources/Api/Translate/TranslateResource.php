<?php

namespace App\Http\Resources\Api\Translate;

use App\Models\Language;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslateResource extends JsonResource
{

    public function toArray($request)
    {
        return Language::pluck('id')
            ->transform(fn($x) => ['0' . $x => $this->{'0' . $x}])
            ->collapse()
            ->merge(
                [
                    'id' => $this->id,
                    'name' => $this->key->name,
                    'description' => $this->key->description,
                ]
            );
    }
}
