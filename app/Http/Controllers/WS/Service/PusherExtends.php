<?php

namespace App\Http\Controllers\WS\Service;

use BeyondCode\LaravelWebSockets\WebSockets\WebSocketHandler;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;

abstract class PusherExtends
{
    public function __construct(array $data, ConnectionInterface $connection, MessageInterface $message, WebSocketHandler $context)
    {

    }

    abstract public function handle(array $message = []);
}
