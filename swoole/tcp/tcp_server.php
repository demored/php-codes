<?php
// +----------------------------------------------------------------------
// | User: demored Date: 2019/7/1 Time: 10:32
// +----------------------------------------------------------------------
// | Copyright (c) 2019 demored All rights reserved.
// +----------------------------------------------------------------------

// | desc: swoole 创建tcp服务器
// +----------------------------------------------------------------------
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server("0.0.0.0", 9501);

//设置异步任务的工作进程数量
$serv->set([
    'task_worker_num' => 4
]);

//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo "客户端{$fd}连接成功.\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $reactor_id, $data) {
    //投递异步任务
    $task_id = $serv->task($data);
    echo "投递任务成功: id={$task_id}\n";
    $serv->send($fd, "服务器返回: ".$data);
});

//处理异步任务(此回调函数在task进程中执行)
$serv->on('Task', function ($serv, $task_id, $reactor_id, $data) {
    sleep(5);
    //返回任务执行的结果
    $serv->finish("[{$data}]数据完成成功");
});

//处理异步任务的结果(此回调函数在worker进程中执行)
$serv->on('Finish', function ($serv, $task_id, $data) {
    echo $data;
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "客户端关闭: Close.\n";
});

//启动服务器
$serv->start();