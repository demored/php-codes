<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// | Copyright (c) 2021 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc:
// +----------------------------------------------------------------------

$config = include_once  "config.php";
/**
 * 业务系统发送指令格式,json
 * 字段如下:
 * req_type:open_single_door
 * door_num:1
 *
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



//创建Server对象，监听 127.0.0.1:9501 端口
$server = new Swoole\Server("0.0.0.0", 9502);

//监听连接进入事件
$server->on('Connect', function ($server, $fd) {
    echo "Client-{$fd}: Connect.\n";
});

//监听数据接收事件
//响应
$server->on('Receive', function ($server, $fd, $from_id, $data) {
//    file_put_contents("a-{$fd}.txt", $data);
//    $server->send($fd, "Server cmd: " . $data);
    if(strstr($data, "req_type") !== false){
        //业务系统发送
        $response_str = json_decode($data, true);
        if(!isset($response_str["req_type"])){
            $server->send($fd, result_json(["msg" => "类型错误", "code" => 1000]));
        }

        if($response_str["req_type"] == "open_single_door"){
            if(!isset($response_str["door_num"])){
                $server->send($fd, result_json(["msg" => "门号必须", "code" => 1000]));
            }

            $req_cmd = open_single_door($response_str["door_num"]);

            $sendStrArray = str_split(str_replace(' ', '', $req_cmd), 2);
            $str = "";
            for ($j = 0; $j < count($sendStrArray); $j++) {
                $str .= chr(hexdec($sendStrArray[$j]));  // 逐组数据发送
            }


//            $req_cmd = str_replace(" ", "", $req_cmd);
            file_put_contents("a-{$fd}.txt", $str);
            $server->send(1,$str);


        }elseif($response_str["req_type"] == "get_door_status"){
            //获取单个门状态
            if(!isset($response_str["door_num"])){
                $server->send($fd, result_json(["msg" => "门号必须", "code" => 1000]));
            }
            $req_cmd = get_door_status($response_str["door_num"]);
            $req_cmd = str_replace(" ", "", $req_cmd);

            $server->send($fd, $req_cmd);
        }elseif($response_str["req_type"] == "get_all_door_status"){
            //获取全部箱门状态
            $req_cmd = get_all_door_status();
            $server->send($fd, $req_cmd);
        }elseif($response_str["req_type"] == "get_door_nums"){
            //获取箱门数
            $req_cmd = get_door_nums();
            $server->send($fd, $req_cmd);
        }

    }elseif(strtolower(strval(bin2hex($data))) == "ef07ff15ffabcd"){
        //设备发送心跳
        file_put_contents("b.txt", $data);
    }else{
        $buffer = bin2hex($data);
        //默认为设备发送
        file_put_contents("m-{$fd}.txt", $buffer);
//        $server->send($fd, $buffer);
    }

});

//监听连接关闭事件
$server->on('Close', function ($server, $fd) {
    echo "Client-{$fd}: Close.\n";
});

//启动服务器
$server->start();