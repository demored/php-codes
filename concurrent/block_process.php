<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/10
 * Time: 17:48
 */

//父进程阻塞和非阻塞示例

$maxChildsNum = 3;
$ppid = posix_getpid();

///////////////////////阻塞式//////////////////////////////
//for ($i = 0; $i < $maxChildsNum; $i ++){
//    $pid = pcntl_fork();
//    if($pid == -1){
//        die("fork process failed");
//    }elseif($pid){
////        echo "parent {$ppid}".PHP_EOL;
////        pcntl_wait($status);
//        $childs[] = $pid;
//    }else{
//        echo "child ".posix_getpid().PHP_EOL;
//        exit;
//    }
//}
////阻塞式
//foreach($childs as $k => $v){
//    pcntl_wait($status);
//    echo "parent {$ppid}".PHP_EOL;
//}


///////////////////////非阻塞式//////////////////////////////

for ($i = 0; $i < $maxChildsNum; $i ++){
    $pid = pcntl_fork();
    if($pid == -1){
        die("fork process failed");
    }elseif($pid){
        echo "parent {$ppid}".PHP_EOL;
        pcntl_wait($status,WNOHANG);
        $childs[] = $pid;
    }else{
        echo "child ".posix_getpid().PHP_EOL;
        sleep(3);
        exit;
    }
}

//防止主进程太快了，就退出了
sleep(10);







