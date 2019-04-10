<?php
//发送信号

function signalHandler($signal){
    if($signal == SIGINT){
        echo  'signal received' . PHP_EOL;
    }
}
pcntl_signal(SIGINT,"signalHandler");
while(true){
    sleep(1);
    pcntl_signal_dispatch();
}
//使用ctrl+c中断，不断输出
//^ signal received
//^ signal received
//^ signal received
// ....