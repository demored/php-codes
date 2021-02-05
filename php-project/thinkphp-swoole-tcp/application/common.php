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

use think\Db;

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
//将单个十进制转换成十六进制
function int2hex($num = 0){
    return sprintf("%02s", strtoupper(dechex($num)));
}

//将单个十六进制转换成十进制
function hex2int($hex_num){
    return hexdec($hex_num);
}



//将16进制字符串格式 命令转换成16进制
function str2hex($req_cmd = ""){
    $sendStrArray = str_split(str_replace(' ', '', $req_cmd), 2);
    $str = "";
    for ($j = 0; $j < count($sendStrArray); $j++) {
        $str .= chr(hexdec($sendStrArray[$j]));  // 逐组数据发送
    }

    $req_cmd_hex = str_replace(" ", "", $str);
    return $req_cmd_hex;
}

//设置时间中间不去空格
function str2hex_($req_cmd = ""){
    $sendStrArray = str_split(str_replace(' ', '', $req_cmd), 2);
    $str = "";
    for ($j = 0; $j < count($sendStrArray); $j++) {
        $str .= chr(hexdec($sendStrArray[$j]));  // 逐组数据发送
    }

    $req_cmd_hex = $str;
    return $req_cmd_hex;
}
//将16进制转换成字符串
function hex2str($hex_cmd = ""){
    $str = "";
    for ($i = 0;$i < strlen($hex_cmd) - 1;$i+= 2) {
        $str.= chr(hexdec($hex_cmd[$i] . $hex_cmd[$i + 1]));
    }
    return $str;
}

//根据设备号获取fd
function get_device_fd($device_no = ""){
}


//数组格式化成json格式
function format_json($arr = []){
    return json_encode($arr);
}

//设置设备号
//设备号规则，1）不重复 2）递增+1 3）00000001 8位数
function create_device_no(){
    $last_device_no = Db::name("devices") -> where(["device_no" => ["neq", ""]]) -> value("device_no");
    if(empty($last_device_no)){
        $device_no = "000001";
    }else{
        $device_no = sprintf("%06d", intval($last_device_no) + 1);
    }
    return $device_no;
}

//模拟curl Post操作
function curl_post($url, $data) {
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    }

    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl);
    }
    curl_close($curl); // 关键CURL会话
    return $tmpInfo; // 返回数据
}