<?php
/**
 * fsockopen使用
 * User: zkcs
 * Date: 2019/4/16
 * Time: 9:45
 */

//备注：不能用本地作为测试，可以使用远程URL，否则会响应超时
$host = "127.0.0.1";

$port = 80;
$timeout = 30;

$fd = fsockopen($host, $port, $errno , $errstr, $timeout);

if(!$fd){
    exit("{$errno}:{$errstr}");
}

$out = "GET /php-codes/input-stream/test.php HTTP/1.1\r\n";
$out .= "Host: {$host}\r\n";
$out .= "Connection: Close\r\n\r\n";
fwrite($fd, $out);

$content = '';
while (!feof($fd)) {
    $content .= fgets($fd, 1024);
}
echo $content;
fclose($fd);
