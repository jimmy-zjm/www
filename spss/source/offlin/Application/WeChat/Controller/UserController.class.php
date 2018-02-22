<?php
namespace WeChat\Controller;
use Think\Controller;
class UserController extends BaseController {

    
	public function index(){
		$this->display();
	}

    //登录
	public function login(){
        $this->display();
	}

	


    //修改个人信息
	public function info(){

		$user = D('index')->user();	
		
		if ($user['pid']!='0') {
			$this->redirect('User/psd');
		}

		$user['n'] = substr($user['birthday'], 0,4);
		$user['y'] = substr($user['birthday'], 4,2);
		$user['r'] = substr($user['birthday'], 6,2);

		$this->assign('user',$user);

		
		
		$this->display();
	}

	//修改个人信息
	public function doInfo(){
		layout(false);
		
		$data=array(
				'real_name'=>I('real_name'),
				'number'=>I('number'),
				'email'=>I('email'),
				'tel'=>intval($_POST['tel']),
				'birthday'=>str_replace(array("-"),"",I('birthday')),
				'sex'=>intval($_POST['sex']),
				'add_time'=>intval(time()),
		);
		if(M('pad_user')->where(array('id'=>$_SESSION['dealerId']))->save($data) !== false){
			echo "1";exit;
		}else{
			echo "2";exit;
		}
	}
	//修改密码
	public function psd(){
		
		
		
		$this->display();
	}
	//修改密码
	public function doPsd(){
		layout(false);
		$res=M('pad_user')->where(array('id'=>$_SESSION['dealerId']))->getField('psd');
		if(md5(I('oldpsd').C('MD5_PAD_PSD'))==$res){
			if(I('newpsd') != I('newpsd1')){
				echo '4';exit;
			}
			$data=array(
				'spsd'=> I('newpsd'),
				'psd'=>md5( I('newpsd').C('MD5_PAD_PSD')),
			);
			if(M('pad_user')->where(array('id'=>$_SESSION['dealerId']))->save($data)  !== false){
				echo "1";exit;
			}else{
				echo "2";exit;
			}
		}
	}

	// 找回密码
	public function findpsd(){
		$this->display();
	}
	//退出
	public function goOut(){
		session_unset();
		session_destroy();
		$this->redirect('User/login');
	
	}

	//检测手机号是否注册
	public function checkTel(){
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
	//发送验证码
    public function message(){
		$verify = I('post.verify');
		$re = $this->check_verify($verify);
		if ($re == true) {
			$tel 	= I('post.tel');
			$rest=getMessage($tel);
			if($rest['success']===true){
				echo 1;
			}else{
				echo -1;
			}
		}else{
			echo '2';
		}
	}


    //验证码
    public function verify(){
    	$config = array(
            'length'=>4,
            'fontSize'=>24,
            'useCurve'=>false,
            'useNoise'=>false,
        );
        $verify = new \Think\Verify($config);
        $verify->entry();
    }

    function check_verify($code, $id = ''){
	    $verify = new \Think\Verify();
	    return $verify->check($code, $id);
	}

	 //执行注册
    public function doRegister(){
    	layout(false);

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


		$_POST['password'] = md5($pass.C('MD5_PASSWORD'));


		$return = M('xgj_users')->where($map)->save(['password'=>$_POST['password']]);

		if (empty($return)) $this->error('密码修改失败！请重试!');

		$url = 'User/login';
		
		echo U("$url");
		// $this->redirect($url);
    }

}