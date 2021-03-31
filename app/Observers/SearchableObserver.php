<?php

namespace App\Observers;

use App\Models\Search;
use App\Models\Translate;
use Illuminate\Database\Eloquent\Model;

class SearchableObserver
{
    public function created(Model $model)
    {
        $this->setSearch($model);
    }

    public function updated(Model $model)
    {
        $this->setSearch($model);
    }

    public function restored(Model $model)
    {
        $this->setSearch($model);
    }

    public function deleting(Model $model)
    {
        // Search::where('entity_id', $model->id)->where('entity', $model->getMorphClass())->delete();
    }

    ///////////////
    ///////////////
    ///////////////
    ///////////////

    private function setSearch(Model $model)
    {
        try {
            if (get_class($model) == Translate::class) {
                $translate = json_decode(json_encode($model), true);
                $keys      = array_values(array_filter(array_keys($translate), fn($x) => intval($x) > 0));
                foreach ($keys as $_ => $key) {
                    $_data = [
                        'entity' => $model->getMorphClass(),
                        'entity_id' => $model->id,
                        'language_id' => $key
                    ];
                    $data = [
                        'searchable' => $this->convertString($model->{$key}),
                        'entity' => $model->getMorphClass(),
                        'entity_id' => $model->id,
                        'language_id' => $key
                    ];
                    if (str_replace(' ', '', $model->{$key})) {
                        Search::updateOrCreate($_data, $data);
                    }
                }
            } else {
                $_data = [
                    'entity' => $model->getMorphClass(),
                    'entity_id' => $model->id,
                    'language_id' => 'name'
                ];
                $data = [
                    'searchable' => $this->convertString($model->name),
                    'entity' => $model->getMorphClass(),
                    'entity_id' => $model->id,
                    'language_id' => 'name'
                ];
                if (str_replace(' ', '', $model->name)) {
                    Search::updateOrCreate($_data, $data);
                }

                $_data = [
                    'entity' => $model->getMorphClass(),
                    'entity_id' => $model->id,
                    'language_id' => 'description'
                ];
                $data = [
                    'searchable' => $this->convertString($model->description),
                    'entity' => $model->getMorphClass(),
                    'entity_id' => $model->id,
                    'language_id' => 'description'
                ];
                if (str_replace(' ', '', $model->description)) {
                    Search::updateOrCreate($_data, $data);
                }
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

    }

    private function convertString($string)
    {
        $string = strval($string);
        $string = mb_strtolower($string);
        $string = preg_replace("/[^\w\x7F-\xFF\s]/", " ", $string);
        $string .= " ";

        return $string;
    }
}
