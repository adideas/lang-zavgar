<?php

namespace App\Observers;

use App\Models\File;
use App\Models\Key;
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
        if (get_class($model) == Translate::class) {
            // TODO пока что в разработке
        } else {
            Search::where('entity_id', $model->id)
                ->where('entity', $model->getMorphClass())
                ->delete();
        }
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
                if (get_class($model) == Key::class) {
                   if(count($model->keys)) {
                       return;
                   }
                }
                if (get_class($model) == File::class) {
                    if(count($model->children)) {
                        return;
                    }
                }

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

                $_data = [
                    'entity' => $model->getMorphClass(),
                    'entity_id' => $model->id,
                    'language_id' => 'path'
                ];

                $text = '';
                if(get_class($model) == Key::class) {
                    $text = $model->file->name . "." . implode('.', is_array($model->indexed) ? $model->indexed : json_decode($model->indexed, true));
                }
                if(get_class($model) == File::class) {
                    $text = $model->path;
                }

                $data = [
                    'searchable' => $text,
                    'entity' => $model->getMorphClass(),
                    'entity_id' => $model->id,
                    'language_id' => 'path'
                ];
                if (str_replace(' ', '', $text)) {
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
