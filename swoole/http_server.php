<?php
// +----------------------------------------------------------------------
// | User: demored Date: 2019/7/1 Time: 11:08
// +----------------------------------------------------------------------
// | Copyright (c) 2019 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: swoole创建web服务器
// +----------------------------------------------------------------------

$host = "192.168.149.141";
$port = 9502;
$server = new swoole_http_server($host, $port);

$server -> on("request", function($request, $response){
    var_dump($request -> get, $request -> post);

    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("hello swoole\n");
});

$server ->start();








