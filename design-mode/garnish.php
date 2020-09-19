<?php
// +----------------------------------------------------------------------
// | User: zkcs Date: 2019/4/25 Time: 10:46
// +----------------------------------------------------------------------
// | Copyright (c) 2018 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: 设计模式之装饰器模式
// +----------------------------------------------------------------------

//有点像组合模式，全部打散，用什么直接传 对象


//饮料抽象类
abstract class Drunk{
    public $name;
    abstract public function cost();

}

//Coffee类
class Coffee extends  Drunk{
   public function __construct()
   {
       $this -> name = "Coffee";
   }
    public function cost(){
        return 1.0;
    }
}

//调味类 抽象类
Class Seasoner extends Drunk{
    public $name;
    public function cost(){
    }
}


class Milk extends Seasoner{
    private $drunkIns;
    public function __construct(Drunk $drunk)
    {
        $this -> name = "milk";
        if($drunk instanceof Drunk){
            $this -> drunkIns = $drunk;
        }else{
            trigger_error("fail");
        }
    }
    public function cost(){
        return $this ->drunkIns->cost() + 0.2;
    }
}

class Sugar extends Seasoner{
    private $drunkIns;
    public function __construct(Drunk $drunk)
    {
        $this -> name = "sugar";
        if($drunk instanceof Drunk){
            $this -> drunkIns = $drunk;
        }else{
            trigger_error("fail");
        }
    }
    public function cost()
    {
        return $this -> drunkIns -> cost() + 0.33;
    }
}

// 点杯咖啡
$coffee = new Coffee();
//加点牛奶
$coffee = new Milk($coffee);
//加点糖
$coffee = new Sugar($coffee);

var_dump($coffee);
printf("Coffee Total:%0.2f元\n",$coffee->Cost());






