<?php
//php 操作elasticsearch
//简单添加索引

require 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;
$hosts = [
    '192.168.149.133:9200'
];

$client = ClientBuilder::create()->setHosts($hosts)->build();
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id',
    'body' => ['testField' => 'abc']
];

$response = $client->index($params);
print_r($response);
/*
成功后的输出：
Array
(
    [_index] => my_index
    [_type] => my_type
    [_id] => my_id
    [_version] => 1
    [result] => created
    [_shards] => Array
        (
            [total] => 2
            [successful] => 1
            [failed] => 0
        )

    [created] => 1
)



 */