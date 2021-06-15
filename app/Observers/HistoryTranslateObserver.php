<?php

namespace App\Observers;

use App\classes\DiffText\DiffText;
use App\Models\HistoryTranslate;
use Illuminate\Database\Eloquent\Model;
use function RingCentral\Psr7\str;

class HistoryTranslateObserver
{
    public function updated(Model $model)
    {
        $keys = array_filter($model->getFillable(), fn($x) => intval($x));

        $original = array_slice_key($model->getOriginal(), $keys);
        $modified = array_slice_key($model->getAttributes(), $keys);

        foreach (array_keys(array_replace(array_diff($original, $modified), array_diff($modified, $original))) as $_ => $key) {
            HistoryTranslate::create(
                array_replace([
                    'user_id'                        => $model->user_id,
                    'language_id'                    => intval($key),
                    'key_id'                         => $model->key_id,
                    'file_id'                        => $model->file_id,
                    'old'                            => $original[$key],
                    'new'                            => $modified[$key],
                    'date'                           => now()
                ],DiffText::text($original[$key], $modified[$key]))
            );
        }
    }
}
