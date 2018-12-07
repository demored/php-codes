<?php

//widnows 下对memcache 的使用

$mem = new Memcache();
$mem -> connect('localhost' , 11211); //连接memcache
$mem -> add('demo' , 'helloworld' , 0, 60);
$get_value = $mem -> get('demo');
echo $get_value;
echo "<hr/>";

$stats = $mem -> getstats();
print_r($stats);