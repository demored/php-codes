#### 说明
本项目是使用swoole创建一个tcp Server，用作自提柜智能开箱服务端

- 基于swoole tcp服务端监听在9502端口
```bash
#启动tcp server9502端口
php public/index.php index/Tcpserver/start

#重启
kill pid
php public/index.php index/Tcpserver/start


#根据心跳会自动联网，更新句柄

```

- 检测智能柜设备存活状态
```bash
//每分钟检测 设备的存活状态
//如果上次心跳时间距离现在超过5分钟，视为离线
//crontab 定时器，使用绝对路径


* * * * * /usr/local/php /data/wwwroot/.../think testalived >> /tmp/test

```
