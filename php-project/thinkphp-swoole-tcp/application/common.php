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

//发送消息到tcp Server
function send_tcp_message($host, $port, $send_data = []){

    $message = json_encode($send_data);
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if(empty($socket)){
        die("Could not create  socket\n");
    }
    @socket_connect($socket, $host, $port);
    socket_set_nonblock($socket);
    socket_write($socket, $message, strlen($message));

    sleep(5);	//机器运算要比网络传输快几百倍，服务器还没有返回数据呢就已经开始运行了，当然就收的是空值了
    $str = "";
    while ($flag = socket_recv($socket, $buf, 2, 0)) {
        $str .= $buf;

    }
    socket_close($socket);
    return $str;
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

//todo 使用redis存储fd和设备号对应关系
//获取设备的fd
function get_device_fd(){

}

//数组格式化成json格式
function format_json($arr = []){
    return json_encode($arr);
}