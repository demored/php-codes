<?php

/**
 * 顺序表基本操作
 * 包括
 *1.顺序表的初始化 __contruct()
 *2.清空顺序表 __destruct()
 *3.判断顺序表是否为空 isEmpty()
 *4.返回顺序表的长度 getLength()
 *5.根据下标返回顺序表中的某个元素 getElement()
 *6.返回顺序表中某个元素的位置 getElementPosition()
 *7.返回顺序表中某个元素的直接前驱元素 getElementPredecessorr()
 *8.返回某个元素的直接后继元素 getElementSubsequence()
 *9.指定下标位置返回元素 getElemForPos()
 *10.根据下标或者元素值删除顺序表中的某个元素 getDeleteElement()
 *11.根据元素位置删除顺序表中的某个元素 getDeleteEleForPos()
 *12.在指定位置插入一个新的结点 getInsertElement()
 */

class OrderNearList{

    private $ol = null;

    /**
     * 初始化顺序表
     * @param array $ol
     */
    public function __construct($ol = []){
        $this -> ol = $ol;
    }

    /**
     * 判断顺序表是否为空
     * @return bool
     */
    public function isEmpty(){
        return count($this -> ol)>0 ? true:false;
    }
    /**
     * 获取顺序表的长度
     * @return int
     */
    public function getLen(){
        return count($this -> ol);
    }

    /**
     * 根据key获取顺序表中的元素
     * @param $key
     */
    public function getElement($key){
        return $this -> ol[$key];
    }

    /**
     * 根据指定的位置返回顺序表中的数值
     * @param int $pos 指定的位置（数字）
     * @return array|bool
     */
    public function getEleByPos($pos = 0){
        if (intval($pos) > count($this -> ol) || intval($pos) < 1){
            return false;
        }
        reset($this -> ol);
        $i = 1;
        //返回数组中当前的键／值对并将数组指针向前移动一步
        while(list($key , $value) = each($this -> ol)){
            if($i == $pos){
                return ['key' => $key, 'value' => $value];
            }
            $i++;
        }
    }

    /**
     * 获取元素在顺序表中的位置
     * @param $value
     */
    public function getElementPosition($value){
        foreach ($this -> ol as $k => $v){
            if($v == $value){
                return $k;
            }
        }
        return -1;
    }

    /**
     * 获取元素的前驱
     * @param $data 传入的数值
     * @param int $tag=0，则data 为key, $tag=2则data为value
     */
    public function getElementPre($data, $tag = 1){
        $i=0;
        foreach($this->ol as $k => $v){
            next($this -> ol);
            $i++;
            if($tag ==1 ){
                if($data == $k){
                    if($i == 1) return false;
                    prev($this->ol);
                    prev($this->ol);
                    return array('value'=>current($this->ol),'key'=>key($this->ol));
                }
            }
            if($tag == 2){
                if($data == $v){
                    if($i == 1) return false;
                    prev($this->ol);
                    prev($this->ol);
                    return array('value'=>current($this->ol),'key'=>key($this->ol));
                }
            }
        }
    }

    /**
     * 获取元素的后继
     * @param $data
     * @param int $tag $tag=1，则data为key, $tag=2则data为value
     */
    public function getElementNext($data, $tag = 2){
        $i = 0 ;
        reset($this ->ol);
        while(list($key, $value) = each($this ->ol)){
            $i++;
            if($tag == 1){
                if($data == $key){
                    return ['key' => key($this -> ol) , 'value' => current($this ->ol)];
                }
            }elseif($tag == 2){
                if($data == $value){
                    return ['key' => key($this -> ol) , 'value' => current($this -> ol)];
                }
            }
        }

//        foreach ($this -> ol as $k => $v){
//            next($this -> ol);
//            $i++;
//            if($tag == 1){
//                if ($data == $k){
//                    if($i +1 == $k) return false;
//                    prev($this -> ol);
//                    return array('value'=>current($this->ol),'key'=>key($this->ol));
//                }
//            }elseif($tag == 2){
//                if ($data == $v){
//                    if ($i + 1 == $k) return false;
//                    prev($this -> ol);
//                    return array('value'=>current($this->ol),'key'=>key($this->ol));
//                }
//            }
//        }
    }

    public function getInsertElement($p,$value,$key=null,$tag=1){
        $p=(int)$p;
        $len=count($this->ol);
        $ol=array();
        $i=0;
        if($p > $len || $p < 1){
            return false;
        }
        foreach($this->ol as $k=>$v){
            $i++;
            if($i==(int)$p){
                if($tag == 1){
                    $ol[]=$value;
                }else if($tag == 2){
                    $keys=array_keys($ol);
                    $j=0;
                    if(is_int($key)){
                        while(in_array($key,$keys,true)){
                            $key++;
                        }
                    }else{
                        while(in_array($key,$keys,true)){
                            $j++;
                            $key.=(string)$j;
                        }
                    }
                    $ol[$key]=$value;
                }else{
                    return false;
                }
                $key=$k;
                $j=0;
                $keys=array_keys($ol);
                if(is_int($key)){
                    $ol[]=$v;
                }else{
                    while(in_array($key,$keys,true)){
                        $j++;
                        $key.=(string)$j;
                    }
                    $ol[$key]=$v;
                }
            }else{
                if($i>$p){
                    $key=$k;
                    $j=0;
                    $keys=array_keys($ol);
                    if(is_int($key)){
                        $ol[]=$v;
                    }else{
                        while(in_array($key,$keys,true)){
                            $j++;
                            $key.=(string)$j;
                        }
                        $ol[$key]=$v;
                    }
                }else{
                    if(is_int($k)){
                        $ol[]=$v;
                    }else{
                        $ol[$k]=$v;
                    }
                }
            }
        }
        $this->ol=$ol;
        return true;
    }

    /**
     * 根据下标或者元素值删除顺序表中的某个元素
     *
     * @param mixed $value 元素下标或者值
     * @param int $tag 1表示$value为下标，2表示$value为元素值
     * @return bool 成功返回true,失败返回false
     * */
    public function getDeleteElement($value,$tag=1){
        $len=count($this->ol);
        foreach($this->ol as $k=>$v){
            if($tag == 1){
                if(strcmp($k,$value) === 0){
                }else{
                    if(is_int($k)){
                        $ol[]=$v;
                    }else{
                        $ol[$k]=$v;
                    }
                }
            }
            if($tag ==2){
                if(strcmp($v,$value) === 0){
                }else{
                    if(is_int($k)){
                        $ol[]=$v;
                    }else{
                        $ol[$k]=$v;
                    }
                }
            }
        }
        $this->ol=$ol;
        if(count($this->ol) == $len){
            return false;
        }
        return true;
    }
    /**
     * 根据元素位置删除顺序表中的某个元素
     *
     * @param int $position 元素位置从1开始
     * @return bool 成功返回true,失败返回false
     * */
    public function getDeleteEleForPos($position){
        $len=count($this->ol);
        $i=0;
        $position=(int)$position;
        if($position > $len || $position < 1){
            return false;
        }
        foreach($this->ol as $k=>$v){
            $i++;
            if($i == $position){
            }else{
                if(is_int($k)){
                    $ol[]=$v;
                }else{
                    $ol[$k]=$v;
                }
            }
        }
        $this->ol=$ol;
        if(count($this->ol) == $len){
            return false;
        }
        return true;
    }

    /**
     * 清空顺序表
     */
    public function __destruct(){
        foreach ($this -> ol as $key => $value){
            unset ($this -> ol[$key]);
        }
    }
}

$var = ['hello' , 'word' , 'php' , 'python' , 'node' , 'c++'];
//$var = [
//    'name' => 'php',
//    'age' => 20,
//    'hobby' =>'like study'
//];
$ordernearlist = new OrderNearList($var);
print_r($ordernearlist);
echo '<br/>';
//var_dump($ordernearlist -> getElementPre('node' ,2));
//var_dump($ordernearlist -> getElementNext('python' ,2));
//print_r($ordernearlist -> getEleByPos(1));
echo '<br/>';
print_r($ordernearlist->getElementNext(0 ,1));