<?php
namespace app\index\controller;
use think\Controller;

/**
 * Class Tcpclient
 * @package app\index\controller
 *
 *  tcp客户端
 */

class Res extends Base{
    private  $host = "39.100.145.198";
    private $port = 9502;
    private $message = [
        "code" => 0,
        "msg" => ""
    ];

    public function _initialize(){
        parent::_initialize();
        //检查token
        $token = $this -> request -> header("token");
        if(empty($token)){
            $this -> return_json(40000, "禁止访问");
        }



    }

    //开单个箱门
    public function open_single_door(){
        if(IS_POST){
            $door_num = input("door_num/d", 0, "intval");
            if(empty($door_num)){
                $this -> return_json(40002, "门箱号必须");
            }

            set_time_limit(0);
            //发送内容
            $send_data = [
                "req_type" => "open_single_door",
                "door_num" => $door_num
            ];
            $ret = send_tcp_message($this -> host, $this ->port, $send_data);
            var_dump($ret);
        }else{
            $this -> return_json(40001, "非法请求");
        }

    }

    //获取单个门箱状态
    public function get_door_status(){
        if(IS_POST){
            $door_num = input("door_num/d", 0, "intval");
            if(empty($door_num)){
                $this -> return_json(40002, "门箱号必须");
            }

            set_time_limit(0);
            $send_data = [
                "req_type" => "get_door_status",
                "door_num" => $door_num
            ];
            $ret = send_tcp_message($this -> host, $this ->port, $send_data);
            var_dump($ret);
        }else{
            $this -> return_json(40001, "非法请求");
        }
    }

    //获取所有门箱状态
    public function get_all_door_status(){
        if(IS_POST){
            set_time_limit(0);
            $send_data = [
                "req_type" => "get_all_door_status",
            ];
            $ret = send_tcp_message($this -> host, $this ->port, $send_data);
            var_dump($ret);
        }else{
            $this -> return_json(40001, "非法请求");
        }
    }

    //获取设备编号
    public function get_device_no(){
        if(IS_POST){
            set_time_limit(0);
            $send_data = [
                "req_type" => "get_device_no",
            ];
            $ret = send_tcp_message($this -> host, $this ->port, $send_data);
            var_dump($ret);
        }else{
            $this -> return_json(40001, "非法请求");
        }
    }

    //设置设备号
    public function set_device_no(){
        if(IS_POST){
            $device_no = input("device_no/s", "", "trim");
            if(empty($device_no)){
                $this -> return_json(40003, "设备号必须");
            }

            if(mb_strlen($device_no) > 15){
                $this -> return_json(40003, "设备号最长15字节");
            }

            set_time_limit(0);
            $send_data = [
                "req_type" => "get_device_no",
                "device_no" => $device_no
            ];
            $ret = send_tcp_message($this -> host, $this ->port, $send_data);
            var_dump($ret);
        }else{
            $this -> return_json(40001, "非法请求");
        }
    }














}
