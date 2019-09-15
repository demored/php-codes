<?php
// +----------------------------------------------------------------------
// | User: demored Date: 2019/7/1 Time: 14:33
// +----------------------------------------------------------------------
// | Copyright (c) 2019 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: TCP协程客户端
// +----------------------------------------------------------------------

$client = new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 9601, 0.5))
{
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send("hello world\n");
echo $client->recv();
$client->close();