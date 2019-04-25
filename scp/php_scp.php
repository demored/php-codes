<?php
/**
 * 用php使用scp进行文件分发
 */

$content=<<<EOT
<html>
<title>hacker!!</title>
<head>
<style type="text/css">
#canvas {display: block;}</style>

</head><BODY>
<canvas id="c"></canvas>
<script language="JavaScript">
var c = document.getElementById("c");
var ctx = c.getContext("2d");

//全屏
c.height = window.innerHeight;
c.width = window.innerWidth;

//文字
var txts = "0123456789";
//转为数组
txts = txts.split("");

var font_size = 16;
var columns = c.width/font_size;
//用于计算输出文字时坐标，所以长度即为列数
var drops = [];
//初始值
for(var x = 0; x < columns; x++)
    drops[x] = 1;

//输出文字
function draw()
{
    //让背景逐渐由透明到不透明
    ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
    ctx.fillRect(0, 0, c.width, c.height);

    ctx.fillStyle = "#0F0"; //文字颜色
    ctx.font = font_size + "px arial";
    //逐行输出文字
    for(var i = 0; i < drops.length; i++)
    {
        //随机取要输出的文字
        var text = txts[Math.floor(Math.random()*txts.length)];
        //输出文字，注意坐标的计算
        ctx.fillText(text, i*font_size, drops[i]*font_size);

        //如果绘满一屏或随机数大于0.95（此数可自行调整，效果会不同）
        if(drops[i]*font_size > c.height || Math.random() > 0.95)
            drops[i] = 0;

        //用于Y轴坐标增加
        drops[i]++;
    }
}

setInterval(draw, 33);
</script>
</body>
</html>
EOT;

exit;
set_time_limit(0);
$host = '139.224.3.132';
$port = '22';
$ssh2 = ssh2_connect($host, $port);
ssh2_auth_password($ssh2, 'root', 'zyk139123!@#');
for($i = 1 ; $i <= 1000 ; $i++) {
    sleep(10);
    $local_file = "index.php";

//    fopen($local_file , 'wr');
//    file_put_contents($local_file ,$content);

    $remote_file = "/data/wwwroot/zykcloud/wwwroot/$local_file";
    $stream = ssh2_scp_send($ssh2, $local_file, $remote_file, 0644);
    if ($stream)
        echo "attack [$i]success <br/>";
}
