<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use React\EventLoop\Factory;
use React\Socket\Server as SocketServer;
use React\Http\Server as HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer as RatchetHttpServer;
use YourBundle\WebSocket\Chat;

$loop = Factory::create();
$socket = new SocketServer('127.0.0.1:8080', $loop);
$http = new HttpServer(new WsServer(new Chat()));

$httpServer = new RatchetHttpServer($http);

$httpServer->listen($socket);

echo "WebSocket server listening on 127.0.0.1:8080\n";

$loop->run();