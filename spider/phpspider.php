<?php
//phpsplider爬虫
require_once __DIR__ . './phpspider/autoloader.php';
use phpspider\core\phpspider;
/* Do NOT delete this comment */
/* 不要删除这段注释 */
$configs = array(
    'name' => '糗事百科',
    'log_show' => false,
    'export' => array(
        'type' => 'db',
        'table' => 'phpslider', // data目录下
    ),
    'db_config' => array(
        'host'  => '127.0.0.1',
        'port'  => 3306,
        'user'  => 'root',
        'pass'  => 'root',
        'name'  => 'test',
    ),
    'domains' => array(
        'qiushibaike.com',
        'www.qiushibaike.com'
    ),
    'scan_urls' => array(
        'https://www.qiushibaike.com/'
    ),
    'content_url_regexes' => array(
        "https://www.qiushibaike.com/article/\d+"
    ),
    'list_url_regexes' => array(
        "http://www.qiushibaike.com/8hr/page/\d+\?s=\d+"
    ),
    'fields' => array(
        array(
            // 抽取内容页的文章内容
            'name' => "article_content",
            'selector' => "//*[@id='single-next-link']",
            'required' => true
        ),
        array(
            // 抽取内容页的文章作者
            'name' => "article_author",
            'selector' => "//div[contains(@class,'author')]//h2",
            'required' => true
        ),
    ),
);

$spider = new phpspider($configs);
$spider->on_extract_field = function($fieldname, $data, $page){
    if($fieldname == 'article_content'){
        return 'helloword';
    }
};
$spider ->start();

//$spider->on_start = function($phpspider){
////    \phpspider\core\requests::$input_encoding = 'utf-8';
////    \phpspider\core\requests::$output_encoding = 'utf-8';
//};


