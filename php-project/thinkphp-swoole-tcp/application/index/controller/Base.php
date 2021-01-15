<?php

namespace app\index\controller;
use think\Config;
use think\Controller;
use think\Loader;

/**
 * @title 基类
 */
class Base extends Controller {

    protected function _initialize() {
        parent::_initialize();
        $this->requestInfo();
    }

    /**
     * @title 定义一些系统需要用到的常量
     */
    protected function requestInfo() {

        defined('MODULE_NAME') or define('MODULE_NAME', $this->request->module());
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $this->request->controller());
        defined('ACTION_NAME') or define('ACTION_NAME', $this->request->action());
        defined('IS_POST') or define('IS_POST' , request() ->isPost());
        defined('IS_GET') or define('IS_GET' , request() ->isGet());
        defined('IS_AJAX') or define('IS_AJAX' , request() ->isAjax());
        defined('PREFIX') or define('PREFIX' ,config('database.prefix'));
    }

    //response json格式
    protected function return_json($code = 0 , $msg = "", $data = []){
        header('content-type:application/json');
        $result = [
            "code" => $code,
            "msg" => $msg,
            "data" => $data
        ];
        echo json_encode($result);
        exit;
    }
}
