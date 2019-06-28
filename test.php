<?php
/**
 * 本文件主要用于在工作中临时文本的编辑
 */

phpinfo();


class HTTP{

    public function setHeader(){

    }

    public function getHeader(){

    }

}

$time = time();

echo $time.'-'.date('Y-m-d H:i:s',$time);
echo '<br/>';

echo 'token_expires:1544590075-'.date('Y-m-d H:i:s','1544590075');
echo '<br/>';
echo 'date_time:1544582875-'.date('Y-m-d H:i:s','1544582875');