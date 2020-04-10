<?php
/**
 * 本文件主要用于在工作中临时文本的编辑
 */

//phpinfo();

$a =3;

echo $a;

exit;

for ($i = 1; $i<100000; $i++){
    $arr[$i] = $i*$i;
    $arr[$i-1] = $a[$i];
}