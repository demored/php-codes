<?php
namespace app\index\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

//每分钟检测 设备的存活状态
//如果上次心跳时间距离现在超过5分钟，视为离线
//crontab 定时器，使用绝对路径
//* * * * * /usr/local/php /data/wwwroot/.../think testalived >> /tmp/test

class Testalived extends Command
{
    protected function configure()
    {
        $this->setName('testalived');
    }

    protected function execute(Input $input, Output $output)
    {
//        $output->writeln("TestCommand:");

        $now_time = time();
        $gap_time = 300; //5分钟
        Db::name("devices") -> where(["update_time" => ["gt" => $now_time - $gap_time]]) -> update(["is_alived" => 1]);
    }
}