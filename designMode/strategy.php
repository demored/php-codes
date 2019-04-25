<?php
// +----------------------------------------------------------------------
// | User: zkcs Date: 2019/4/25 Time: 10:09
// +----------------------------------------------------------------------
// | Copyright (c) 2018 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: 设计模式之策略模式
// +----------------------------------------------------------------------

//使用场景
// 一个复杂的业务可能需要多个接口共同干，放在一个接口不利于扩展
// 例如：搜索引擎是由：遍历页面、排序、排序的结果排序
// 不可能把这几块全部写在一个接口中
// 可以通过策略模式改进

//将排序分离出去

interface Strategy{
    public function filter($record);
}

//对排序的结果进行排序
class FindAfterStrategy implements Strategy{

    private $_name;
    public function __construct($name)
    {
        $this -> _name = $name;
    }

    public function filter($record){
        return strcmp($this -> _name, $record) <= 0;
    }
}

//对搜索结果进行排序

class RandomStrategy implements Strategy{
    public function filter($record)
    {
        return rand(0, 1) >= 0.5;
    }
}


//主搜索 引擎类

class UserList{
    private $_list = [];
    public function __construct($names)
    {
        if($names){
            foreach($names as $name)
                $this ->_list[] = $name;
        }
    }

    public function add($name){
        $this -> _list[] = $name;
    }

    //将数据传输到不同的接口上
    public function find(Strategy $filter){
        $res = [];
        foreach ($this->_list as $user){
            if($filter -> filter($user))
                $res[] = $user;
        }
        return $res;
    }


}

$userList = new UserList([
    "goLang",
    "python",
    "node.js",
    "Lua",
    "shell",
    "php",
    "javascript"
]);

$f1 = $userList -> find(new RandomStrategy());
print_r($f1);

$f2 = $userList ->find(new FindAfterStrategy("f"));
print_r($f2);











