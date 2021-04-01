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

        function without_symbol($str)
        {
            $str = str_replace(' ', '', $str);

            return $str;
        }

        foreach (array_keys(array_diff($original, $modified)) as $_ => $key) {
            $new            = $modified[$key];
            $symbol_replace = [',', '.', '-', '_', '(', ')', '!', '?'];
            $data           = explode(' ', str_replace($symbol_replace, ' ', $original[$key]));

            foreach ($data as $__ => $rem) {
                foreach (mb_str_split($rem, 2) as $index => $rem_) {
                    $new = str_replace(isset($rem[$index + 1]) ? $rem[$index] . $rem[$index + 1] : $rem[$index], '', $new);
                }
            }

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
