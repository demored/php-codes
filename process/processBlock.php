<?php

//父进程阻塞

define('FORK_NUMS',5);
$ppids = [];

for($i = 0 ; $i < FORK_NUMS ; $i++){
    $pid = pcntl_fork();
    $ppids[$i] = $pid;
    if($ppids[$i] == -1){
        die('fork failed');
    }elseif($ppids[$i]){
        pcntl_wait($status,WNOHANG);
    }elseif($ppids[$i] == 0){
        echo "父进程ID:".posix_getppid()."子进程ID:".posix_getpid()."\n";
        sleep(10);
        exit;
    }
}

////我们把pcntl_waitpid放到for循环外面，那样在for循环里创建子进程就不会阻塞了
////但是在这里仍会阻塞，主进程要等待5个子进程都退出后，才退出。
//foreach ($ppids as $pid) {
//    pcntl_waitpid($pid, $status);
//}

