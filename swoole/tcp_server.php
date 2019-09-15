<?php
// +----------------------------------------------------------------------
// | User: demored Date: 2019/7/1 Time: 10:32
// +----------------------------------------------------------------------
// | Copyright (c) 2019 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: swoole 创建tcp服务器
// +----------------------------------------------------------------------

$host = "127.0.0.1";
$port = 9501;

$server = new swoole_server($host, $port);


//进入连接模式
$server -> on("connect",function($server, $fd){
    echo "client:{$fd} connect \n";
});


//监听数据接收
$server ->on('receive', function($server, $fd , $from_id, $data){
    echo "client：{$fd} 发送 {$data} \n";
    $server ->send($fd , "server send {$data} \n");
});


//监听连接关闭事件

$server -> on('close', function($server , $fd){
    echo "client:{$fd}连接关闭 \n";
});

//启动服务器
$server->start();
























