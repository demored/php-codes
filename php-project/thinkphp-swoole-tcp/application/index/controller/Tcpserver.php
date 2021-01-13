<?php
namespace app\index\controller;
use think\swoole\Server;

class Tcpserver extends Server
{
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

            if($response_str["req_type"] == "open_single_door"){
                if(!isset($response_str["door_num"])){
                    $server->send($fd, result_json(["msg" => "门号必须", "code" => 1000]));
                }

                $req_cmd = open_single_door($response_str["door_num"]);

//                $sendStrArray = str_split(str_replace(' ', '', $req_cmd), 2);
//                $str = "";
//                for ($j = 0; $j < count($sendStrArray); $j++) {
//                    $str .= chr(hexdec($sendStrArray[$j]));  // 逐组数据发送
//                }


//            $req_cmd = str_replace(" ", "", $req_cmd);
//                $server->send(1,$str);

                file_put_contents("./a.txt", $req_cmd);
                $server->send($fd,$req_cmd);

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
        }
    }

    //连接关闭时回调函数
    public function onClose($server, $fd)
    {
        echo "Client-{$fd}: Close.\n";
    }

    public function onRequest($request, $response) {
//        $response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");
    }

}