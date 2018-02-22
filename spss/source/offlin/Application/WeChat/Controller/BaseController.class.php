<?php
namespace WeChat\Controller;
use Think\Controller;
class BaseController extends Controller {
    /*
     * 初始化操作
     */
    public function _initialize() {  
    	$this->url();
    	if(!empty($_SESSION['userId'])){
    		$res=M('xgj_users')->where(['user_id'=>$_SESSION['userId']])->limit(1)->find();
			if(ACTION_NAME!=='findpsd' && ACTION_NAME!=='login' && ACTION_NAME!=='doLogin'&& ACTION_NAME!=='goOut' && empty($res)){
	    		$this->doLogin();
	    	}
	    }else{
	    	if(ACTION_NAME!=='findpsd' && ACTION_NAME!=='login' && ACTION_NAME!=='doLogin'&& ACTION_NAME!=='goOut'){
	    		$this->doLogin();
	    	}
	    }

    }
    //获取跳转URL地址
    public function url(){
		if (!in_array(ACTION_NAME, C(URL_NO_RECORD)) && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			$url = CONTROLLER_NAME.'/'.ACTION_NAME;
			if(!empty($_GET)) {
				foreach ($_GET as $k => $v) {
					$url .= '/'.$k.'/'.$v;
				}
			}
			$_SESSION['wurl'] = $url;
		}
    }

    //执行登录
    public function doLogin(){
    	layout(false);
    	if(empty($_POST['tel']) || empty($_POST['pass'])){
    		if(!empty($_COOKIE['autoTel']) && !empty($_COOKIE['autoPsd'])){
		        $tel=$_COOKIE['autoTel'];
	        	$pass=$_COOKIE['autoPsd'];
		        $res=M('xgj_users')->field('user_id,user_name,mobile_phone,password')->where(['mobile_phone'=>$tel,'password'=>$pass])->limit(1)->find();
		        if($res){
		        	/*更新登录信息*/
					$data = array(
						'last_time' =>time(),
						'last_ip'   =>get_client_ip()
						);
					$usersMap['user_id'] = $res['user_id'];
					$re = M('xgj_users')->where($usersMap)->save($data);
		        	if($re!==false){
				    	$_SESSION['userId']=$res['user_id'];
				        $_SESSION['userName']=$res['user_name'];
	        			$this->redirect('Order/index');exit;
			    	}else{
	        			$this->redirect('User/login');exit;
			    	}
		        }else{
		        	setcookie('autoTel','',time()-86400*7,'/');
	            	setcookie('autoPsd','',time()-86400*7,'/');
	        		$this->redirect('User/login');exit;
		        }
	        }else{
	        	setcookie('autoTel','',time()-86400*7,'/');
	            setcookie('autoPsd','',time()-86400*7,'/');
	        	$this->redirect('User/login');exit;
	        }
    	}else{
    		$auto=isset($_POST['auto'])?$_POST['auto']:'';
        	$tel=I('tel');
        	$pass=I('pass');
	        $psd=md5($pass.C('MD5_PASSWORD'));
	        /*验证用户名或者密码是否为空或者格式错误*/
			if(empty($tel) || !preg_match("/^1[34578]\d{9}$/", $tel) || empty($pass) || !preg_match("/^\w{6,15}$/", $pass)){
				echo '2';exit;
			}  	
	        $res=M('xgj_users')->field('user_id,user_name,mobile_phone,password')->where(['mobile_phone'=>$tel,'password'=>$psd])->limit(1)->find();
	        //var_dump($res,$tel,$psd);die;
	        if(empty($res)){
	        	echo '3';exit;
	        }
	        /*更新登录信息*/
			$data = array(
				'last_time' =>time(),
				'last_ip'   =>get_client_ip()
				);
			$usersMap['user_id'] = $res['user_id'];
			$re = M('xgj_users')->where($usersMap)->save($data);
	    	if($re!==false){
	    		if($auto==1){
		            //自动登陆, 保存登陆信息一个礼拜
		            setcookie('autoTel',$res['mobile_phone'],time()+86400*7,'/');
		            setcookie('autoPsd',$res['password'],time()+86400*7,'/');
		        }
		    	$_SESSION['userId']=$res['user_id'];
		        $_SESSION['userName']=$res['user_name'];
				echo '1';exit;
	    	}else{
	    		echo '4';exit;
	    	}
    	}
    }
}