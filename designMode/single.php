<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/18
 * Time: 13:57
 */
//单例模式
header('content-type:text/html;charset=utf-8');
class A{
    public $num;
    public function test(){
        echo 'the test';
    }
    public function __set($param , $vlue){
        $this -> param = $vlue;
    }
}

$a1 = new A();
$a2 = new A();

if($a1 === $a2){
    echo '全想等<br/>';
}else{
    echo "不全等<br/>";//类new出来的两个实例不相等
}
$a1 -> num = 10;
var_dump($a2 -> num);//null 一个对象操作对另一个对象不可见
echo $a1 -> num ;   // 10
echo "<br/>";

$a1 -> param = 20;
echo $a1 -> param;
echo "<br/>";
echo $a2 -> param;


//单例模式

class B{
    //保存实例的
    protected static $ins = null;
    //禁止new
    private function __construct()
    {
    }
    //开放公共接口获取实例
    public static function getIns(){
        if(self::$ins == null){
            self:: $ins = new self();
        }
        return self::$ins;
    }
    //防止clone保证类实例只有一个
    public function __clone()
    {
        trigger_error('Clone is not allow' ,E_USER_ERROR);
    }
}
$b1 = B::getIns();
$b2 = B::getIns();
//全等
if($b1 === $b2){
    echo "全相等<br/>";
}else{
    echo "不全等<br/>";
}
$b3 = clone $b1;
//不全等
if($b1 === $b3){
    echo "全相等<br/>";
}else{
    echo "不全等<br/>";
}




