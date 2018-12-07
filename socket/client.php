<?php
/*
 * 客户端连接
 +-------------------------------
 *    @socket连接整个过程
 +-------------------------------
 *    @socket_create
 *    @socket_connect
 *    @socket_write
 *    @socket_read
 *    @socket_close
 +--------------------------------
 */

$port = 1956;
$ip = "127.0.0.1";
//创建一个socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if(!$socket){
    echo "socket_create failed\nReason:".socket_strerror(socket_last_error($socket)).'\n';
    exit;
}else{
    echo "client create socket success!\n";
}

//连接服务器端
$result = socket_connect($socket, $ip, $port);
if (!$result) {
    echo "socket_connect failed.\nReason: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit;
}else {
    echo "client connect success\n";
}

//往服务器端发送数据
$in = 'mysql';
//从服务器端接收的数据
$out = '';
//往服务器器发送http请求
if(!socket_write($socket, $in, strlen($in))) {
    echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit;
}else {
    echo "client send success!\n";
}
while($out = socket_read($socket, 1024)) {
    echo "receive success!\n";
    echo "receive content:\n",$out."\n";
}

socket_close($socket);
echo "client close OK\n";