<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;



/**
 * Class Tcpclient
 * @package app\index\controller
 *  tcp客户端
 *  1）每个发送给设备的cmd必须大于500ms
 *  2）不能同时开启多个箱门
 */

class Res extends Base{
    private  $host = "39.100.145.198";
    private $port = 9502;
    private $token = "9a3ebb2820a834dcec33360a3b164947";

    private $log_ins = null;

    private $message = [
        "code" => 0,
        "msg" => ""
    ];

    public function _initialize(){
        parent::_initialize();
        //检查token
        $token = $this -> request -> header("token");
        if(empty($token)){
            $this -> return_json(40100, "禁止访问");
        }

        if($token != $this -> token){
            $this -> return_json(40101, "令牌错误");
        }

    }

    //测试monolog
    public function test_monolog(){
//        $this -> log_ins->info('My logger is now ready',array('username' => 'Seldaek'));
//        echo "success";

        $device_no = "000002";
        $byte_nums = strlen($device_no) + 7;
        //转成16进制格式
        $device_no_hex = trim(chunk_split(bin2hex($device_no), 2, ' '));

        //字节数转成16进制
        $byte_nums_hex = int2hex($byte_nums);
        $req_cmd = "EF {$byte_nums_hex} 00 11 FF {$device_no_hex} AB CD";
        echo $req_cmd;

    }

    //解析从tcp服务端传递过来的返回数据
    public function parse_tcp_server_return($result){
        $result = json_decode($result, true);
        $code = $result["code"];
        $msg = $result["msg"];
        $data = $result["data"];
        $this -> return_json($code, $msg, $data);
    }

    //获取所有自提柜
    public function get_all_devices(){
        if(IS_POST){
            $devices = Db::name("devices") -> where(["status" => 1]) -> select();
            $this -> return_json(40000, "获取成功", $devices);
        }else{
            $this -> return_json(40001, "非法请求");
        }
    }

    //设置设备的设备号
    public function set_device_no(){
        if(IS_POST){
            $device_no = input("device_no/s", "", "trim");
            if(empty($device_no)){
                $this -> return_json(30003, "设备号必须");
            }

            //句柄资源
            $fd = input("fd/d", 0 , "intval");
            if(empty($fd)){
                $this -> return_json(30009, "句柄必须");
            }

            if(mb_strlen($device_no) > 15){
                $this -> return_json(30010, "设备号最长15字节");
            }
            set_time_limit(0);
            $send_data = [
                "req_type" => "set_device_no",
                "device_no" => $device_no,
                "fd" => $fd
            ];
            $result = send_tcp_message($this -> host, $this ->port, $send_data);
            $this -> parse_tcp_server_return($result);
        }else{
            $this -> return_json(30001, "非法请求");
        }
    }

    //获取设备编号
    public function get_device_no(){
        if(IS_POST){
            //句柄资源
            $fd = input("fd/d", 0 , "intval");
            if(empty($fd)){
                $this -> return_json(30009, "句柄必须");
            }
            set_time_limit(0);
            $send_data = [
                "req_type" => "get_device_no",
                "fd" => $fd
            ];
            $result = send_tcp_message($this -> host, $this ->port, $send_data);
            $this -> parse_tcp_server_return($result);
        }else{
            $this -> return_json(30001, "非法请求");
        }
    }

    //开单个箱门
    public function open_single_door(){
        if(IS_POST){
            $device_no = input("device_no/s", "", "trim");
            if(empty($device_no)){
                $this -> return_json(30003, "设备号必须");
            }
            $door_num = input("door_num/d", 0, "intval");
            if(empty($door_num)){
                $this -> return_json(30002, "门箱号必须");
            }

            $fd = Db::name("devices") -> where(["device_no" => $device_no]) -> value("fd");
            if(empty($fd)){
                $this -> return_json(30006, "该设备号不存在");
            }
            set_time_limit(0);
            //发送内容
            $send_data = [
                "req_type" => "open_single_door",
                "door_num" => $door_num,
                "device_no" => $device_no,
                "fd" => $fd
            ];
            $result = send_tcp_message($this -> host, $this ->port, $send_data);
            $this -> parse_tcp_server_return($result);

        }else{
            $this -> return_json(30001, "非法请求");
        }
    }

    //获取单个门箱状态
    public function get_door_status(){
        if(IS_POST){
            $device_no = input("device_no/s", "", "trim");
            if(empty($device_no)){
                $this -> return_json(30003, "设备号必须");
            }

            $door_num = input("door_num/d", 0, "intval");
            if(empty($door_num)){
                $this -> return_json(30002, "门箱号必须");
            }
            $fd = Db::name("devices") -> where(["device_no" => $device_no]) -> value("fd");
            if(empty($fd)){
                $this -> return_json(30006, "该设备号不存在");
            }

            set_time_limit(0);
            $send_data = [
                "req_type" => "get_door_status",
                "door_num" => $door_num,
                "device_no" => $device_no,
                "fd" => $fd
            ];

            $result = send_tcp_message($this -> host, $this ->port, $send_data);
            $this -> parse_tcp_server_return($result);
        }else{
            $this -> return_json(30001, "非法请求");
        }
    }


    //获取自提柜所有门箱状态
    public function get_all_door_status(){
        if(IS_POST){
            $device_no = input("device_no/s", "", "trim");
            if(empty($device_no)){
                $this -> return_json(30003, "设备号必须");
            }

            $fd = Db::name("devices") -> where(["device_no" => $device_no]) -> value("fd");
            if(empty($fd)){
                $this -> return_json(30006, "该设备号不存在");
            }

            set_time_limit(0);
            $send_data = [
                "req_type" => "get_all_door_status",
                "device_no" => $device_no,
                "fd" => $fd
            ];

            $result = send_tcp_message($this -> host, $this ->port, $send_data);
            $this -> parse_tcp_server_return($result);
        }else{
            $this -> return_json(30001, "非法请求");
        }
    }

    //获取自提柜门箱数
    public function get_door_nums(){
        if(IS_POST){
            $device_no = input("device_no/s", "", "trim");
            if(empty($device_no)){
                $this -> return_json(30003, "设备号必须");
            }
            
            $fd = Db::name("devices") -> where(["device_no" => $device_no]) -> value("fd");
            if(empty($fd)){
                $this -> return_json(30006, "该设备号不存在");
            }

            set_time_limit(0);
            $send_data = [
                "req_type" => "get_door_nums",
                "device_no" => $device_no,
                "fd" => $fd
            ];
            $result = send_tcp_message($this -> host, $this ->port, $send_data);
            $this -> parse_tcp_server_return($result);
        }else{
            $this -> return_json(30001, "非法请求");
        }
    }

    //设置设备日期
    public function set_device_date(){
        if(IS_POST){
            $device_no = input("device_no/s", "", "trim");
            if(empty($device_no)){
                $this -> return_json(30003, "设备号必须");
            }

            $fd = Db::name("devices") -> where(["device_no" => $device_no]) -> value("fd");
            if(empty($fd)){
                $this -> return_json(30006, "该设备号不存在");
            }

            set_time_limit(0);
            $send_data = [
                "req_type" => "set_device_date",
                "device_no" => $device_no,
                "fd" => $fd
            ];

            $result = send_tcp_message($this -> host, $this ->port, $send_data);
            $this -> parse_tcp_server_return($result);
        }else{
            $this -> return_json(30001, "非法请求");
        }
    }

    //发送二维码【服务器->设备】
    public function set_device_qrcode(){
        if(IS_POST){
            $device_no = input("device_no/s", "", "trim");
            if(empty($device_no)){
                $this -> return_json(30003, "设备号必须");
            }

            $fd = Db::name("devices") -> where(["device_no" => $device_no]) -> value("fd");
            if(empty($fd)){
                $this -> return_json(30006, "该设备号不存在");
            }

            $qrcode_data = input("qrcode_data/s", "", "trim");
            if(empty($qrcode_data)){
                $this -> return_json(30007, "二维码数据必须");
            }

            set_time_limit(0);
            $send_data = [
                "req_type" => "set_device_qrcode",
                "device_no" => $device_no,
                "qrcode_data" => $qrcode_data,
                "fd" => $fd
            ];
            $result = send_tcp_message($this -> host, $this ->port, $send_data);
            $this -> parse_tcp_server_return($result);

        }else{
            $this -> return_json(30001, "非法请求");
        }
    }

    //设置设备初始登录密码
    public function set_device_pwd(){
        if(IS_POST){
            $device_no = input("device_no/s", "", "trim");
            if(empty($device_no)){
                $this -> return_json(30003, "设备号必须");
            }

            $fd = Db::name("devices") -> where(["device_no" => $device_no]) -> value("fd");
            if(empty($fd)){
                $this -> return_json(30006, "该设备号不存在");
            }

            $pwd = input("pwd/s", "", "trim");
            if(empty($pwd)){
                $this -> return_json(30008, "密码不能为空");
            }

            if(strlen($pwd) > 8 || strlen($pwd) < 4){
                $this -> return_json(30009, "密码最少4位，最多8位");
            }

            if(strpos($pwd, "\0") !== false){
                $this -> return_json(30010, "密码有误，包含特殊字符\0");
            }

            set_time_limit(0);
            $send_data = [
                "req_type" => "set_device_pwd",
                "device_no" => $device_no,
                "pwd" => $pwd,
                "fd" => $fd
            ];
            $result = send_tcp_message($this -> host, $this ->port, $send_data);
            $this -> parse_tcp_server_return($result);
        }else{
            $this -> return_json(30001, "非法请求");
        }
    }

    //获取设备初始登录密码
    public function get_device_pwd(){
        if(IS_POST){
            $device_no = input("device_no/s", "", "trim");
            if(empty($device_no)){
                $this -> return_json(30003, "设备号必须");
            }

            $fd = Db::name("devices") -> where(["device_no" => $device_no]) -> value("fd");
            if(empty($fd)){
                $this -> return_json(30006, "该设备号不存在");
            }

            set_time_limit(0);
            $send_data = [
                "req_type" => "get_device_pwd",
                "device_no" => $device_no,
                "fd" => $fd
            ];
            $result = send_tcp_message($this -> host, $this ->port, $send_data);

            $this -> parse_tcp_server_return($result);
        }else{
            $this -> return_json(30001, "非法请求");
        }
    }

    //获取4G卡ICCID号
    public function get_iccid(){
        if(IS_POST){
            $device_no = input("device_no/s", "", "trim");
            if(empty($device_no)){
                $this -> return_json(30003, "设备号必须");
            }

            $fd = Db::name("devices") -> where(["device_no" => $device_no]) -> value("fd");
            if(empty($fd)){
                $this -> return_json(30006, "该设备号不存在");
            }

            set_time_limit(0);
            $send_data = [
                "req_type" => "get_door_status",
                "device_no" => $device_no,
                "fd" => $fd
            ];

            $result = send_tcp_message($this -> host, $this ->port, $send_data);
            $this -> parse_tcp_server_return($result);

        }else{
            $this -> return_json(30001, "非法请求");
        }
    }

}
