<?php

//格式化字符串，去除字符串中的空格
function format_str($str){
    return str_replace(" ","", $str);
}

//解析字符串
function parse_str($str){
    $str = format_str($str);

}



//解析命令
function hex($num = 0){
    $hex_num = dechex($num);
}

//非设备请求tcp服务器
//请求类型：类型值|token:fysw
// open_door:1|token:fysw

//开箱门
function open_door ($door_num){
    $door_num = hex($door_num);
    $cmd = "EF 08 00 01 FF {$door_num} AB CD";
    return $cmd;
}

//创建Server对象，监听 127.0.0.1:9501 端口
$server = new Swoole\Server("0.0.0.0", 9502);

//监听连接进入事件
$server->on('Connect', function ($server, $fd) {
    echo "Client-{$fd}: Connect.\n";

});

//监听数据接收事件
$server->on('Receive', function ($server, $fd, $from_id, $data) {
//    $server->send($fd, "Server: " . $data);
    file_put_contents("a-{$fd}.txt", $data);
    if($data == "send"){
        $server->send(1, "EF 08 00 01 FF 11 AB CD");
    }

});

//监听连接关闭事件
$server->on('Close', function ($server, $fd) {
    echo "Client-{$fd}: Close.\n";
});

//启动服务器
$server->start();