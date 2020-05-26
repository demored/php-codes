<?php

//go(function(){
//    echo "111\n";
//});
//
//
//echo "2222\n";
//
//go(function(){
//    echo "3333\n";
//});


go(function(){
    Co::sleep(1); //模拟IO等待
    echo "1\n";
});

echo "2\n";

go(function(){
    echo "3\n";
});










