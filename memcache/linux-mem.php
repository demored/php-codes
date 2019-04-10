<?php
//linux下测试编译后的memcache
header('content-type:text/html;charset=utf-8');
$host = '192.168.8.131';
$port = '11211';

$mem = new Memcached();
$mem -> addServer ($host , $port);//连接memcache缓存服务器 , pconnect()长连接，不会因为close而关闭
$mem ->add('demo' , 'hello world!' , false , 30);//插入数据
$demo = $mem ->get('demo');//取出数据
echo "demo的值是:".$demo."<br/>";
$mem->add('num' , 9 , false , 30);
$mem->increment('num' , 1);//将内存中的num 加1
$mem ->decrement('num' , 2);//将内存中的num减1 ， 减的数字最小为0
$mem ->delete('num');//删除内存中的num$mem ->flush()；//清空内存中所有的变量
$mem ->set('a' , '刘德华' , false , 30);//修改一个变量，当变量不存在时，增加
echo $mem ->get('a');
$mem ->replace('b' ,'demored' , false , 30);//替换一个已经存在的变量，变量不存在不作任何操作
echo $mem->get('b');