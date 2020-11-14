
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ajax请求数据，形成进度条</title>
</head>
<script src="jquery-3.3.1.min.js"></script>
<div>
    <p>内容显示</p>
    <div id="show"></div>
</div>
<body>
<script>
   function echo_msg(msg){
	    $('#show').append('<p>'+msg+'</p>');
    }
</script>
</body>
</html>

<?php
//终止缓存
//下面这句是必须的
header('X-Accel-Buffering: no');
//ob_end_clean();
//@ob_implicit_flush(1);
$msg = "this is install";
for($i = 0 ; $i<3 ; $i++){
    echo '<script type="text/javascript">echo_msg("'.addslashes($msg).$i.'");</script>';
    ob_flush();
    flush();
    sleep(1);
}
?>
