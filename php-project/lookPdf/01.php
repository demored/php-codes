<?php
// +----------------------------------------------------------------------
// | User: demored Date: 2019/9/17 Time: 15:16
// +----------------------------------------------------------------------
// | Copyright (c) 2019 demored All rights reserved.
// +----------------------------------------------------------------------
// | desc: php预览pdf
// +----------------------------------------------------------------------

$pdf_file = "E:\python-codes\word2pdf\\tmp\\01.pdf";
$pdf_file = str_replace("\\", "/", $pdf_file);
//if(file_exists($pdf_file)){
//    echo "file exists<br/>";
//}else{
//    echo "file not exists<br/>";
//}

//$file = $filename = $pdf_file;
//
//$filename = 'filename.pdf';
//
//header('Content-type: application/pdf');
//
//header('Content-Disposition: inline; filename="' . $filename . '"');
//
//header('Content-Transfer-Encoding: binary');
//
//header('Accept-Ranges: bytes');
//
//// 读取文件
//
//@readfile($file);

$filename = $pdf_file;

// Header content type
header("Content-type: application/pdf");
header("Content-Length: " . filesize($filename));
// 将文件发送到浏览器。
readfile($filename);




