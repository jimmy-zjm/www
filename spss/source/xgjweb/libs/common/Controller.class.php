<?php
/**
 * 基础控制器
 * @date 2016-3-10
 * @author grass <14712905@qq.com>
 */
class Controller extends Smarty{
    public function __construct(){
        parent::__construct();
        $this->settemplatedir(WWW_DIR ."/templates/");
        $this->setCompileDir(WWW_DIR ."/templates_c/");
        $this->setConfigDir(WWW_DIR . "/conf/Smarty/");
        $this->left_delimiter    = C('TMPL_L_DELIM');
        // Smarty::muteExpectedErrors();
        $this->right_delimiter   = C('TMPL_R_DELIM');
        $this->caching = false;//是否开启缓存
        $this->debugging   =   false;
    }

    protected function checkLogin($url=''){
        //验证用户登陆状态, 必须登陆
        $user_id = session('userId');
        if(empty($user_id)){
            if(empty($url)){
                $url = $_SERVER['PHP_SELF'];
            }
            $_SESSION['redirect_url'] = $url;
            $this->error('此操作必须登录!','user.php?login');
        }
    }


    public function success($message, $url='', $time=3){
        $this->jump($message, $url, true, $time);
    }

    public function error($message, $url='', $time=3){
        $this->jump($message, $url, false, $time);
    }

    public function redirect($url){
        $this->jump(null, $url);
    }

    /**
     * [提示一个信息,并跳转到指定网站]
     * @param  string $url     跳转的url地址
     * @param  string $message 提示的信息
     * @param  bool   $mode   正确还是 错误  true为正确
     * @param  int    $time    提示的时间
     * @return void
     */
    //jump('sss',"提示的时间");
    private function jump($message='',$url='',$mode=true,$time=3){

        $img   = $mode?'yes':'no';
        $color = $mode?'blue':'orange';
        // $path  = ADMIN_URL;

        if(empty($message)){
            header('Location:' . $url);
        }else{
            echo <<<HTML
            <!DOCTYPE html>
            <html><body>
            <meta http-equiv='refresh' content='{$time};url={$url}' />
            <style>#box{width:450px;border:1px solid #ccc;margin:80px auto;text-align:center;box-shadow:2px 2px 7px 2px rgba(50 ,40,40,0.5);border-radius:3px;}body{margin:0;background:#ddd;}#box h2{display:block;height:35px;line-height:35px;font-weight:700;font-size:14px;padding:0 0 0 10px;background:#eee;border-bottom:1px solid #ccc;margin:0}#box .cont{background:#fff;text-align:center;padding:60px}#box .cont .wrap{margin:5px auto}#box .cont .wrap img{margin:-10px 0 0 0;vertical-align:middle;float:left}#box .cont .wrap p{display:inline-block;margin:0;width:290px;float:left}#box .cont a{width:150px;height:30px;border:1px solid #ccc;background:#FEFEFE;display:inline-block;margin:30px 0 0 0;line-height:30px;text-align:center;font-size:13px;text-decoration:none;color:#000;margin:15px auto;border-radius:4px;transition:all 0.5s;}#box .cont a:hover{background:#eee;transition:all 0.5s;}</style>
            <div id="box"><h2>温馨提示:</h2><div class="cont"><div class="wrap"><img src="images/{$img}.png" width="35px" />
            <p class="msg" style="color:{$color}">{$message},<b style="color:red" id="wait">{$time}</b>秒后自动跳转</p><div style="clear:both"></div></div><a id="href" href="{$url}">立即跳转</a></div></div>
            <script>
            var wait = document.getElementById('wait'),href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    if(href == ''){
                        window.history.go(-1);
                    }else{
                        location.href = href;
                    }
                    clearInterval(interval);
                };
            }, 1000);
            </script>
            </body></html>
HTML;
        }
        die;
    }

}