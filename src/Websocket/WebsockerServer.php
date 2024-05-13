<?php

declare(strict_types=1);

namespace App\Websocket;

use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\LoopInterface;
use React\Socket\SocketServer;

class WebsockerServer extends IoServer
{
    public static function create(MessageComponentInterface $server, LoopInterface $loop, $port = 80, $address = '0.0.0.0')
    {
        $component = new HttpServer(
            new WsServer(
               $server
            )
        );

        $socket = new SocketServer($address . ':' . $port, [], $loop);

        return new static($component, $socket, $loop);
    }
}
