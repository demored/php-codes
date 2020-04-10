<?php
// +----------------------------------------------------------------------
// | User: demored Date: 2019/9/16 Time: 11:13
// +----------------------------------------------------------------------
// | Copyright (c) 2019 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: 
// +----------------------------------------------------------------------

$input_file = "source.php";
$output_file = "encode_source.php";
$expire_timestamp = time()+1000000*20;
$encrypt_type = BEAST_ENCRYPT_TYPE_DES;

$encode_result = beast_encode_file($input_file,$output_file,$expire_timestamp, $encrypt_type);
var_dump($encode_result);
