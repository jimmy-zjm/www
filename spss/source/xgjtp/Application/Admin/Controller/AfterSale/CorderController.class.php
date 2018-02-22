<?php

namespace Admin\Controller\AfterSale;
use \Admin\Controller\Index\AdminController;
/*
 后台管理员控制器
 */
class CorderController extends AdminController{

	public function index(){
		//分页
		$count = M('xgj_s_order o')->join('xgj_users u on u.user_id=o.user_id')->where(array("o.class_id"=>9))->count();
		$page = getPage($count,15);
		$res=M('xgj_s_order o')->field("o.*,u.user_name")->join('xgj_users u on u.user_id=o.user_id')->where(array("o.class_id"=>9))->limit($page['limit'])->select();
		foreach ($res as $k => $v) {
			$arr=M('xgj_s_order_goods')->where(array("order_id"=>$v['id']))->select();
			$str='';
			foreach($arr as $kk=>$vv){
				$str.=$vv['goods_title'].'、';
			}
			$res[$k]['goods_name']=rtrim($str,'、');
		}

		$this->assign('res',$res);
		$this->assign('page',$page['page']);
		$this->display();
	}
	
	//查找最近的经销商信息
	public function dealer(){
		$provinceNo = $_GET['province'];
		$id = $_GET['id'];
		$arr['d_province'] = array("like","%{$provinceNo}%");
		$dealer = M('Xgj_furnish_dealer');
		$deal = $dealer->where($arr)->select();
		
		
		$this->assign('deal',$deal);
		$this->assign('id',$id);
		$this->display();
	}
	
	//将问题反馈分给就近的经销商
	public function setAllot(){
		//print_r($_GET);
		$update = M('xgj_s_order');
		$data = array();
		$data['d_id'] = $_GET['d_id'];
		$where=array();
		$where['id'] = $_GET['id'];
		
		$res = $update->where($where)->save($data);

		if ($res!==false){
			$this->success('分配成功',U('index'),2);
		}else {
			$this->error('分配失败 或已分配',U('index'),2);
		}
	}


}
