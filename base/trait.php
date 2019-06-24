<?php
// +----------------------------------------------------------------------
// | demored-test [持之以恒]
// +----------------------------------------------------------------------
// |【demored日常练习脚本】
// +----------------------------------------------------------------------
// | Copyright (c) 2018 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: trait用法
// +----------------------------------------------------------------------

trait Weight{
    public function myWeight(){
        echo '我的重量';
    }
    public function myName(){
        echo '我的名字叫体重';
    }
}

class Stu{
    use Weight;
    public function myName(){
        echo '张三';
    }
}

class Goods{
    public function myName(){
        echo '苹果';
    }
}

$stu = new Stu();
$stu -> myWeight();
$stu -> myName();