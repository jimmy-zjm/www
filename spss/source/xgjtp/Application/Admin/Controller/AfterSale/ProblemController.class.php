<?php

namespace Admin\Controller\AfterSale;
use \Admin\Controller\Index\AdminController;
/*
 后台管理员控制器
 */
class ProblemController extends AdminController{

	public function index(){
		
		$problem = M('xgj_user_problem');
		//分页
		$count = $problem->count();
		$page = getPage($count,15);
		$res = $problem->query("select * from xgj_user_problem p join xgj_furnish_quote q on p.quote_id=q.quote_id join xgj_furnish_order_info o on o.order_id=p.order_id limit {$page['limit']} ");

		$this->assign('res',$res);
		$this->assign('page',$page['page']);
		$this->display();
	}
	
	//查找最近的经销商信息
	public function dealer(){
		$provinceNo = $_GET['province'];
		
		$where['id'] = $provinceNo;
		$province = M('xgj_area')->where($where)->find();
		$provinceName = $province['name'];
		
		$id = $_GET['id'];
		$arr['d_province'] = array("like","%{$provinceName}%");
		$dealer = M('Xgj_furnish_dealer');
		$deal = $dealer->where($arr)->select();
		
		
		$this->assign('deal',$deal);
		$this->assign('id',$id);
		$this->display();
	}
	
	//将问题反馈分给就近的经销商
	public function setAllot(){
		//print_r($_GET);
		$update = M('xgj_user_problem');
		$data = array();
		$data['d_id'] = $_GET['d_id'];
		$data['allot'] = 1;
		$where=array();
		$where['id'] = $_GET['id'];
		
		$res = $update->where($where)->save($data);
		if ($res){
			//echo '问题分配成功';
			//$this->redirect();
			$this->success('分配成功',U('index'),2);
		}else {
			$this->error('分配失败 或已分配',U('index'),2);
		}
	}


}
