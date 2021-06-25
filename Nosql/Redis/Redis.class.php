<?php
/*
 *  Redis操作类
 *  单例+支持长连接 模式
 */
namespace Nosql;
use think\Exception;
class Redis {
    const REDISTIMEOUT = 0; //超时
    private static $_instance = []; //类单例数组
    private $hash;
    private $redis; //redis连接句柄

    private function __construct($redis_config = []){

        $this->redis = new \Redis();
        $this -> hash = $redis_config["db"];

        if ($redis_config["pconnect"]){
            $this->redis -> pconnect($redis_config['host'], $redis_config['port'], self::REDISTIMEOUT);
        }else{
            $this->redis -> connect($redis_config['host'], $redis_config['port'], self::REDISTIMEOUT);
        }
        //设置连接密码
        if ($redis_config["auth"]){
            $this->redis -> auth($redis_config["auth"]);
        }
        //选择库 0-15
        $this->redis->select($this ->hash);
    }

    //外部获取实例
    public static function getInstance($redis_config){
        if (!isset(self::$_instance[$redis_config["db"]])) {
            self::$_instance[$redis_config["db"]] = new self($redis_config);
        }

        //防止挂掉
        try{
            self::$_instance[$redis_config["db"]]->Ping() == 'Pong';
        } catch (Exception $e) {
            throw new Exception("连接错误");
        }
        return self::$_instance[$redis_config["db"]];
    }

    //获取redis的连接实例
    public function getRedisConnect ()
    {
        return $this->redis;
    }

    public function __call($method,$args)
    {
        return call_user_func_array([$this ->redis, $method], $args);
    }


    /**
     * 关闭单例时做清理工作
     */
    public function __destruct ()
    {
        $key = $this->hash;
        $this -> redis->close();
        self::$_instance[$key] = null;
    }

    private function __clone(){}
}

$redis_config = [
    "host" => "192.168.126.130",
    "port" => "6379",
    "db" => 0,
    "auth" => "",
    "pconnect" => 1
];
$redis = Redis::getInstance($redis_config);
$redis -> set("hello", "world");