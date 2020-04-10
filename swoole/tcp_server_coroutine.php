<?php
// +----------------------------------------------------------------------
// | User: demored Date: 2019/7/1 Time: 14:39
// +----------------------------------------------------------------------
// | Copyright (c) 2019 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: TCP协程服务端
// +----------------------------------------------------------------------


use Swoole\Coroutine\Server as Server;
use Swoole\Coroutine\Server\Connection as Connection;

go(function () {
    $server = new Server('0.0.0.0', 9601, false);
    $server->handle(function (Connection $conn) use ($server) {
        while(true) {
            $data = $conn->recv();
            $json = json_decode($data, true);
            Assert::eq(is_array($json), $json['data'], 'hello');
            $conn->send("world\n");
        }
    });
    $server->start();
});