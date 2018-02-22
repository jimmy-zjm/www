<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends BaseController {

	public function _initialize() { 
		parent::_initialize();
		$this->assign('now','1');
	}

    //登录
	public function login(){
		layout(false);
        $this->display();
	}

	
    //个人中心
	public function userCenter(){

		$user = D('index')->user();	
//var_dump($user);
		$this->assign('user',$user);

		$this->display();
	}

    //修改个人信息
	public function info(){

		$user = D('index')->user();	
		
		// if ($user['pid']!='0') {
		// 	$this->redirect('User/psd');
		// }

		$user['n'] = substr($user['birthday'], 0,4);
		$user['y'] = substr($user['birthday'], 4,2);
		$user['r'] = substr($user['birthday'], 6,2);

		$this->assign('user',$user);

		
		
		$this->display();
	}

	//修改个人信息
	public function doInfo(){
		layout(false);

		$user = D('index')->user();	
		if ($user['pid']!='0') {
			echo '您没有此权限';exit;
		}
		
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


}