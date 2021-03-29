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
            $this->execute($data, $connection, $message, $this);
        }
    }

    private function execute(array $data, ConnectionInterface $connection, MessageInterface $message, WebSocketHandler $context) {
        $namespace = 'App\\Http\\Controllers\\WS\\Events\\' . $data['event'];
        if (class_exists($namespace)) {
           $class = new $namespace($data, $connection, $message, $context);
           if(method_exists($class, 'handle')) {
               if ($return = $class->handle($data)) {
                   $data['data'] = is_array($return) ? $return : [$return];
                   $connection->send(json_encode($data));
               }
           }
        } else {
            dump('class ' . $namespace);
        }
    }
}


