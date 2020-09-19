<?php

function foo($var1, $var2){
    echo "foo".PHP_EOL;
    echo "var1:{$var1},var2:{$var2}";
}

call_user_func("foo","hello","world");

call_user_func_array("foo",["hello", "world", "!"]);

class A{
    static public $b = 1;
    static function show($var1, $var2){
        echo self::$b;
    }
}

call_user_func_array(["A","show"],["hello","word"]);