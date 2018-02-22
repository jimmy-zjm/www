<?php
namespace Mobile\Controller;

// use Think\Controller;
class UserController extends BaseController {

    public function register(){
        $this->display();
    }

    //执行注册
    public function doRegister(){

		$tel    = I('post.mobile_phone');
		$pass   = I('post.password');
		$repass = I('post.repassword');
		$msg 	= I('post.msg');

		foreach ($_POST as $key => $v) {
			if (empty($v)) $this->error('请填写完整再提交');
		}

		if (time()-$_SESSION['msgTime'] > 180)      $this->error('验证码已过期，请重新获取！');

		if(!preg_match("/^1[34578]\d{9}$/", $tel)) 	$this->error('请填写正确的手机号码！!');
		if(!preg_match("/^\w{6,15}$/", $pass)) 		$this->error('请正确填写密码，密码为6-15位！!');
		
		if ($pass !== $repass)  	  				$this->error('两次密码不一致请重新输入！');
		if ($msg  != $_SESSION['msg']) 				$this->error('验证码错误!');
		if ($tel  != $_SESSION['tel']) 				$this->error('手机号码与获取验证码号码不一致!');

		$map['mobile_phone'] = $tel;
    	$re = M('xgj_users')->where($map)->count();
    	if ($re > 0)  	  			  				$this->error('您已注册过，不可再次注册!');


		$_POST['password'] = md5($pass.C(MD5_PASSWORD));
		$_POST['reg_time'] = time();

		$data = M('xgj_users')->create();
		$return = M('xgj_users')->add();

		if (empty($return)) $this->error('注册失败！请重试!');

		$_SESSION['user']['userId'] = $return;

		if (!empty($_SESSION['url'])) $url = $_SESSION['url'];
		else $url = 'Index/index';
		
		echo U("$url");
    }

    //重置密码
    function doReset(){
    	$tel    = I('post.mobile_phone');
		$pass   = I('post.password');
		$repass = I('post.repassword');
		$msg 	= I('post.msg');

		foreach ($_POST as $key => $v) {
			if (empty($v)) $this->error('请填写完整再提交');
		}

		if (time()-$_SESSION['msgTime'] > 180)      $this->error('验证码已过期，请重新获取！');

		if(!preg_match("/^1[34578]\d{9}$/", $tel)) 	$this->error('请填写正确的手机号码！!');
		if(!preg_match("/^\w{6,15}$/", $pass)) 		$this->error('请正确填写密码，密码为6-15位！!');
		
		if ($pass !== $repass)  	  				$this->error('两次密码不一致请重新输入！');
		if ($msg  != $_SESSION['msg']) 				$this->error('验证码错误!');
		if ($tel  != $_SESSION['tel']) 				$this->error('手机号码与获取验证码号码不一致!');

		$map['mobile_phone'] = $tel;
    	$re = M('xgj_users')->where($map)->count();
    	if ($re < 0)  	  			  				$this->error('不存在该手机号!');

		$password = md5($pass.C(MD5_PASSWORD));

		$return = M('xgj_users')->where($map)->save(['password'=>$password]);


		if (empty($return)){
			$this->error('重置密码失败！请重试!');
		}else{
			unset($_SESSION['user']);
			echo U("User/login");
		} 
    }

    //退出
    public function outLogin(){
    	unset($_SESSION['user']);
    	//执行跳转
		$this->redirect('Index/index');
    }

    //发送验证码
    public function message(){
		$tel=$_POST['tel'];
		$rest=getMessage($tel);
		if($rest['success']===true){
			echo 1;
		}else{
			echo -1;
		}
	}

    //检测手机号是否已注册
    public function checkRegister(){
    	$map['mobile_phone'] = I('post.tel');
    	$re = M('xgj_users')->where($map)->count();
    	echo $re;
    }

    //验证验证码
	public function verification(){
		$msg = I('post.msg');
		$tel = I('post.tel');
		if (time()-$_SESSION['msgTime'] > 180){
			echo '验证码已过期，请重新获取！';
		}else if ($msg != $_SESSION['msg']) {
			echo '验证码错误';
		}else if($tel != $_SESSION['tel']){
			echo '手机号码与获取验证码号码不一致';
		}else if ($msg == $_SESSION['msg'] && $tel == $_SESSION['tel']) {
			echo '1';
		}else{
			echo '验证失败';
		}
	}

    public function login(){

        $this->display();
    }

    public function find(){
    	
        $this->display();
    }

}