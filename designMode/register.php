<?php

//注册器模式

class Register{
    private static $obj = [];
    //注册
    public static function set($objname , $obj){
        if(!isset(self::$obj[$objname])){
            self::$obj[$objname] = $obj;
        }
        return true;
    }
    //获取
    public static function get($objname){
        if(isset(self::$obj[$objname])){
            return self::$obj[$objname];
        }
        return false;
    }
    //销毁
    public static function _unset($objname)
    {
        if(isset(self::$obj[$objname])){
            unset(self::$obj[$objname]);
        }
    }
}
class People{
    public function work(){
        echo __CLASS__;
    }
}


Register::set('People' , new People());
Register::get('People') ->work();
var_dump(Register::get('People') === Register::get('People'));
