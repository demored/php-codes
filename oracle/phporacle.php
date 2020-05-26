<?php
//phpinfo();
//exit;

$conn = ocilogon('system','123456','127.0.0.1:1521/orcl');

if (!$conn){
    $Error = oci_error();
    print htmlentities($Error['message']);
    exit;
}else{
    echo "Connected Oracle Successd!"."<br>";
}
