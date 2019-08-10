<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/10
 * Time: 15:59
 */

//子进程退出避免僵尸进程

//1、使用信号来管理子进程
function signHandler($sign){
    echo "SIGCHLD ".PHP_EOL;
    pcntl_wait($status, WNOHANG);
}

//注册信号
pcntl_signal(SIGCHLD, "signHandler");


$childs = [];
for($i = 1; $i <= 5; $i++){
    $pid = pcntl_fork();
    $ppid = posix_getpid();
    pcntl_signal_dispatch();
    if($pid){
//        echo "parent process ${ppid}".PHP_EOL;
//        $childs[] = $pid;
          sleep(10);

    }elseif($pid == 0 ){
        //在子进程中
        $cpid = posix_getpid();
        echo "child process ${cpid}".PHP_EOL;
        sleep(2);
        exit;

    }elseif($pid == -1){
        die("fork process failed");
    }
}

//2、将循环单拿出来,必须主进程阻塞。在里面使用pcntl_waitpid()来预防僵尸进程
//while(count($childs)){
//    foreach($childs as $k => $v){
//        $res = pcntl_waitpid($v, $status , WNOHANG); //
//        if($res == -1 || $res > 0){
//            unset($childs[$k]);
//        }
//    }
//
//    sleep(1);
//}





