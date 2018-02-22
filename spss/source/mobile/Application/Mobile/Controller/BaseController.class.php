<?php
namespace Mobile\Controller;
use Think\Controller;
class BaseController extends Controller {
    /*
     * 初始化操作
     */
    public function _initialize() {  
    	$this->url();
    	if(isset($_COOKIE['auto_tel']) && isset($_COOKIE['auto_psd']) && empty($_SESSION['user']['userId']) && empty($_SESSION['auto'])){
    		$this->doLogin();
    	}
    	if(empty($_SESSION['user']['userId'])) $this->isLogin();
    }

    //判断是否是需要登录的页面是的话跳转登录页面
    public function isLogin(){
        $keys = array_keys(C(IS_LOGIN));
        if (in_array(CONTROLLER_NAME, $keys)) {
            $controller = C(IS_LOGIN)[CONTROLLER_NAME];
            if ($controller['0']=='all' || in_array(ACTION_NAME,$controller)) $this->redirect('User/login');
        }
    }

    //获取跳转URL地址
    public function url(){
		if (ACTION_NAME!='doLogin' && ACTION_NAME!='register' && ACTION_NAME!='doRegister' && ACTION_NAME!='find' && ACTION_NAME!='login' && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			$url = CONTROLLER_NAME.'/'.ACTION_NAME;
			if(!empty($_GET)) {
				foreach ($_GET as $k => $v) {
					$url .= '/'.$k.'/'.$v;
				}
			}
			$_SESSION['url'] = $url;
		}
		if($url=="Order/index" && ACTION_NAME!='login'){
			$_SESSION['url'] = "Index/index";
		}
    }

    //执行登录
    public function doLogin(){

    	// if (!empty($_COOKIE['auto_tel']) && !empty($_COOKIE['auto_psd'])) {
    	// 	$mobile_phone = $_COOKIE['auto_tel'];
    	// 	$password     = $_COOKIE['auto_psd'];
    	// 	$repassword   = $password;
    	// }
    	
    	/*接受数据*/
    	if (!empty($_POST)) {
    		$mobile_phone = I('post.tel');
			$password     = I('post.pass');
			$repassword   = md5($password.C(MD5_PASSWORD));
    	}
		
		/*验证用户名或者密码是否为空或者格式错误*/
		if(empty($mobile_phone)) 							 $this->error('请填写手机号码');
		if(empty($password))     							 $this->error('请填写密码');
		if(!preg_match("/^[1][34578][0-9]{9}$/", $mobile_phone))  $this->error('请输入正确手机号码');
		if (!empty($_POST)){
			if(!preg_match("/^\w{6,15}$/", $password))  	 $this->error('请正确填写您的密码');
		}
		

		/*查询用户*/
		$map['mobile_phone'] = array('EQ',$mobile_phone);
		$userData = M('xgj_users')->field('user_id,user_name,password')->where($map)->find();

		/*验证用户名*/
		if (empty($userData)) 					 	$this->error('用户名不存在');
		/*验证密码*/
		if ($userData['password'] !== $repassword){
			if (!empty($_COOKIE['auto_tel']) && !empty($_COOKIE['auto_psd']) && empty($_POST['tel'])) {
				$_SESSION['auto'] = 'false';
				$this->redirect('User/login');
			} else {
				$this->error('密码错误');
			}
		}  

		/*保存用户信息*/
		$_SESSION['user']['userId']   = $userData['user_id'];
		$_SESSION['user']['userName'] = $userData['user_name'];
		
		// /*自动登录设置COOKIE  时限7天*/
		// if(isset($_POST['auto'])) {
		// 	setcookie('auto_tel',$mobile_phone,time()+86400*7,'/');
  //           setcookie('auto_psd',$userData['password'],time()+86400*7,'/');
		// }

		/*更新登录信息*/
		$data = array(
			'last_time' =>time(),
			'last_ip'   =>get_client_ip()
			);
		$usersMap['user_id'] = $userData['user_id'];
		$re = M('xgj_users')->where($usersMap)->save($data);
    	
		//执行跳转
		if (!empty($_SESSION['url'])) $url = $_SESSION['url'];
		else $url = 'Index/index';

		$this->redirect($url);
    }
}