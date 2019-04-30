<?php
/**
 * php实现hash表
 */
class HashNode{
    public $key;    //关键字
    public $value;  //节点的值
    public $nextNode;  //节点
    public function __construct($key , $value , $nextNode = null){
        $this -> key = $key;
        $this -> value = $value;
        $this -> nextNode = $nextNode;
    }
}
class HashTable{
    private $buckets;
    private $size = 10;
    public function __construct(){
        $this ->buckets = new SplFixedArray($this -> size);

    }
    //通过关键字创建hash值
    private function hashFunc($key){
        $len = strlen($key);
        $hashVal = 0;

        for($i = 0 ; $i < $len ; $i++){
            $hashVal += ord($key[$i]);
        }
        return $hashVal % $this -> size;
    }

    //插入数据
    public function insert($key, $val){
        $index = $this -> hashFunc($key);
        if(isset($this -> buckets[$index])){
            $newNode = new HashNode($key , $val , $this -> buckets[$index]);
        }else{
            $newNode = new HashNode($key, $val , null);
        }
        $this -> buckets[$index] = $newNode;    //保存新节点
    }

    //通过关键字查找数据
    public function find($key){
        $index = $this ->hashFunc($key);
        $current = $this -> buckets[$index];
        while(isset($current)){
            if($current -> key == $key){//比较当前节点的关键字和要找查找的关键字是否一致
                return $current -> value;
            }
            $current = $current -> nextNode;//比较下一个节点
        }
        return null;    //查找失败
    }
}

$ht = new HashTable();
$ht -> insert('key1' , 'v1');
$ht -> insert('key2' , 'v2');
echo $ht -> find('key12').PHP_EOL; //key12 和key1算出来的hash值一样，hash冲突
echo $ht -> find('key2').PHP_EOL;//v2





