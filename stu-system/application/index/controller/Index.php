<?php
namespace app\index\controller;
use think\Controller;
class Index extends Controller
{
    //主要做一个导航
    public function index()
    {
        return $this -> fetch();
    }

    //上传大文件
    public function upload_big_excel(){
        if(request()->isPost()){
            echo "hello world";
            exit;
        }else{
            return $this -> fetch();
        }
    }
}
