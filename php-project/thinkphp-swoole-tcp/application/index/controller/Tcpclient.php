<?php
namespace app\index\controller;
use think\Controller;

/**
 * Class Tcpclient
 * @package app\index\controller
 *  tcp客户端
 */
class Tcpclient extends Controller{

    public function index(){
        $door_num = input("door_num/d", 1, "intval");

        //请求文件
        set_time_limit(0);
        //IP
        $host = "39.100.145.198";
        //端口
        $port = 9502;
        //发送内容
        $send_data = [
            "req_type" => "open_single_door",
            "door_num" => $door_num
        ];

        $send_msg = json_encode($send_data);
        $ret = send_tcp_message($host, $port, $send_msg);
        var_dump($ret);

    }
}
