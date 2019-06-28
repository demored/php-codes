<?php
// +----------------------------------------------------------------------
// | User: zkcs Date: 2019/4/30 Time: 14:42
// +----------------------------------------------------------------------
// | Copyright (c) 2018 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: php-rpc框架yar 鸟哥的大作  服务端
// +----------------------------------------------------------------------


class API{
    public function getMyName($name){
        return "my name is {$name}";
    }

    protected function clientCanNotSee(){
        return "客户端看不到";
    }
}

$service = new Yar_Server(new API());
$service ->handle();