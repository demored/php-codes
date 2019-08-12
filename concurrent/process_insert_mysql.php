<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/9
 * Time: 15:22
 *
 * desc:
 * hard-dev: 1G,1核
 * speed: 200w写 30秒
 */
//大数据导入/导出模板
//多进程批量插入mysql脚本，根据实际硬件核数来

set_time_limit(0);
ini_set("max_execution_time", 0);
ini_set("memory_limit", "2560M");
define("MAX_NUMS", 50000000);
define("PROCESS_NUMS", 10);
$per = MAX_NUMS/PROCESS_NUMS;

$host = "localhost";
$user = "root";
$pwd = "123456";
$db = "test";

if(substr(php_sapi_name(), 0, 3) !== 'cli'){
    die("cli mode only");
}

$conn = mysqli_connect($host, $user, $pwd, $db);
if (!$conn) {
    die("connection failed: " . mysqli_connect_error());
}
//查询一行
function findRow($sql){
    global $conn;
    $res = mysqli_query($conn , $sql);
    $row = mysqli_fetch_assoc($res);
    return $row;
}
//查询全部
function findAll($sql){
    global $conn;
    $res = mysqli_query($conn , $sql);
    $row = mysqli_fetch_assoc($res);
}

//生成随机数
function randomStr($randLength = 6, $addtime = 0){
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    $len = strlen($chars);
    $randStr = '';
    for ($i = 0; $i < $randLength; $i++) {
        $randStr .= $chars[rand(0, $len - 1)];
    }
    $tokenvalue = $randStr;
    if ($addtime) {
        $tokenvalue = $randStr . time();
    }
    return $tokenvalue;
}

//主进程创建表
$conn = mysqli_connect($host, $user, $pwd, $db);
if (!$conn) {
    die("connection failed: " . mysqli_connect_error());
}

$sql = "show tables";
$row = findRow($sql);

if(empty($row) || !in_array('user', array_values($row))){
    $table = <<<EOF
CREATE TABLE `user` (  
    `id` INT (11) NOT NULL AUTO_INCREMENT,  
    `username` VARCHAR (20) NOT NULL,  
    `group_id` INT (11) NOT NULL,  
    `add_time` int(11) NOT NULL,  
    PRIMARY KEY (`id`),  
    KEY `index_username` (`username`) USING HASH  
) ENGINE = INNODB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8  
EOF;

    $res = mysqli_query($conn,$table);
    !$res || `echo "create user success".PHP_EOL;`;
}

$values = "";
$start =  microtime(true);
echo 'start '.$start.PHP_EOL;
for($i = 1; $i<= PROCESS_NUMS; $i++){
    $pid = pcntl_fork();
    if($pid == -1) {
        die('fork error');
    }
    if($pid > 0) {
        $child[] = $pid;
    } elseif ($pid == 0) {

        $link = mysqli_connect($host, $user, $pwd, $db);
        if (!$conn) {
            die("connection failed: " . mysqli_connect_error());
        }
        $num = 1;
        $values = "";
        while($num <= $per){
            $username = randomStr(6);
            $group_id = rand(100,999);
            $add_time = time();
            $values .= "('${username}',${group_id},${add_time}),";
            $num +=1;
        }
        $values = rtrim($values, ',');
        $insertSql = "insert into user(username,group_id, add_time) values ${values}";
        $res = mysqli_query($link , $insertSql);
        echo mysqli_error($link).PHP_EOL;
        mysqli_close($link);
        $id = getmypid();
        echo 'child '.$id.' finished '.microtime(true).PHP_EOL;
        exit(0);
    }
}

while(count($child)){
    foreach($child as $k => $pid) {
        $res = pcntl_waitpid($pid, $status, WNOHANG);
        if ( -1 == $res || $res > 0) {
            unset($child[$k]);
        }
    }
}

$end  = microtime(true);
echo 'end '.$end.PHP_EOL;
echo 'total costed '.($end - $start).PHP_EOL;
exit;
