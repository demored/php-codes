<?php

/**
 * 简单工厂
 */

//msyql类
class MysqlDb{

}
//Sqlite类
class SqliteDb {

}
class Db {
	
	static public $ins = null; 
	static public function getDb($type){
		if($type == 'mysql'){
			self::$ins = new MysqlDb();
		}elseif ($type == 'sqlite') {
			self::$ins = new SqliteDb();
		}
		return self:: $ins;
	}
}

$db = Db :: getDb('mysql');
var_dump($db);
$db = Db :: getDb('sqlite');
var_dump($db);


//工厂模式
//产品类
abstract class Car{
	abstract public function getCarName();
}
//工厂类（主要实现工厂对象），防止实现类时new所以用interface
interface CarFactory{
	static function getCarObj();
}
//具体产品实现类
class BigCar extends Car{
	public function getCarName()
	{
		echo 'I am big car';
	}
}
//具体工厂类
class BigCarFactory implements  CarFactory{
	public static function getCarObj()
	{
		return new BigCar();
	}
}

class SamllCar extends Car{
	public function getCarName()
	{
		echo 'I am small car';
	}
}

class SmallCarFactory implements  CarFactory{
	public static function getCarObj()
	{
		return new SamllCar();
	}
}
BigCarFactory::getCarObj()->getCarName();
SmallCarFactory::getCarObj()->getCarName();