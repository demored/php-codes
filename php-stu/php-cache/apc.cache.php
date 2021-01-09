<?php

//Apc 缓存系统
include '../Mysql.class.php';
$mysql = new VueMysql();

$apc_key = 'apc_key';
$res = apc_fetch($apc_key);
if(empty($res)){
    /**
     *  Concurrency Level:      2
        Time taken for tests:   4.669 seconds
        Complete requests:      100
        Failed requests:        0
        Total transferred:      18600 bytes
        HTML transferred:       0 bytes
        Requests per second:    21.42 [#/sec] (mean)
        Time per request:       93.378 [ms] (mean)
        Time per request:       46.689 [ms] (mean, across all concurrent requests)
        Transfer rate:          3.89 [Kbytes/sec] received
     */
    $sql = "select * from bigdata limit 10000";
    $res = $mysql -> doSql($sql);
}
