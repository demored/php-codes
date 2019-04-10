<?php
//同步worker

$worker = new GearmanWorker();
$worker ->addServer();
$worker ->addFunction("reverse","reverse_do");
echo "Waiting for job...\n";
while($worker ->work());

function reverse_do($job){
    $workload = $job -> workload();
    echo "workload :$workload\n";
    $result = strrev($workload);
    echo "Result:$result\n";
    return $result;
}