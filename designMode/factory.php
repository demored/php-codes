<?php
// +----------------------------------------------------------------------
// | User: zkcs Date: 2019/4/23 Time: 11:40
// +----------------------------------------------------------------------
// | Copyright (c) 2018 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: 工厂模式创建Db实例
// +----------------------------------------------------------------------

//定义产品
interface Db{
	public function connect();
}

class Mysql implements Db{
	public function connect(){
		echo "mysql connect";
	}
}

class Sqlite implements Db{
	public function connect(){
		echo "sqlite connect";
	}
}

//工厂类,用来生产同一结构的产品
interface CreateDbFactory{
	static public function create();
}

class CreateMysql implements CreateDbFactory{
	static public function create()
	{
		return new Mysql();
	}
}

class CreateSqlite implements CreateDbFactory{

	static public function create()
	{
		return new Sqlite();
	}
}

$mysqlIns = CreateMysql::create();
$sqliteIns = CreateSqlite::create();

var_dump($mysqlIns);
var_dump($sqliteIns);