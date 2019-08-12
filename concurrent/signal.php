<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/9
 * Time: 10:36
 */


//在php中信号的使用


//定义一个处理器
function signalHandler($signal){
    if($signal == SIGINT){
        echo "signal received ".PHP_EOL;
    }
}

//注册一个信号
pcntl_signal(SIGINT, 'signalHandler');

while(true){
    sleep(1);
    for ($i = 0; $i < 1000000; $i++) {
        echo $i . PHP_EOL;
        usleep(100000);
    }
    //信号分发
    pcntl_signal_dispatch();
}

