<?php

namespace App\Observers;

use App\Models\Search;
use Illuminate\Database\Eloquent\Model;

class SearchableObserver
{
    public function created(Model $model)
    {
        $this->setSearch($model, $this->getString($model));
    }

    public function updated(Model $model)
    {
        $this->setSearch($model, $this->getString($model));
    }

    public function restored(Model $model)
    {
        $this->setSearch($model, $this->getString($model));
    }

    public function deleting(Model $model)
    {
        Search::where('entity_id', $model->id)->where('entity', $model->getMorphClass())->delete();
    }

    ///////////////
    ///////////////
    ///////////////
    ///////////////

    private function setSearch(Model $model, $string)
    {
        if (strlen($string) > 3) {
            Search::updateOrCreate(
                [
                    'entity'    => $model->getMorphClass(),
                    'entity_id' => $model->id,
                ],
                [
                    'searchable' => $string
                ]
            );
        }
    }

    private function getString(Model $model) : string
    {
        $string = ' ';

        foreach ($model->getAttributes() as $key => $value) {
            if (gettype($value) == 'string' && strtotime($value) < 1500000000) {
                $string .= $this->convertString($value) . ' ';
            }
        }

        if (isset($model->searchable)) {
            foreach ($model->searchable as $relation => $params) {
                try {
                    $params = explode('.', $params);
                    if (count($params) == 2) {
                        $data =
                            $model->{$params[0]}()->select(['id', ...explode(',', $params[1])])->get()->toArray()[0];
                        foreach ($data as $key => $value) {
                            try {
                                if (gettype($value) == 'string' && strtotime($value) < 1500000000) {
                                    $string .= $this->convertString($value) . ' ';
                                }
                            } catch (\Exception $e) {
                            }
                        }
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return $string;
    }

    private function convertString($string)
    {
        return strval(preg_replace("/[^\w\x7F-\xFF\s]/", " ", strval($string)) . " ");
    }
}
