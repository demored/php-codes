<?php

/**
 *  input流
 */

//读取POST数据
//$raw_post_data = file_get_contents("php://input",'r');
//
//echo "-------\$_POST------------------<br/>";
//echo var_dump($_POST) . "\r\n";
//echo "-------php://input-------------\r\n";
//echo $raw_post_data . "<br/>";


//读取GET数据
$raw_post_data = file_get_contents("php://input",'r');

echo "-------\$_GET------------------<br/>";
echo var_dump($_GET) . "\r\n";
echo "-------php://input-------------\r\n";
echo $raw_post_data . "<br/>";