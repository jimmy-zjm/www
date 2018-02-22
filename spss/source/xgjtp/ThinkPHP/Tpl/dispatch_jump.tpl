<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
</head>
<body>
    <div style="width:700px;margin:0 auto;margin-top:50px;">
        <div style="150px;float:left;">
            <?php if(isset($message)): ?>
            <img src="__PUBLIC__/Admin/images/1.png" style="width:150px;"/>
            <?php else: ?>
            <img src="__PUBLIC__/Admin/images/2.png" style="width:150px;"/>
            <?php endif; ?>
        </div>
        <div style="width:530px;float:left;margin-left:20px;margin-top:10px;">
            <div style="width:550px;">
                <span style="font-size:22px;font-weight:bold;color:#2b2b2b;">
                    <?php if(isset($message)): ?>
                        <b style="color:green"><?php echo $message ?></b>
                    <?php else: ?>
                        <b style="color:red"><?php echo $error ?></b>
                    <?php endif; ?>
                </span>
            </div>
            <div style="background:#eb5409;border:1px solid #eb5409;border-radius:3px;width:100px;height:25px;line-height:25px;text-align:center;margin-top:30px;">
                <a id="href" href="<?php echo($jumpUrl); ?>" style="color:#fff;font-size:18px;display:block;text-decoration:none;">返回</a>
            </div>
            <p class="jump">
            页面自动跳转等待时间： <b id="wait"><?php echo($waitSecond); ?></b>
            </p>
            <div style="clear:both;">
        </div>
        <div style="clear:both;">
        </div>
    </div>
    <script type="text/javascript">
        (function(){
        var wait = document.getElementById('wait'),href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
        })();
    </script>
</body>
</html>
