<?php

namespace App\Http\Controllers\WS\Events;

use App\Http\Controllers\WS\Service\PusherExtends;
use App\Models\File;
use App\Models\Key;

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
            if($message['data']['type'] == 'key') {
                return Key::where('parent', $message['data']['id'])
                    ->withCount('translates')
                    ->orderBy('translates_count')
                    ->get();
            }
        }
        return File::whereNull('parent')->get();
    }
}
