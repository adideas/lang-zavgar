<?php

namespace App\Http\Resources\Api\Translate;

use App\Models\Language;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslateResource extends JsonResource
{

    public function toArray($request)
    {
        $filter_language = false;
        if(request()->has('filter')) {
          $request_filter = json_decode(request()->input('filter'), true);
          if(isset($request_filter['language']) && count($request_filter['language'])) {
              $filter_language = $request_filter['language'];
          }
        }

        return Language::when(
            $filter_language,
            fn($builder) => $builder->whereIn('id', $filter_language)
        )
            ->pluck('id')
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
