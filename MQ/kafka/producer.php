<?php

require_once('./vendor/autoload.php');

$produce = \Kafka\Produce::getInstance('localhost:2181', 3000);

$produce->setRequireAck(-1);

$topicName = 'test';
//获取到topic下可用的partitions
$partitions = $produce->getAvailablePartitions($topicName);
$partitionCount = count($partitions);

$count = 1;
while(true){
    $message = json_encode(array('uid' => $count, 'age' => $count%100, 'datetime' => date('Y-m-d H:i:s')));

    //发送消息到不同的partition
    $partitionId = $count%$partitionCount;
    $produce->setMessages(topicName, $partitionId, array($message));
    $result = $produce->send();
    var_dump($result);
    
    $count++;
    echo "producer sleeping\n";
    sleep(1);

    $partitionId = $count%
}