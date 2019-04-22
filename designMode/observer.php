<?php

/**
 * 观察者模式
 */

interface SubjectInter{
    public function attach(ObserverInter $obj);
    public function notify();
    public function deattach(ObserverInter $obj);
}
class Subject implements SubjectInter{
    private $obServers = [];
    //注册观察者
    public function attach(ObserverInter $obj){
        if(!in_array($obj,$this->obServers))
            $this ->obServers[] = $obj;
    }
    //通知
    public function notify(){
        if(!empty($this ->obServers)){
            foreach ($this -> obServers as $obServer){
                $obServer -> update();
            }
        }
    }
    //删除观察者
    public function deattach(ObserverInter $obj){
        $index = array_search($obj , $this -> obServers);
        if($index !== false && array_key_exists($index , $this -> obServers)){
            unset($this -> obServers[$index]);
        }
    }
}

//抽象观察者接口
interface ObserverInter{
    public function update();
}
//观察者实现
class ObServer1 implements ObserverInter{
    public function update(){
        echo __CLASS__.'触发<br/>';
    }
}
class ObServer2 implements ObserverInter{
    public function update(){
        echo __CLASS__.'触发<br/>';
    }
}
$subject = new Subject();
$subject -> attach(new ObServer1());
$subject -> attach(new ObServer2());
$subject -> notify();
$subject -> deattach(new ObServer1());
$subject -> notify();
