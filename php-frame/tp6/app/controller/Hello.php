<?php
namespace app\controller;

class Hello{

    protected $middleware = [\app\middleware\Check::class];

    public function index(){
        echo "this is a function";
    }

}