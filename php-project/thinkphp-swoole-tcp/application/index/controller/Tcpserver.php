<?php
namespace app\index\controller;
use think\swoole\Server;
use think\Db;
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

    //0x11 设置设备编号【服务器->设备】
    protected function set_device_no($device_no){
        //转成16进制格式
        $device_no_hex = chunk_split(dechex($device_no), 2, ' ');
        //字节数
        $byte_nums = mb_substr($device_no) + 7;
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
        $device_date_hex = chunk_split(dechex($time), 2, ' ');
        $byte_nums = mb_substr($time) + 7;
        $byte_nums_hex = int2hex($byte_nums);
        $req_cmd = "EF {$byte_nums_hex} 00 13 FF {$device_date_hex} AB CD";
        return $req_cmd;
    }

    //0x14：发送二维码
    protected function set_device_qrcode($qrcode_data){
        $device_qrcode_hex = chunk_split(dechex($qrcode_data), 2, ' ');
        $byte_nums = mb_substr($qrcode_data) + 7;
        $byte_nums_hex = int2hex($byte_nums);
        $req_cmd = "EF {$byte_nums_hex} 00 14 FF {$device_qrcode_hex} AB CD";
        return $req_cmd;
    }

    //设置设备初始登录密码
    protected function set_device_pwd($pwd){
        $pwd_hex = chunk_split(dechex($pwd), 2, ' ');
        $byte_nums = mb_substr($pwd) + 6;
        $byte_nums_hex = int2hex($byte_nums);
        $req_cmd = "EF {$byte_nums_hex} 00 1D FF 02 {$pwd_hex} AB CD";
        return $req_cmd;
    }

    //获取设备初始登录密码
    protected function get_device_pwd(){
        $req_cmd = "EF 08 00 1D FF 01 AB CD";
        return $req_cmd;
    }

    //设备返回，解析响应cmd字符串
    //EF 08 00 01 00 01 AB CD
    protected function parse_response_cmd($cmd = ""){
        $response_cmd = format_str($cmd);
        $response_cmd_type = substr($response_cmd,6,2); //命令类型

        //解析完的格式
        $result = ["type" => "", "code" => 0 , "msg" => "", "data" => []];

        switch ("0x".$response_cmd_type){
            case "0x01": //1、开箱门【服务器->设备】
                $result["type"] = "0x01";
                $response_code = substr($response_cmd, 8, 2);
                if($response_code == "00"){
                    $result["msg"] = "开箱成功";
                }elseif ($response_code == "01"){
                    $result["msg"] = "开箱失败";
                }elseif($response_code == "02"){
                    $result["msg"] = "与锁板通信故障";
                }elseif($response_code == "03"){
                    $result["msg"] = "没有此箱号";
                }else{
                    $result["msg"] = "未知错误";
                    $result["code"] = 101;
                }
                break;
            case "0x02": //获取某箱门状态【服务器->设备】
                $result["type"] = "0x02";
                //转换成16进制格式
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                if($response_cmd_hex_arr[6] == "00"){
                    $result["msg"] = "关闭状态";
                }elseif($response_cmd_hex_arr[6] == "01"){
                    $result["msg"] = "开启状态";
                }elseif(count($response_cmd_hex_arr) == 7){
                    $result["msg"] = "与锁板通信故障";
                    $result["code"] = 102;
                }else{
                    $result["msg"] = "未知错误";
                    $result["code"] = 101;
                }

                break;

            case "0x03": //获取全部箱门状态【服务器->设备】
                $result["type"] = "0x03";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                if(count($response_cmd_hex_arr) == 7){
                    $result["msg"] = "与锁板通信故障";
                    $result["code"] = 102;
                }else{

                    $door_arr = implode("", array_slice($response_cmd_hex_arr, 5, -2));
                    $door_status_arr = ["door_num" => hex2int($door_arr[0]), "door_msg" => []];
                    foreach ($door_arr as $k => $v){
                        $door_status_arr["door_msg"][$k] = $v == "00"?"关闭":"开启";
                    }

                    $result["msg"] = "获取成功";
                    $result["data"] = $door_status_arr;
                }
                break;

            case "0x06": //获取箱门数【服务器->设备】
                $result["type"] = "0x06";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                $hex_num = $response_cmd_hex_arr[5];
                $result["msg"] = "获取成功";
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
                $result["msg"] = "成功获取服务器密码";
                $result["data"] = ["pwd" => $pwd];
                break;

            case "0x07"://设备发送密码给服务器，服务器收到再发送结果给设备，设备的响应
                $result["type"] = "0x07";
                $result["msg"] = "发送密码检验后的设备响应";
                $result["data"] = ["cmd" => $response_cmd];
//                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
//                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
//                $code_status = $response_cmd_hex_arr[5];
//
                break;

            case "0x11": //设置设备编号【服务器->设备】
                $result["type"] = "0x07";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                $code_status = $response_cmd_hex_arr[4];
                if($code_status == "00"){
                    $result["msg"] = "设置成功";
                }elseif($code_status == "01"){
                    $result["msg"] = "设置失败";
                }
                break;

            case "0x12": //获取设备编号【服务器->设备】
                $result["type"] = "0x12";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                $device_hex_str = implode("", array_slice($response_cmd_hex_arr, 5, -2));
                $device_no = hex2str(format_str($device_hex_str));

                $result["msg"] = "获取成功";
                $result["data"]["device_no"] = $device_no;

                break;
            case "0x14": //发送二维码【服务器->设备】
                $result["type"] = "0x14";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                if($response_cmd_hex_arr[4] == "00"){
                    $result["msg"] = "设置成功";
                }else{
                    $result["msg"] = "设置失败";
                }
                break;

            case "0x1D": //获取/设置设备初始登录密码【上位机->下位机】
                $result["type"] = "0x1D";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                if(count($response_cmd_hex_arr) == 7){
                    //设置设备初始密码
                    $result["msg"] = "设置成功";
                }else{
                    //获取设备初始密码
                    $device_pwd_hex = implode("", array_slice($response_cmd_hex_arr, 5, -2));
                    $device_pwd = hex2str(format_str($device_pwd_hex));
                    $result["msg"] = "获取设备初始密码成功";
                    $result["data"]["device_pwd"] = $device_pwd;
                }
                break;

            case "0x27": //获取4G卡ICCID号
                $result["type"] = "0x27";
                $response_cmd_hex = chunk_split(format_str($response_cmd), 2, ' ');
                $response_cmd_hex_arr = explode(" ", $response_cmd_hex);
                if($response_cmd_hex_arr[4] == "05"){
                    $result["msg"] = "网络模块不是4G模块，是有线联网";

                }elseif(intval(format_str($response_cmd)) == 0){
                    $result["msg"] = "获取失败";
                }else{
                    $ICCID_str_hex = implode("", array_slice($response_cmd_hex_arr, 4, -2));
                    $ICCID = hex2str(format_str($ICCID_str_hex));
                    $result["msg"] = "获取成功";
                    $result["data"]["iccid"] = $ICCID;
                }


                break;
            default:
                $result["code"] = 100;
                $result["msg"] = "命令错误";
                break;
        }
    }

    //建立连接时回调函数
    public function onConnect(\swoole_server $server, $fd){
        echo "Client-{$fd}: Connect.\n";
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
                //设置设备号
                $device_no = $response_str["device_no"];
                $req_cmd = $this -> set_device_no($device_no);
                $tcp_cmd = str2hex($req_cmd);
                $fd = $response_str["fd"];
                $server->send($fd,$tcp_cmd);

            }elseif($response_str["req_type"] == "get_device_no"){
                //获取设备号
                $fd = $response_str["fd"];
                $req_cmd = $this ->get_device_no();
                $tcp_cmd = str2hex($req_cmd);
                $server->send($fd,$tcp_cmd);

            }elseif($response_str["req_type"] == "open_single_door"){
                //开单个门
                $device_no = $response_str["device_no"];
                $fd = $response_str["fd"];
                $door_num = $response_str["door_num"];
                $req_cmd = $this -> open_single_door($door_num);
                $tcp_cmd = str2hex($req_cmd);
                $server->send($fd,$tcp_cmd);

            }elseif($response_str["req_type"] == "get_door_status"){
                //获取单个门状态
                $door_num = $response_str["door_num"];
                $fd = $response_str["fd"];
                $req_cmd = $this -> get_door_status($door_num);
                $tcp_cmd = str2hex($req_cmd);
                $server->send($fd, $tcp_cmd);

            }elseif($response_str["req_type"] == "get_all_door_status"){
                //获取全部箱门状态
                $fd = $response_str["fd"];
                $req_cmd = $this -> get_all_door_status();
                $tcp_cmd = str2hex($req_cmd);
                $server->send($fd, $tcp_cmd);

            }elseif($response_str["req_type"] == "get_door_nums"){
                //获取箱门数
                $fd = $response_str["fd"];
                $req_cmd = $this -> get_door_nums();
                $tcp_cmd = str2hex($req_cmd);
                $server->send($fd, $tcp_cmd);
            }elseif($response_str["req_type"] == "set_device_date"){
                //设置设备日期
                $fd = $response_str["fd"];
                $req_cmd = $this -> set_device_date();
                $tcp_cmd = str2hex($req_cmd);
                $server->send($fd, $tcp_cmd);
            }elseif($response_str["req_type"] == "set_device_qrcode"){
                //发送二维码【服务器->设备】
                $fd = $response_str["fd"];
                $qrcode_data = $response_str["qrcode_data"];
                $req_cmd = $this -> set_device_qrcode($qrcode_data);
                $tcp_cmd = str2hex($req_cmd);
                $server->send($fd, $tcp_cmd);
            }elseif($response_str["req_type"] == "set_device_pwd"){
                //设置设备初始登录密码
                $fd = $response_str["fd"];
                $pwd = $response_str["pwd"];
                $req_cmd = $this -> set_device_pwd($pwd);
                $tcp_cmd = str2hex($req_cmd);
                $server->send($fd, $tcp_cmd);
            }elseif($response_str["req_type"] == "get_device_pwd"){
                //获取设备初始登录密码
                $fd = $response_str["fd"];
                $req_cmd = $this -> get_device_pwd();
                $tcp_cmd = str2hex($req_cmd);
                $server->send($fd, $tcp_cmd);
            }

        }elseif(strtolower(strval(bin2hex($data))) == "ef07ff15ffabcd"){
            //设备发送心跳
            $res = Db::name("device") -> where(["fd"=> $fd]) -> find();
            if(empty($res)){
                Db::name("fd") -> insert(["fd" => $fd, "create_time" => time()]);
            }else{
                Db::name("fd") -> where(["fd" => $fd]) -> update(["update_time" => time()]);
            }
            //每次心跳时，获取设备编号
            $req_cmd  = "EF 07 00 12 FF AB CD";
            $tcp_cmd = str2hex($req_cmd);
            $server->send($fd, $tcp_cmd);

        }else{
            //设备响应
            $response_cmd = bin2hex($data);
            $result = parse_response_cmd($response_cmd);
            if($result["type"] == "0x12"){
                //获取设备号，更新数据库
                if(isset($result["data"]["device_no"])){
                    Db::name("fd") -> where(["fd" => $fd]) -> update(["device_no" => $result["data"]["device_no"]]);
                }
                
            }
        }
    }

    //连接关闭时回调函数
    public function onClose($server, $fd){
        echo "Client-{$fd}: Close.\n";
    }

    public function onRequest($request, $response) {
//        $response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");
    }

}