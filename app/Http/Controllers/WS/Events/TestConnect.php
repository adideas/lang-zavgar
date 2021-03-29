<?php

namespace App\Http\Controllers\WS\Events;

use App\Http\Controllers\WS\Service\PusherExtends;

class TestConnect extends PusherExtends
{

    public function handle(array $data = null)
    {
        return [
            'test'
        ];
    }
}
