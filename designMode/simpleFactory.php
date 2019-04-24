<?php
//简单工厂实例化Db


//定义产品类

interface Db{
    public function connect();
}

class Mysql implements Db{
    public function connect()
    {
        echo "mysql connect";
    }
}
class Sqlite implements Db{
    public function connect()
    {
        echo "sqlite connect";
    }
}

//建立一个工厂，通过传参来获得不同的实例
//使用单例创建简单工厂
class CreateDbIns{
    static private $dbIns= null ;
    //私有化构造函数
    protected function __construct(){

    }

    static public function getIns($type = "mysql"){
        //下面这种写法有错，self::$dbIns instanceof self 每个实例不一样不是当前CreateDbIns的实例
        //if(self::$dbIns)也不对，这样所有的实例都是mysql，希望通过工厂创建不同的实例

//        if(self::$dbIns && self::$dbIns instanceof self){
//            return self::$dbIns;
//        }
        if($type == "mysql"){
            self::$dbIns = new mysql();
        }elseif ($type == "sqlite"){
            self::$dbIns = new Sqlite();
        }
        return self::$dbIns;

    }
    //防止该类被克隆
    public function __clone(){
        trigger_error("forbidden clone");
    }
}

$mysqlIns = CreateDbIns::getIns("mysql");
var_dump($mysqlIns);
$sqliteIns = CreateDbIns::getIns("sqlite");
var_dump($sqliteIns);



