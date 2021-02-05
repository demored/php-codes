<?php
namespace app\index\controller;
use think\swoole\Server;
use think\Db;

use app\index\controller\Tcpsys;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;


// TCP服务端
class Tcpserver extends Server{

    // 监听所有地址
    protected $host = '0.0.0.0';
    // 监听 9501 端口
    protected $port = 9502;
    // 指定运行模式为多进程
    protected $mode = SWOOLE_PROCESS;
    // 指定 socket 的类型为 ipv4 的 tcp socket
    protected $sockType = SWOOLE_SOCK_TCP;
    protected $serverType = "tcp";
    protected $log_path = "/data/wwwroot/zyk-swoole-tcp/";
    private $heart_log_ins = null;
    private $sys_log_ins = null;

    // 配置项
    protected $option = [
        /**
         *  设置启动的worker进程数
         *  业务代码是全异步非阻塞的，这里设置为CPU的1-4倍最合理
         *  业务代码为同步阻塞，需要根据请求响应时间和系统负载来调整
         */
        'worker_num' => 4,    //设置启动的Worker进程数
        'daemonize'  => true, //守护进程化（上线改为true）
        'backlog'    => 128,
        'debug_mode' => 1,
         'dispatch_mode' => 2, //固定模式，保证同一个连接发来的数据只会被同一个worker处理
    ];

    public function __construct()
    {
        parent::__construct();

        $this ->heart_log_ins = new Logger('heart_log');
        $this ->sys_log_ins = new Logger('sys_log');
        $this -> set_heart_log();
        $this -> set_sys_log();
    }

    //设置心跳日志
    public function set_heart_log(){
        $heart_log_file = config("heart_log_file");
        $stream_heart= new StreamHandler($heart_log_file, Logger::DEBUG);
        $firephp = new FirePHPHandler();

        $dateFormat = "Y-n-j, g:i:s a";
        $output = "%datetime% > %level_name% > %message% %context% %extra%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream_heart ->setFormatter($formatter);

        // 添加handler
        $this ->heart_log_ins->pushHandler($stream_heart);
        $this ->heart_log_ins->pushHandler($firephp);
    }

    //设置系统日志
    public function set_sys_log(){
        $sys_log_file = config("sys_log_file");
        $stream_sys= new StreamHandler($sys_log_file, Logger::DEBUG);
        $firephp = new FirePHPHandler();

        $dateFormat = "Y-n-j, g:i:s a";
        $output = "%datetime% > %level_name% > %message% %context% %extra%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream_sys ->setFormatter($formatter);

        // 添加handler
        $this ->sys_log_ins->pushHandler($stream_sys);
        $this ->sys_log_ins->pushHandler($firephp);
    }


    //0x11 设置设备编号【服务器->设备】
    protected function set_device_no($device_no){
        //字节数
        $byte_nums = strlen($device_no) + 7;
        //转成16进制格式
        $device_no_hex = trim(strtoupper(chunk_split(bin2hex($device_no), 2, ' ')));
        //字节数转成16进制
        $byte_nums_hex = int2hex($byte_nums);
        $req_cmd = "EF {$byte_nums_hex} 00 11 FF {$device_no_hex} AB CD";
        return $req_cmd;
    }

    //0x12：获取设备编号
    protected function get_device_no(){
        $req_cmd = "EF 07 00 12 FF AB CD";
        return $req_cmd;
    }

    //开单个门0x01
    protected function open_single_door ($door_num){
        $door_num = int2hex($door_num);
        $req_cmd = "EF 08 00 01 FF {$door_num} AB CD";
        return $req_cmd;
    }

    //获取某箱门状态 0x02
    protected function get_door_status ($door_num){
        $door_num = int2hex($door_num);
        $req_cmd = "EF 08 00 02 FF {$door_num} AB CD";
        return $req_cmd;
    }

    //0x03：获取全部箱门状态
    protected function get_all_door_status(){
        $req_cmd = "EF 07 00 03 FF AB CD";
        return $req_cmd;
    }

    //0x06：获取箱门数
    protected function get_door_nums(){
        $req_cmd = "EF 07 00 06 FF AB CD";
        return $req_cmd;
    }

    //0x13：设置设备日期时间
    protected function set_device_date(){
        $time = date("Y-m-d H:i:s");
        $byte_nums = strlen($time) + 7;
        $device_date_hex = trim(strtoupper(chunk_split(bin2hex($time), 2, ' ')));
        $byte_nums_hex = int2hex($byte_nums);
        $req_cmd = "EF {$byte_nums_hex} 00 13 FF {$device_date_hex} AB CD";
        return $req_cmd;
    }

    //0x14：发送二维码
    protected function set_device_qrcode($qrcode_data){
        $byte_nums = strlen($qrcode_data) + 7;

        $device_qrcode_hex = trim(strtoupper(chunk_split(bin2hex($qrcode_data), 2, ' ')));
        $byte_nums_hex = int2hex($byte_nums);
        $req_cmd = "EF {$byte_nums_hex} 00 14 FF {$device_qrcode_hex} AB CD";
        return $req_cmd;
    }

    //设置设备初始登录密码
    protected function set_device_pwd($pwd){
        $byte_nums = strlen($pwd) + 8;
        $pwd_hex = trim(chunk_split(bin2hex($pwd), 2, ' '));
        $byte_nums_hex = int2hex($byte_nums);
        $req_cmd = "EF {$byte_nums_hex} 00 1D FF 02 {$pwd_hex} AB CD";
        return $req_cmd;
    }

    //获取设备初始登录密码
    protected function get_device_pwd(){
        $req_cmd = "EF 08 00 1D FF 01 AB CD";
        return $req_cmd;
    }
    //获取设备初始登录密码
    protected function get_iccid(){
        $req_cmd = "EF 07 00 27 FF AB CD";
        return $req_cmd;
    }


    //设备返回，解析响应cmd字符串
    //EF 08 00 01 00 01 AB CD
    protected function parse_response_cmd($cmd = ""){
        $response_cmd = format_str($cmd);
        $response_cmd_type = substr($response_cmd,6,2); //命令类型
        //解析完的格式
        $result = ["type" => "", "code" => 0 , "msg" => "", "data" => []];

        switch ("0x".strtoupper($response_cmd_type)){
            case "0x01": //1、开箱门【服务器->设备】
                $result["type"] = "0x01";
                $response_code = substr($response_cmd, 8, 2);
                if($response_code == "00"){
                    $result["msg"] = "开箱成功";
                    $result["code"] = 10000;
                }elseif ($response_code == "01"){
                    $result["msg"] = "开箱失败";
                    $result["code"] = 10001;
                }elseif($response_code == "02"){
                    $result["msg"] = "与锁板通信故障";
                    $result["code"] = 20002;
                }elseif($response_code == "03"){
                    $result["msg"] = "没有此箱号";
                    $result["code"] = 10003;
                }else{
                    $result["msg"] = "未知错误";
                    $result["code"] = 20001;
                }
                break;

            case "0x02": //获取某箱门状态【服务器->设备】
                $result["type"] = "0x02";
                //转换成16进制格式
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                if($response_cmd_hex_arr[6] == "00"){
                    $result["msg"] = "关闭状态";
                    $result["code"] = 10004;
                }elseif($response_cmd_hex_arr[6] == "01"){
                    $result["msg"] = "开启状态";
                    $result["code"] = 10005;
                }elseif(count($response_cmd_hex_arr) == 7){
                    $result["msg"] = "与锁板通信故障";
                    $result["code"] = 20002;
                }else{
                    $result["msg"] = "未知错误";
                    $result["code"] = 20001;
                }

                break;

            case "0x03": //获取全部箱门状态【服务器->设备】
                $result["type"] = "0x03";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                if(count($response_cmd_hex_arr) == 7){
                    $result["msg"] = "与锁板通信故障";
                    $result["code"] = 20002;
                }else{

                    $door_arr = implode("", array_slice($response_cmd_hex_arr, 5, -2));
                    $door_status_arr = ["door_num" => hex2int($door_arr[0]), "door_msg" => []];
                    foreach ($door_arr as $k => $v){
                        $door_status_arr["door_msg"][$k] = $v == "00"?"关闭":"开启";
                    }
                    $result["msg"] = "获取成功";
                    $result["code"] = 10006;
                    $result["data"] = $door_status_arr;
                }
                break;

            case "0x06": //获取箱门数【服务器->设备】
                $result["type"] = "0x06";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                $hex_num = $response_cmd_hex_arr[5];
                $result["msg"] = "获取成功";
                $result["code"] = 10006;
                $result["data"] = ["nums" => hex2int($hex_num)];
                break;

            case "0x0A": //设备发送密码给服务器【设备->服务器】
                //这里只获取密码
                $result["type"] = "0x0A";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                //获取设备发来的十六进制密码
                $pwd_hex = implode("", array_slice($response_cmd_hex_arr, 5, -2));
                $pwd = hex2str(format_str($pwd_hex));
                $result["msg"] = "设备发送密码";
                $result["code"] = 10006;
                $result["data"] = ["pwd" => $pwd];
                break;

            case "0x07"://设备发送密码给服务器，服务器收到再发送结果给设备，设备的响应
                $result["type"] = "0x07";
                $result["msg"] = "发送密码检验后的设备响应";
                $result["data"] = ["cmd" => $response_cmd];
                break;

            case "0x11": //设置设备编号【服务器->设备】
                $result["type"] = "0x07";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                $code_status = $response_cmd_hex_arr[4];
                if($code_status == "00"){
                    $result["msg"] = "设置成功";
                    $result["code"] = 10007;
                }elseif($code_status == "01"){
                    $result["msg"] = "设置失败";
                    $result["code"] = 10008;
                }
                break;

            case "0x12": //获取设备编号【服务器->设备】
                $result["type"] = "0x12";
                $response_cmd_hex = trim(chunk_split(format_str($response_cmd), 2, ' '));
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                $device_hex_str = implode("", array_slice($response_cmd_hex_arr, 5, -2));
                $device_no = hex2str(format_str($device_hex_str));

                $result["msg"] = "获取成功";
                $result["code"] = 10006;
                $result["data"]["device_no"] = $device_no;
                break;

            case "0x13": //设置设备日期时间
                $result["type"] = "0x13";
                $result["msg"] = "设置成功";
                $result["code"] = 10007;
                break;

            case "0x14": //发送二维码【服务器->设备】
                $result["type"] = "0x14";

                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                if($response_cmd_hex_arr[4] == "00"){
                    $result["msg"] = "设置成功";
                    $result["code"] = 10007;
                }else{
                    $result["msg"] = "设置失败";
                    $result["code"] = 10008;
                }
                break;

            case "0x1D": //获取/设置设备初始登录密码【上位机->下位机】
                $result["type"] = "0x1D";
                $response_cmd_hex = trim(chunk_split(format_str($response_cmd), 2, ' '));
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                if(count($response_cmd_hex_arr) == 7){
                    //设置设备初始密码
                    $result["msg"] = "设置成功";
                    $result["code"] = 10007;
                    $result["is_set"] = 1;
                    $result["is_get"] = 0;
                }else{
                    //获取设备初始密码
                    $device_pwd_hex = implode("", array_slice($response_cmd_hex_arr, 6, -2));
                    $device_pwd = hex2str(format_str($device_pwd_hex));
                    $result["msg"] = "获取成功";
                    $result["is_get"] = 1;
                    $result["is_set"] = 0;
                    $result["data"]["device_pwd"] = $device_pwd;
                    $result["code"] = 10006;
                }
                break;

            case "0x27": //获取4G卡ICCID号
                $result["type"] = "0x27";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                if($response_cmd_hex_arr[4] == "05"){
                    $result["msg"] = "网络模块不是4G模块，是有线联网";
                    $result["code"] = 10010;

                }elseif(intval(format_str($response_cmd)) == 0){
                    $result["msg"] = "获取失败";
                    $result["code"] = 10009;
                }else{
                    $ICCID_str_hex = implode("", array_slice($response_cmd_hex_arr, 4, -2));
                    $ICCID = hex2str(format_str($ICCID_str_hex));
                    $result["msg"] = "获取成功";
                    $result["code"] = 10006;
                    $result["data"]["iccid"] = $ICCID;
                }

                break;
            default:
                $result["code"] = 11000;
                $result["msg"] = "命令错误";
                break;
        }
        return $result;
    }

    //建立连接时回调函数
    public function onConnect(\swoole_server $server, $fd){
//        echo "Client-{$fd}: Connect.\n";
    }

    //http发送tcp客户端存储日志，记录客户端连接fd，设备连接fd
    public function tcpclient_set_cmd_log($tcp_client_fd , $device_fd, $cmd_lang){
        $data["tcp_client_fd"] = $tcp_client_fd;
        $data["device_fd"] = $device_fd;
        $data["cmd_lang"] = $cmd_lang;
        $data["send_time"] = time();
        Db::name("cmd_log") -> insert($data);
    }

    //设备响应，查询tcpclient_fd，给tcpclient发送命令
    public function get_tcpclient_fd($device_fd, $cmd_lang){
        $where["device_fd"] = $device_fd;
        $where["cmd_lang"] = $cmd_lang;
        $where["status"] = 1;
        $info = Db::name("cmd_log") -> where($where) -> order("id desc")->find();
        $tcp_client_fd = $info["tcp_client_fd"];
        Db::name("cmd_log") -> where(["id" => $info["id"]]) -> update(["status" => -1, "return_time" => time()]);
        return $tcp_client_fd;
    }

    //发送信息
    public function onReceive(\swoole_server $server,$fd,$from_id,$data){
        if(strstr($data, "req_type") !== false){
            //业务系统发送
            $response_str = json_decode($data, true);
            if(!isset($response_str["req_type"])){
                $server->send($fd, result_json(["msg" => "类型错误", "code" => 1000]));
            }

            if($response_str["req_type"] == "set_device_no"){
                //设置设备号,注释：先不由客户端发起设置设备号功能
//                $device_no = $response_str["device_no"];
//                $req_cmd = $this -> set_device_no($device_no);
//                $tcp_cmd = str2hex($req_cmd);
//                $device_fd = $response_str["fd"];
////                $this -> log_ins->info('设置设备号',array('req_cmd' => $req_cmd,"tcp_cmd" => $tcp_cmd, "device_fd" => $device_fd));
//                $server->send($device_fd,$tcp_cmd);
//                $this -> tcpclient_set_cmd_log($fd, $device_fd, "set_device_no");

            }elseif($response_str["req_type"] == "get_device_no"){
                //获取设备号
                $device_fd = $response_str["fd"];
                $req_cmd = $this ->get_device_no();
                $tcp_cmd = str2hex($req_cmd);
                $server->send($device_fd,$tcp_cmd);
                $this -> tcpclient_set_cmd_log($fd, $device_fd, "get_device_no");

            }elseif($response_str["req_type"] == "open_single_door"){
                //开单个门
                $device_no = $response_str["device_no"];
                $device_fd = $response_str["fd"];
                $door_num = $response_str["door_num"];
                $req_cmd = $this -> open_single_door($door_num);
                $tcp_cmd = str2hex($req_cmd);
                $server->send($device_fd,$tcp_cmd);
                $this -> tcpclient_set_cmd_log($fd, $device_fd, "open_single_door");

            }elseif($response_str["req_type"] == "get_door_status"){

                //获取单个门状态
                $door_num = $response_str["door_num"];
                $device_fd = $response_str["fd"];
                $req_cmd = $this -> get_door_status($door_num);
                $tcp_cmd = str2hex($req_cmd);
                $server->send($device_fd, $tcp_cmd);
                $this -> tcpclient_set_cmd_log($fd, $device_fd, "get_door_status");

            }elseif($response_str["req_type"] == "get_all_door_status"){
                //获取全部箱门状态
                $device_fd = $response_str["fd"];
                $req_cmd = $this -> get_all_door_status();
                $tcp_cmd = str2hex($req_cmd);

                $this -> sys_log_ins->info('获取全部门箱状态',["device_fd" => $device_fd, "req_cmd" => $req_cmd,"tcp_cmd" => $tcp_cmd]);

                $server->send($device_fd, $tcp_cmd);
                $this -> tcpclient_set_cmd_log($fd, $device_fd, "get_all_door_status");

            }elseif($response_str["req_type"] == "get_door_nums"){
                //获取箱门数
                $device_fd = $response_str["fd"];
                $req_cmd = $this -> get_door_nums();
                $tcp_cmd = str2hex($req_cmd);
                $server->send($device_fd, $tcp_cmd);
                $this -> tcpclient_set_cmd_log($fd, $device_fd, "get_door_nums");

            }elseif($response_str["req_type"] == "set_device_date"){
                //设置设备日期
                $device_fd = $response_str["fd"];
                $req_cmd = $this -> set_device_date();
                $tcp_cmd = str2hex_($req_cmd);
                $this -> sys_log_ins->info('设置时间',array('req_cmd' => $req_cmd, "tcp_cmd" => $tcp_cmd));
                $server->send($device_fd, $tcp_cmd);
                $this -> tcpclient_set_cmd_log($fd, $device_fd, "set_device_date");

            }elseif($response_str["req_type"] == "set_device_qrcode"){
                //发送二维码【服务器->设备】
                $device_fd = $response_str["fd"];
                $qrcode_data = $response_str["qrcode_data"];
                $req_cmd = $this -> set_device_qrcode($qrcode_data);
                $tcp_cmd = str2hex($req_cmd);
                $server->send($device_fd, $tcp_cmd);
                $this -> tcpclient_set_cmd_log($fd, $device_fd, "set_device_qrcode");

            }elseif($response_str["req_type"] == "set_device_pwd"){
                //设置设备初始登录密码
                $device_fd = $response_str["fd"];
                $pwd = $response_str["pwd"];
                $req_cmd = $this -> set_device_pwd($pwd);
                $tcp_cmd = str2hex($req_cmd);
                $server->send($device_fd, $tcp_cmd);
                $this -> tcpclient_set_cmd_log($fd, $device_fd, "set_device_pwd");

            }elseif($response_str["req_type"] == "get_device_pwd"){
                //获取设备初始登录密码
                $device_fd = $response_str["fd"];
                $req_cmd = $this -> get_device_pwd();
                $tcp_cmd = str2hex($req_cmd);
                $server->send($device_fd, $tcp_cmd);
                $this -> tcpclient_set_cmd_log($fd, $device_fd, "get_device_pwd");


            }elseif($response_str["req_type"] == "get_iccid"){
                //获取设备初始登录密码
                $device_fd = $response_str["fd"];
                $req_cmd = $this -> get_iccid();
                $tcp_cmd = str2hex($req_cmd);

                $this -> sys_log_ins->info('获取4G卡ICCID号',array('req_cmd' => $req_cmd, "tcp_cmd" => $tcp_cmd));

                $server->send($device_fd, $tcp_cmd);
                $this -> tcpclient_set_cmd_log($fd, $device_fd, "get_iccid");

            }
        }elseif(strtolower(strval(bin2hex($data))) == "ef07ff15ffabcd"){
            //设备发送心跳，心跳包默认20s一次
            $this -> heart_log_ins->info('20s一次心跳检测',array('fd' => $fd));
            //每次心跳时查询设备号
            $device_no_req_cmd = $this -> get_device_no();
            $device_no_tcp_cmd = str2hex($device_no_req_cmd);
            $server->send($fd,$device_no_tcp_cmd);

        }else{


            //设备发送响应
            $response_cmd = bin2hex($data);
            $result = $this -> parse_response_cmd($response_cmd);
            $this -> sys_log_ins->info('设备响应',["response_cmd" => $response_cmd, "result" => $result]);

            if($result["type"] == "0x12"){
                //获取设备编号,设备响应结果，无需发送给客户端
                $device_no = isset($result["data"]["device_no"])?$result["data"]["device_no"]:"";
                //查询数据库，如果没有的话那么认为是没有初始设置
                $device_info = Db::name("devices") -> where(["device_no" => $device_no]) -> find();
                if(empty($device_info)){
                    $device_no = create_device_no();
                    $device_data = [
                        "fd" => $fd,
                        "create_time" => time(),
                        "device_no" =>$device_no
                    ];
                    Db::name("devices") -> insert($device_data);
                    //发送设置设备号到设备指令
                    $req_cmd = $this -> set_device_no($device_no);
                    $tcp_cmd = str2hex($req_cmd);
                    $server->send($fd,$tcp_cmd);
                }else{
                    //更新最新的时间
                    //防止fd变化
                    $update["update_time"] = time();
                    $update["is_alived"] = 1;
                    $update["fd"] = $fd;
                    $update["status"] = 1;
                    Db::name("devices") -> where(["id" => $device_info["id"]]) -> update($update);
                }
                
            }elseif ($result["type"] == "0x01"){
                //开箱门
                $tcp_client_fd = $this->get_tcpclient_fd($fd, "open_single_door");
                $server -> send($tcp_client_fd , format_json($result));

            }elseif ($result["type"] == "0x02"){
                //获取某箱门状态
                $tcp_client_fd = $this->get_tcpclient_fd($fd, "get_door_status");
                $server -> send($tcp_client_fd , format_json($result));

            }elseif ($result["type"] == "0x03"){
                //获取全部箱门状态
                $tcp_client_fd = $this->get_tcpclient_fd($fd, "get_all_door_status");
                $server -> send($tcp_client_fd , format_json($result));
            }elseif ($result["type"] == "0x06"){
                //获取箱门数
                $tcp_client_fd = $this->get_tcpclient_fd($fd, "get_door_nums");
                $server -> send($tcp_client_fd , format_json($result));
            }elseif ($result["type"] == "0x0A"){
                //设备发送密码给tcp服务器，服务器解析密码
                //返回要开哪个设备的哪个门数据
                //验证成功后发送开门指令

                $pwd = isset($result["data"]["pwd"])?$result["data"]["pwd"]:"";
                if(empty($pwd)){
                    //解析密码失败
                    $tcp_cmd = str2hex("EF 07 00 0A 08 AB CD");
                    $server->send($fd,$tcp_cmd);
                }else{
                    //向业务系统发送验证
                    $url = config("sys_url");
                    $data["pwd"] = $pwd;
                    $data["device_no"] = Db::name("devices") -> where(["fd" => $fd, "status" => 1]) -> value("device_no");
                    $curl_result = curl_post($url, $data);

                    //验证成功，开箱门
                    if($curl_result["code"] = 0 ){
                        $door_num = $curl_result["door_num"];
                        $req_cmd = $this -> open_single_door($door_num);
                        $tcp_cmd = str2hex($req_cmd);
                        $server->send($fd, $tcp_cmd);

                    }else{
                        //密码验证失败
                        $tcp_cmd = str2hex("EF 07 00 0A 08 AB CD");
                        $server->send($fd,$tcp_cmd);
                    }
                }

                //验证成功开箱门
//                $req_cmd = $this -> open_single_door($door_num);
//                $tcp_cmd = str2hex($req_cmd);
//                $server->send($device_fd,$tcp_cmd);

            }elseif ($result["type"] == "0x07"){
                //设备发送密码给服务器，服务器收到再发送结果给设备，设备的响应
                //同指令0x0A
            }elseif ($result["type"] == "0x11"){
                //设置设备号,设备响应结果
                $tcp_client_fd = $this->get_tcpclient_fd($fd, "set_device_no");
                $server -> send($tcp_client_fd , format_json($result));

            }elseif ($result["type"] == "0x13"){
                //设置设备时间
                $tcp_client_fd = $this->get_tcpclient_fd($fd, "set_device_date");
                $server -> send($tcp_client_fd , format_json($result));

            }elseif ($result["type"] == "0x14"){
                //发送二维码,设备响应结果
                $tcp_client_fd = $this->get_tcpclient_fd($fd, "set_device_qrcode");
                $server -> send($tcp_client_fd , format_json($result));

            }elseif ($result["type"] == "0x1D"){

                //获取/设置设备初始登录密码
                if($result["is_set"] == 1){
                    $tcp_client_fd = $this->get_tcpclient_fd($fd, "set_device_pwd");
                }elseif($result["is_get"] == 1){
                    $tcp_client_fd = $this->get_tcpclient_fd($fd, "get_device_pwd");
                }
                $server -> send($tcp_client_fd , format_json($result));

            }elseif ($result["type"] == "0x27"){
                //获取4G卡ICCID号
                $tcp_client_fd = $this->get_tcpclient_fd($fd, "get_iccid");
                $server -> send($tcp_client_fd , format_json($result));
            }
        }
            
    }

    //连接关闭时回调函数
    public function onClose($server, $fd){
//        echo "Client-{$fd}: Close.\n";
        //判断是否是设备，如果是设备则更新数据库
        //注意事项：当服务端进程重启后，则fd会重新开始计数，也就是说会影响之前的数据
        Db::name("devices") -> where(["fd" => $fd]) -> update(["is_alived" => -1]);

    }

    public function onRequest($request, $response) {
//        $response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");
    }

}