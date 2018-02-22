<?php
namespace Home\Controller;
use Think\Controller;

class CountController extends Controller {
	public function index(){
		layout(false);
		$prov = getPCD();
		$this->assign('prov',$prov);
		$this->display();
	}
	
	//查询省市县三级联动
    public function area(){
    	$id = $_GET['v'];
    	$return = M('xgj_area')->where("pid=$id")->field('id,name')->select();
    	echo json_encode($return);
    }
}
