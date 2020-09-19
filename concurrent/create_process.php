<?php
//初步使用子进程
$ppid = getmypid();
$pid = pcntl_fork();
if($pid == -1){
    die("fork child process fail");

}elseif($pid == 0){
    $cpid = getmypid();
    echo "我是子进程，我当前的进程号是{$cpid}";
    exit;
}else{
    echo "我是父进程,我当前的进程号是{$ppid}-在父进程获取到的cpid值为{$pid}\n";
    exit;
}

//运行结果
/**
 * 我是父进程,我当前的进程号是69954
    我是子进程，我当前的进程号是69955
 */