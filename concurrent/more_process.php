<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/10
 * Time: 14:43
 */

//php多进程，生成一个固定个数的子进程

$maxChildNums = 8;
$curChildNums = 0;

//当子进程退出时，触发当前信号收发器

function sig_handler($sign){
    global $curChildNums;
    switch($sign){
        case SIGCHLD:
            echo 'SIGCHLD', PHP_EOL;
            $curChildNums--;
            break;
    }
}

pcntl_signal(SIGCHLD, 'sig_handler');


while(1){
    $curChildNums++;
    pcntl_signal_dispatch();
    $pid = pcntl_fork();
    if($pid == -1 ){
        throw new Exception("fork process failed");
    }elseif($pid == 0 ){
        $cpid = posix_getpid();
        $s = rand(2,6);   //让子进程驻留2-6秒
        sleep($s);
        echo "child ${cpid} sleep $s second quilt".PHP_EOL;
        //调用信号分发器
        exit;
    }else{
        //父进程
        if($curChildNums >= $maxChildNums){
            pcntl_wait($status); //阻塞当前父进程
        }
    }
}