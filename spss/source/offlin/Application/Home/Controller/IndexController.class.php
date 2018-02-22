<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends BaseController {
    public function index(){
    	//查询出所有系统分类
    	
		$this->assign('now','2');
    	layout(false);

		$quoteCat = D('Index')->quoteCat();
		$user = D('Index')->user();
		// var_dump($user['system']);exit;
		// dump($quoteCat);exit;
		$this->assign('system',explode('|', $user['system']));
		$this->assign('quoteCat',$quoteCat);
        $this->display();
    }


	public function delSession(){
		unset($_SESSION['pad_post']);
		echo 1;
	}
	public function setup(){
		$this->assign('now','5');
		$row = D('Index')->selectSetUp();
		$return = explode('|', $row['setup']);
		//var_dump($return);die();
	/*	if ($_GET['t1']=='true') $t1 = '1';
		else $t1 = '0';
		if ($_GET['t2']=='true') $t2 = '1';
		else $t2 = '0';
		if ($_GET['t3']=='true') $t3 = '1';
		else $t3 = '0';
		if ($_GET['t4']=='true') $t4 = '1';
		else $t4 = '0';
		if ($_GET['t5']=='true') $t5 = '1';
		else $t5 = '0';
		if ($_GET['t6']=='true') $t6 = '1';
		else $t6 = '0';
		if ($_GET['t7']=='true') $t7 = '1';
		else $t7 = '0';
		$t['setup'] = $t1.'|'.$t2.'|'.$t3.'|'.$t4.'|'.$t5.'|'.$t6.'|'.$t7;
		
		$row = D('Index')->setup($t);*/
		$this->assign('return',$return);
		$this->display();
	}
	public function set(){
		$t['setup'] = trim(I('get.data'),'|');
		$row = D('Index')->setup($t);
		if ($row!==false) {
			echo '修改成功！';
		} else {
			echo '修改失败！';
		}
		
	}
}