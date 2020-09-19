<?php

/**
 * 观察者模式
 */

//1、所有观察者通过主题的接口注册到该主题
//2、主题的状态变化通过观察者的接口，通知所有观察者
//观察者模式变化的 只有主题的状态和观察者的数量

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
