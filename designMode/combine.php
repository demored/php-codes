<?php
// +----------------------------------------------------------------------
// | User: zkcs Date: 2019/4/25 Time: 14:14
// +----------------------------------------------------------------------
// | Copyright (c) 2018 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: 设计模式之组合模式
// +----------------------------------------------------------------------

//组合模式 优于 继承
abstract class Unit{
    abstract public function getLength();
}

class T1 extends Unit{
    private $myLength = 10;

    public function getLength(){

       return  $this -> myLength;
    }
}


class T2 extends Unit{
    private $myLength = 100;
    public function getLength(){
        return $this -> myLength;
    }
}

class User{
    private $unit = [];
    private $totalLength = 0;
    public function addUnit(Unit $unit){
        array_push($this -> unit, $unit);
    }

    public function getCommonLength(){
        print_r($this ->unit);
        if($this -> unit){
            foreach($this -> unit as $unit){
                if($unit instanceof Unit)
                    $this ->totalLength += $unit -> getLength();
            }
        }

        return $this -> totalLength;
    }
}

$user = new User();
$user -> addUnit(new T1());
$user -> addUnit(new T2());
printf('通过组合模式，总的length为：%d', $user->getCommonLength());

