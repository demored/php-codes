<?php

use Swoole\Coroutine;
use function Swoole\Coroutine\run;

echo "main start\n";

//1、父子协程执行顺序，协程遇到sleep会被挂起交，将cpu交给其他协程
//run(function () {
//
//    echo "coro " . Coroutine::getcid() . " start\n";
//
//    Coroutine::create(function() {
//        echo "coro " . Coroutine::getcid() . " start\n";
//        Coroutine::sleep(1);
//        echo "coro " . Coroutine::getcid() . " end\n";
//    });
//
//    Coroutine::create(function() {
//        echo "coro " . Coroutine::getcid() . " start\n";
//        Coroutine::sleep(1);
//        echo "coro " . Coroutine::getcid() . " end\n";
//
//    });
//    echo "coro " . Coroutine::getcid() . " do not wait children coroutine\n";
//});
//
//echo "done\n";

//2、测试协程的性能
// time php coroutine.php

//执行4个普通任务
//for ($i = 1; $i <= 4; $i++){
//    sleep(1);
//    echo microtime(true) .": hello $i \n";
//}
//echo "main hello";

//单个协程版
//go(function(){
//    for ($i = 1; $i <= 4; $i++) {
//        Co::sleep(1);
//        echo microtime(true) . ": hello $i \n";
//    };
//});
//echo "hello main \n";

//多个协程版
$n = 4;
for ($i = 0; $i < $n; $i++) {
    go(function () use ($i) {
        Co::sleep(1);
        echo microtime(true) . ": hello $i \n";
    });
};
echo "hello main \n";






















