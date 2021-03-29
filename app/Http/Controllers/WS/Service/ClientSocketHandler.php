<?php

namespace App\Http\Controllers\WS\Service;

use BeyondCode\LaravelWebSockets\WebSockets\WebSocketHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;

class ClientSocketHandler extends WebSocketHandler
{
    public function onMessage(ConnectionInterface $connection, MessageInterface $message)
    {
        parent::onMessage($connection, $message);
        $user_id = Cache::get($connection->socketId . '');
        Auth::loginUsingId($user_id);

        $data = json_decode($message->getPayload(), true);

        if ($data && isset($data['event']) && Str::after($data['event'], ':') == $data['event']) {
            $data['user_id'] = $user_id;
            $this->execute($data)
        }
    }

    private function execute(array $data) {

    }
}


