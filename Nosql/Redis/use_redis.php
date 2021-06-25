<?php

/**
 * 使用 Redis类
 */
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require_once "./Redis.class.php";
use Nosql\Redis;

$redis_config = [
    "host" => "192.168.126.130",
    "port" => "6379",
    "db" => 0,
    "auth" => "",
    "pconnect" => 1
];

$redis = Redis::getInstance($redis_config);
$redis -> set("hello", "world");
