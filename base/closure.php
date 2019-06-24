<?php
// +----------------------------------------------------------------------
// | demored-test [持之以恒]
// +----------------------------------------------------------------------
// |【demored日常练习脚本】
// +----------------------------------------------------------------------
// | Copyright (c) 2018 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: PHP闭包（匿名匿名函数）
// +----------------------------------------------------------------------

$msg = [1,2,3];

$func = function() use($msg){
    print_r($msg);
};

$func();

//参数改为引用
$func1 = function () use (&$msg){
   $msg[0]++;
    print_r($msg);
};
$func1();
print_r($msg);  //引用改变最外层的值

class Stu{
    private $name;
    private $age;
    public function setName(){
        $this -> name = '张三';
    }
    public function getName(){
        $a = 12;
        $setNameAgain = function () use ($a){
        };
        return $setNameAgain;
    }
    public function getName2(){
        echo $this -> name;
    }
}

$stu = new Stu();
$again = $stu -> getName();
$again();
$stu -> getName2();


