<?php
// +----------------------------------------------------------------------
// | User: zkcs Date: 2019/6/24 Time: 21:45
// +----------------------------------------------------------------------
// | Copyright (c) 2018 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: php使用redis秒杀
// +----------------------------------------------------------------------

header("content-type:text/html;charset=utf-8");
date_default_timezone_set("PRC");
$host = "192.168.149.146";
$port = 6379;
$redis = new Redis();
$res = $redis ->connect($host, $port);
$num = $redis -> get("goods");

if($num > 0){
    //库存减1
    $redis -> decr("goods");
}else{
    echo "库存没有了";
}

echo $num = $redis -> get("goods");