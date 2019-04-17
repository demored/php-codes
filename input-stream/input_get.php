<?php
/**
 * Created by PhpStorm.
 * User: zkcs
 * Date: 2019/4/16
 * Time: 9:26
 */

//模拟GET请求
$query = 'n=' . urldecode('perfgeeks') . '&p=' . urldecode('7788');
$host = '192.168.149.135';
$port = 80;
$path = '/input_server.php';


$fp = fsockopen($host, $port, $error_no, $error_desc, 30);

if ($fp) {
        fputs($fp, "GET {$path}?{$query} HTTP/1.1\r\n");
        fputs($fp, "Host: {$host}\r\n");
        fputs($fp, "Connection: close\r\n\r\n");

    while (!feof($fp)) {
        $d .= fgets($fp, 4096);
    }
    fclose($fp);
    echo $d;
}