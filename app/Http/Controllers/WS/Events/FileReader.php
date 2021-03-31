<?php

namespace App\Http\Controllers\WS\Events;

use App\Http\Controllers\WS\Service\PusherExtends;
use App\Models\File;
use App\Models\Key;
use App\Models\Language;
use App\Models\Translate;

class FileReader extends PusherExtends
{

    public function handle(array $message = [])
    {
        if ($message['data'] && isset($message['data']['id'])) {
            if ($message['data']['type'] == 'folder') {
                return File::where('parent', $message['data']['id'])->get();
            }
            if ($message['data']['type'] == 'file') {
                return Key::whereNull('parent')
                    ->where('file_id', $message['data']['id'])
                    ->withCount('translates')
                    ->orderBy('translates_count')
                    ->get();
            }
            if ($message['data']['type'] == 'key') {
                $key       = Key::where('parent', $message['data']['id'])->count();
                $translate = Translate::where('key_id', $message['data']['id'])->first();
                if ($translate) {
                    $language = Language::get()
                        ->transform(
                            function ($x) use ($translate) {
                                $x->translate_id = $translate->id;
                                if (isset($translate['0' . $x->id])) {
                                    $x->translate = $translate['0' . $x->id];
                                } else {
                                    $x->translate = "";
                                }

                                return $x;
                            }
                        );

                    return [
                        'language' => $language,
                    ];
                }

                if ($key) {
                    return Key::where('parent', $message['data']['id'])
                        ->withCount('translates')
                        ->orderBy('translates_count')
                        ->get();
                }

                return [];
            }
        }

        return File::whereNull('parent')->get();
    }
}
