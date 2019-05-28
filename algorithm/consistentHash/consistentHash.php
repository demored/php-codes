<?php
// +----------------------------------------------------------------------
// | User: zkcs Date: 2019/5/27 Time: 17:09
// +----------------------------------------------------------------------
// | Copyright (c) 2018 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: 自定义一致性hash
// +----------------------------------------------------------------------

interface ConsistentHash{
    //将字符串转换为hash
    public function toHash($str);

    //添加一台服务器到服务器列表,添加到圆环中
    public function addServer($host);

    //从服务器中删除一个节点
    public function deleteServer($host);

    //从服务器列表中查找节点
    public function lookup($key);

}

class MyConsistentHash implements ConsistentHash{

    public $serverList = []; //服务器列表
    public $virtualPos = []; //虚拟节点列表
    public $virtualNums = 6; //虚拟节点数量

    public function toHash($str){
        $str = md5($str);
        return sprintf("%u", crc32($str));

    }

    //往圆环上添加节点
    public function addServer($host){
        //服务器列表不存在该服务器
        if(!isset($this -> serverList[$host])){
            for($i = 0 ; $i < $this ->virtualNums ; $i++){

                //得出该虚拟节点hash值
                $pos = $this->toHash($host . '#' . $i);

                //存放虚拟节点存放的对应的服务器
                $this->virtualPos[$pos] = $host;

                //存放单台服务器包含的所有节点
                $this->serverList[$host][] = $pos;
            }
            //虚拟节点根据位置排序
            ksort($this->virtualPos, SORT_NUMERIC);
        }
    }

    //从圆环上删除节点
    public function deleteServer($host){
        if(isset($this ->serverList[$host])){
            foreach($this -> serverList[$host] as $k => $v){
                unset($this->virtualPos[$v]);
            }
            unset($this -> serverList[$host]);
        }
    }

    //在当前的服务器列表中找到合适的服务器存放数据
    public function lookup($key){
        $point = $this ->toHash($key);

        //先取圆环上最小的一个节点当成结果(数组的第一个索引单元)
        $findServer = current($this -> virtualPos);

        foreach ($this -> virtualPos as $pos => $server){
            if($point <=  $pos){
                $findServer = $server;
                break;
            }
        }
        reset($this ->virtualPos);
        return $findServer;
    }

}

//测试

//添加服务器
$hashServer = new MyConsistentHash();
$hashServer ->addServer("192.168.1.1");
$hashServer ->addServer("192.168.1.2");
$hashServer ->addServer("192.168.1.3");
$hashServer ->addServer("192.168.1.4");
$hashServer ->addServer("192.168.1.5");

echo "<pre>";
print_r($hashServer -> serverList);
print_r($hashServer -> virtualPos);



//设置key

echo "保存 key1 到 server :".$hashServer->lookup('key1') . '<br />';
echo "保存 key2 到 server :".$hashServer->lookup('key2') . '<br />';
echo "保存 key3 到 server :".$hashServer->lookup('key3') . '<br />';
echo "保存 key4 到 server :".$hashServer->lookup('key4') . '<br />';
echo "保存 key5 到 server :".$hashServer->lookup('key5') . '<br />';
echo "保存 key6 到 server :".$hashServer->lookup('key6') . '<br />';
echo "保存 key7 到 server :".$hashServer->lookup('key7') . '<br />';
echo "保存 key8 到 server :".$hashServer->lookup('key8') . '<br />';
echo "保存 key9 到 server :".$hashServer->lookup('key9') . '<br />';
echo "保存 key10 到 server :".$hashServer->lookup('key10') .'<br />';


echo "</pre>";


