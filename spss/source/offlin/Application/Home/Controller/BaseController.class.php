<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
    /*
     * 初始化操作
     */
    public function _initialize() {  
    	//$this->url();
	
    	if(!empty($_SESSION['dealerId'])){
    		$res=M('pad_user')->where(['id'=>$_SESSION['dealerId']])->limit(1)->find();
			if($res['level']==0){
				$istry=$res['is_try'];
			}else{
				$com=M('pad_company')->where(array('id'=>$res['c_id']))->find();
				$istry=M('pad_user')->where(array('id'=>$com['u_id']))->getField('is_try');
			}
			$this->assign('istry',$istry);
    		if($res['sessionid']!==session_id() && ACTION_NAME!=='login' && ACTION_NAME!=='doLogin'){
	    		$this->doLogin();
	    	}else{
	    		$this->jurisdiction();
	    	}
    	}else{
    		if(ACTION_NAME!=='login' && ACTION_NAME!=='doLogin'){
	    		$this->doLogin();
	    	}
    	}

		$this->assign('staff',$this->isDisplayStaff());
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
			$_SESSION['ourl'] = $url;
		}
    }

    //执行登录
    public function doLogin(){
    	layout(false);
    	if(empty($_POST['name']) || empty($_POST['psd'])){
    		if(!empty($_COOKIE['auto_name']) && !empty($_COOKIE['auto_psd'])){
		        $name=$_COOKIE['auto_name'];
		        $psd=$_COOKIE['auto_psd'];
		        $res=M('pad_user')->where(['name'=>$name,'psd'=>$psd])->limit(1)->find();
		        if($res['is_use']!==1){
		        	$this->redirect('User/login');exit;
	            }
	            if($res['auto_login']=='1cok'){
	            	$_SESSION['dealerId']=$res['id'];
	                $_SESSION['dealerName']=$res['name'];
	                if(M('pad_user')->where(["id"=>$_SESSION['dealerId']])->save(array('sessionid'=>session_id())) !== false){
						$this->redirect('Index/index');	exit;
					}else{
						$this->redirect('User/login');exit;
					}
	            }
	        }else{
	        	$this->redirect('User/login');exit;
	        }
    	}else{
    		$auto=isset($_POST['auto'])?$_POST['auto']:'';
    		$name=$_POST['name'];
	    	$psd=md5($_POST['psd'].C('MD5_PAD_PSD'));
	        $res=M('pad_user')->where(['name'=>$name,'psd'=>$psd])->find();
	        if(empty($res)){
	        	echo '4';exit;
	        }
	        if($res['is_use']==1){
	            if($auto==1){
	                //自动登陆, 保存登陆信息一个礼拜
	                setcookie('auto_name',$res['name'],time()+86400*7,'/');
	                setcookie('auto_psd',$res['psd'],time()+86400*7,'/');
	                $str='1cok';
	            }else{
	                setcookie('auto_name',$res['name'],time()-86400*7,'/');
	                setcookie('auto_psd',$res['psd'],time()-86400*7,'/');
	                $str='';
	            }
	            $_SESSION['dealerId']=$res['id'];
	            $_SESSION['dealerName']=$res['name'];
                if(M('pad_user')->where(["id"=>$_SESSION['dealerId']])->save(array('sessionid'=>session_id(),'auto_login'=>$str)) !== false){
                    echo '1';exit;
                }else{
                    echo '4';exit;
                }
	        }else{
	            echo '5';exit;
	        }
    	}
    }


    //权限
    public function jurisdiction(){
    	if ($_SESSION['permission']['level'] !== '0') {
	    	if (empty($_SESSION['permission'])) {
	    		
	    		$permission = M('pad_permissions')->where(['pid'=>['neq','0'],'status'=>'1'])->field('id,ctl,act')->select();
	    		$user = M('pad_user')->field('level,permission')->where(['id'=>$_SESSION['dealerId']])->find();
	    		$_SESSION['permission']['level'] = $user['level'];
	    		$ids =  explode(',', $user['permission']);
	    		foreach ($permission as $k => $v) {
	    			if (in_array($v['id'], $ids)) {
	    				$act[$v['ctl']][] = $v['act'];
	    			}else{
	    				$list[$v['ctl']][] = $v['act'];
	    			}
		    	}

		    	$_SESSION['permission']['list'] = $list;
		    	$_SESSION['permission']['act']  = $act;
		    	$_SESSION['permission']['ids']  = $ids;
	    	}else{
	    		$list = $_SESSION['permission']['list'];
	    		$act  = $_SESSION['permission']['act'];
	    		$ids  = $_SESSION['permission']['ids'];
	    	}
	    	

	    	if (in_array(ACTION_NAME, $list[CONTROLLER_NAME])) {
	    		if (!in_array(ACTION_NAME, $act[CONTROLLER_NAME]) && ACTION_NAME!='assigns') {
					$error = true;
				}else{
					/***********************/
					//上传
					if (ACTION_NAME=='infoupload') {
						$type = I('get.type');
						if ($type==1 && !in_array(15, $ids)) {
							$error = true;
						}else if($type==2 && !in_array(26, $ids)){
							$error = true;
						}else if($type==3 && !in_array(36, $ids)){
							$error = true;
						}else if($type==5 && !in_array(40, $ids)){
							$error = true;
						}
					}else if (ACTION_NAME=='editdraw') {
						$tab = I('get.tab');
						if ($tab==1 && !in_array(53, $ids)) {
							$error = true;
						}else if($tab==2 && !in_array(23, $ids)){
							$error = true;
						}else if($tab==3 && !in_array(35, $ids)){
							$error = true;
						}else if($tab==5 && !in_array(41, $ids)){
							$error = true;
						}
					}else if (ACTION_NAME=='deldraw') {
						$tab = I('get.tab');
						if ($tab==1 && !in_array(54, $ids)) {
							$error = true;
						}else if($tab==2 && !in_array(27, $ids)){
							$error = true;
						}else if($tab==3 && !in_array(37, $ids)){
							$error = true;
						}else if($tab==5 && !in_array(42, $ids)){
							$error = true;
						}
					}
					/***********************/

					if (ACTION_NAME=='assigns') {
						$rid = I('get.rid');
						if ($rid==2 && !in_array(55, $ids)) {
							$error = true;
						}else if($rid==3 && !in_array(56, $ids)){
							$error = true;
						}else if($rid==4 && !in_array(57, $ids)){
							$error = true;
						}
					}
				}

				/************************/
				//报价
				if (ACTION_NAME=='offer') {
					$href = I('post.href');
					if (in_array($href, $act[CONTROLLER_NAME])) {
						$error = false;
					}
					//查看清单
					if (in_array(19, $ids) || in_array(31, $ids)) {
						$error = false;
					}
				}

				//查看清单
				if (ACTION_NAME=='listdetail' && in_array(11, $ids)) {
					$error = false;
				}
				/************************/
				
	    	}else{
	    		//修改清单
				if (ACTION_NAME=='goodsAdd' && !in_array(20, $ids)) {
					$error = true;
				}
	    	}

	    	if ($error == true) {
				layout(false);
				$this->error('抱歉！您没有此权限');
			}
		}
    }



	public function isDisplayStaff(){
		
		$row = D('Index')->userCity();
		$return = $row['level'];
		return $return;
	}

	public function selectSetUp(){
		
		$row = D('Index')->selectSetUp();
		$return = explode('|', $row['setup']);
		return $return;
	}

}