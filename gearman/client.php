<?php

//同步client
$client = new GearmanClient();
$client ->addServer();

$msg = "hello world";
echo "Sending $msg \n";
echo "Success: ", $client->doNormal("reverse", $msg), "\n";
