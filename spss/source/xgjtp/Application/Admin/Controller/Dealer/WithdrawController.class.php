<?php
namespace Admin\Controller\Dealer;
use \Admin\Controller\Index\AdminController;

/**
 * 后台商品控制器
 */
class WithdrawController extends AdminController{
	private $m;
	public function __construct(){
		parent::__construct();
		$this->m = new \Admin\Model\Dealer\WithdrawModel;
	}
   /**
	 * 提现管理
	 */
	public function index(){
		$data=$this->m->getAll();
		//var_dump($data);exit;
		$this->assign('page', $data['page']);
		$this->assign('list',$data['list']);
		//显示模板
		$this->display();
	}

	/**
	 * 变更状态
	 */
	public function edit(){
		$id=I('get.id/d');
		$status=I('get.status/d');
		if(empty($id)) die;
		if($status==2){
			$data=$this->m->getOne($id);
			$d_price=M('xgj_furnish_dealer')->where("d_id={$data['d_id']}")->getField('d_price');
			$price=$d_price-$data['money_num'];
			//var_dump($data,$price,$d_price);
			if($price < 0){
				$this->error("提现失败，提现金额有误，请核对！");
			}
			$re=M()->execute('UPDATE xgj_furnish_dealer SET d_price ='.$price.' WHERE d_id='. $data['d_id']);
			$rs=M()->execute('UPDATE xgj_furnish_get_money SET status ='.$status.' WHERE id='. $id);
			if($rs&&$re){
				$this->success('操作成功');
	            die;
			}else{
				$this->error('操作失败');
	            die;
			}
		}else{
			if(M()->execute('UPDATE xgj_furnish_get_money SET status ='.$status.' WHERE id='. $id)){
	        	$this->success('操作成功');
	            die;
	        }else{
	        	$this->error('操作失败');
	            die;
	        }
		}
	}
}