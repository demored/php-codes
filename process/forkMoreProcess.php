<?php
//创建10个进程

$master = getmypid();
$childs = [];
for($i = 0; $i < 10; $i++){
    $pid = pcntl_fork();
    if($pid == -1){
        die('could not fork');
    }
    if($pid > 0) {
        $childs[] = $pid;
    }else{
        sleep($i+10);
        exit();
    }
}
echo "master :{$master}\n";

//打印所有的子进程
print_r($childs);