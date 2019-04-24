<?php
// +----------------------------------------------------------------------
// | User: zkcs Date: 2019/4/23 Time: 11:40
// +----------------------------------------------------------------------
// | Copyright (c) 2018 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: 静态成员和静态方法
// +----------------------------------------------------------------------


class Mysql{
    static $error;
    static public function getMysqlConn(){
        echo "mysql connect<br/>";
    }
    public function setSql(){
        echo "set mysql sql";
    }
}

$mysql = new mysql();
$mysql -> getMysqlConn(); //实例可以访问静态方法
$mysql -> error; //实例不能访问静态属性