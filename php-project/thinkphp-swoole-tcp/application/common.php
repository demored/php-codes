<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


function send_tcp_message($host, $port, $message){

    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if(empty($socket)){
        die("Could not create  socket\n");
    }
    @socket_connect($socket, $host, $port);
    socket_set_nonblock($socket);

    socket_write($socket, $message, strlen($message));

//    $num = 0;
//    $length = strlen($message);
//    do
//    {
//        $buffer = substr($message, $num);
//        $ret = @socket_write($socket, $buffer);
//        $num += $ret;
//    } while ($num < $length);

//    while ($buff = socket_read($socket, 1024, PHP_NORMAL_READ)) {
//        echo("Response was:" . $buff . "\n");
//        echo("input what you want to say to the server:\n");
//    }

    sleep(5);	//机器运算要比网络传输快几百倍，服务器还没有返回数据呢就已经开始运行了，当然就收的是空值了
    $str = "";
    while ($flag = socket_recv($socket, $buf, 2, 0)) {
        $str .= $buf;

    }

//    sleep(1);
//    $ret = '';
//    do
//    {
//        $buffer = @socket_read($socket, 1024, PHP_NORMAL_READ);
//        $ret .= $buffer;
//    } while (strlen($buffer) == 1024);

    socket_close($socket);
    return $str;
}

//客户端发送tcp协议
function send_tcp_hex($host, $port, $message){
    $sendStr = $message;  // 16进制数据
    $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // 将16进制数据转换成两个一组的数组
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);  // 创建Socket

    if (socket_connect($socket, $host, $port)) {  //连接
        for ($j = 0; $j < count($sendStrArray); $j++) {
            socket_write($socket, chr(hexdec($sendStrArray[$j])));  // 逐组数据发送
        }

        $receiveStr = "";
        $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // 采用2进制方式接收数据
        $receiveStrHex = bin2hex($receiveStr);  // 将2进制数据转换成16进制
        echo "client:" . $receiveStrHex;
    }
    socket_close($socket);  // 关闭Socket
}





/**
 * 业务系统发送指令格式,json
 * 字段如下:
 * req_type:open_single_door
 * door_num:1
 */
//格式化字符串，去除字符串中的空格
function format_str($str){
    return str_replace(" ","", $str);
}

//解析响应cmd字符串
//EF 08 00 01 00 01 AB CD
function parse_response_cmd($cmd = ""){
    $response_cmd = format_str($cmd);
    $response_cmd_type = substr($response_cmd,6,2); //命令类型
    switch ("0x".$response_cmd_type){
        case "0x01": //1、开箱门【服务器->设备】
            $response_code = substr($response_cmd, 8, 2);
            if($response_code == "00"){
                echo "开箱成功";
            }
            if($response_code == "01"){
                echo "开箱失败";
            }
            if($response_code == "02"){
                echo "与锁板通信故障";
            }
            if($response_code == "03"){
                echo "没有此箱号";
            }
            break;
        case "0x02": //获取某箱门状态【服务器->设备】
            break;

        default:
            echo "命令错误";
            break;
    }
}

//将十进制转换成十六进制
function hex($num = 0){
    return sprintf("%02s", strtoupper(dechex($num)));
}


//非设备请求tcp服务器
//请求类型：类型值|token:fysw
// open_door:1|token:fysw

//开单个门0x01
function open_single_door ($door_num){
    $door_num = hex($door_num);
    $req_cmd = "EF 08 00 01 FF {$door_num} AB CD";
    return $req_cmd;
}

//获取某箱门状态 0x02
function get_door_status ($door_num){
    $door_num = hex($door_num);
    $req_cmd = "EF 08 00 02 FF {$door_num} AB CD";
    return $req_cmd;
}

//0x03：获取全部箱门状态
function get_all_door_status(){
    $req_cmd = "EF 07 00 03 FF AB CD";
    return $req_cmd;
}

//0x06：获取箱门数
function get_door_nums(){
    $req_cmd = "EF 07 00 06 FF AB CD";
    return $req_cmd;
}

//0x12：获取设备编号
function get_device_no(){
    $req_cmd = "EF 07 00 12 FF AB CD";
    return $req_cmd;
}

//todo 使用redis存储fd和设备号对应关系
//获取设备的fd
function get_device_fd(){

}
function result_json($arr = []){
    return json_encode($arr);
}