<?php
// +----------------------------------------------------------------------
// | User: demored Date: 2019/7/1 Time: 11:08
// +----------------------------------------------------------------------
// | Copyright (c) 2019 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: swoole创建web服务器
// +----------------------------------------------------------------------

$http = new Swoole\Http\Server("0.0.0.0", 9501);

$http->on('request', function ($request, $response) {
    var_dump($request->get, $request->post);
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();








