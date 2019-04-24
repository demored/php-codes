<?php
// +----------------------------------------------------------------------
// | User: zkcs Date: 2019/4/23 Time: 11:40
// +----------------------------------------------------------------------
// | Copyright (c) 2018 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: 抽象工厂创建Db实例
// +----------------------------------------------------------------------

//定义产品
interface Goods{
    public function price();
}

class SmallCar implements Goods{
    public function price()
    {
        echo "small car price";
    }
}

class BigCar implements Goods{
    public function price()
    {
        echo "big car price";
    }
}
class  BigBeg implements Goods{
    public function price()
    {
        echo "beg price";
    }
}

class SmallBeg implements Goods{
    public function price()
    {
        echo "small price";
    }
}


//定义工厂
//抽象工厂可以定义多个产品，产品族的
interface CreateFactory{
   static public function createOpen();
   static public function createInner();
}

class CreateCar implements CreateFactory{

    static public function createOpen(){
        return new BigCar();
    }

    static public function createInner()
    {
        return new SmallCar();
    }
}

class CreateBeg implements CreateFactory{

   static public function createOpen()
    {
        return new BigBeg();
    }
   static public function createInner()
    {
        return new SmallBeg();
    }
}

$bigCar = CreateCar::createInner();
$smallCar = CreateCar::createOpen();

var_dump($bigCar);
$bigCar -> price();