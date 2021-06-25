<?php
/*
 +-------------------------------
 *    服务器端@socket通信整个过程
 +-------------------------------
 *    @socket_create
 *    @socket_bind
 *    @socket_listen
 *    @socket_accept
 *    @socket_read
 *    @socket_write
 *    @socket_close
 +--------------------------------
*/

set_time_limit(0);
$ip = '127.0.0.1';
$port = '1956';

//创建一个socket
$socket = socket_create(AF_INET , SOCK_STREAM , SOL_TCP );

if(!$socket){
    echo " server create socket fail \n reason:".socket_strerror(socket_last_error($socket))."\n";
    exit;
}else{
    echo "server create socket success\n";
}

//绑定到该socket上
if(!socket_bind($socket , $ip , $port)){
    echo " server bind socket fail \n reason:".socket_strerror(socket_last_error($socket))."\n";
    exit;
}else{
    echo "server bind socket success\n";
}


//监听客户端连接
if(!socket_listen($socket)){
    echo " server listen socket fail \n reason:".socket_strerror(socket_last_error($socket))."\n";
    exit;
}else{
    echo "server listen socket success\n";
}

while(true) {
    //等待客户端连接
    if(($clientSource = socket_accept($socket)) !== false){
        echo "Client $clientSource has connected\n";
        $clients[] = $clientSource;
    }
    //获取客户端的输入数据
    $in = socket_read($clientSource , 1024);
    $msg = "you send msg is [$in] you are ".count($clients).' connects';
    //返回数据给客户端
    socket_write($clientSource , $msg , strlen($msg));
    //关闭当前的客户端连接socket
    socket_close($clientSource);
}
//关闭整个服务器
socket_close($socket);