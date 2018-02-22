<?php
namespace Admin\Controller\Dealer;
use \Admin\Controller\Index\AdminController;

/**
 * 后台商品控制器
 */
class FinanceController extends AdminController{

   /**
	 * 财务管理
	 */
	public function index(){
		//显示指定页面
		$tab=intval($_GET['tab']);
		$tab_child=empty($_GET['tab_child'])?'':intval($_GET['tab_child']);
		//实例化数据model类
		$financeOb=new \Admin\Model\Dealer\FinanceModel;
		//结算申请搜索
		
		//结算申请列表
		$finance_apply_list=$financeOb->finance_apply_list();		
		
		//退货订单搜索
		@$searchs=trim($_POST['keyword']);
		if (!empty($_POST['refund_start_time']) && !empty($_POST['refund_end_time'])){
			@$refund_start_time=strtotime($_POST['refund_start_time']);
			@$refund_end_time=strtotime($_POST['refund_end_time']);
		}
		$wheres="";
		if(isset($refund_start_time) && isset($refund_end_time)){
			$wheres.=" and r.refund_time between $refund_start_time and $refund_end_time ";
		}
		if(!empty($searchs)){
			$searchs=" where d_company like '%$searchs%' ";
		}
		//退货订单统计列表
		$refund_order_list=$financeOb->refund_show_list($wheres,$searchs);
		
		
		//补货订单搜索
		@$add_searchs=trim($_POST['add_keyword']);
		if (!empty($_POST['add_start_time']) && !empty($_POST['add_end_time'])){
			@$add_start_time=strtotime($_POST['add_start_time']);
			@$add_end_time=strtotime($_POST['add_end_time']);
		}
		$add_wheres="";
		if(isset($add_start_time) && isset($add_end_time)){
			$add_wheres.=" and r.refund_time between $add_start_time and $add_end_time ";
		}
		if(!empty($add_searchs)){
			$add_searchs=" where d_company like '%$add_searchs%' ";
		}
		//补货订单统计列表
		$add_order_list=$financeOb->add_show_list($add_wheres,$add_searchs);		
		
		//伙伴自购
		@$first_audit_status=intval($_POST['first_audit_status']);
		if (!empty($_POST['self_buy_start_time']) && !empty($_POST['self_buy_end_time'])){
			@$self_buy_start_time=strtotime($_POST['self_buy_start_time']);
			@$self_buy_end_time=strtotime($_POST['self_buy_end_time']);
		}
		$conditions="";
		if(isset($self_buy_start_time) && isset($self_buy_end_time)){
			$conditions.=" and r.refund_time between $self_buy_start_time and $self_buy_end_time ";
		}
		if(!empty($first_audit_status)){
			$conditions.=" and r.first_audit_status =$first_audit_status ";
		}
		//伙伴自购订单统计列表
		$self_buy_order_list=$financeOb->self_buy_show_list($conditions);

		//	var_dump($finance_apply_list,$refund_order_list,$self_buy_order_list,$add_order_list);exit;
		//结算申请模板传值
		$this->assign("finance_apply_page",$finance_apply_list['finance_apply_page']);
		$this->assign('finance_apply_list',$finance_apply_list['finance_apply_list']);
		$this->assign('finance_status',$finance_status);
		$this->assign('keyword',$keywords);
		//退货订单统计模板传值
		$this->assign("refund_page",$refund_order_list['refund_show_page']);
		$this->assign('refund_order_list',$refund_order_list['refund_show_list']);
		$this->assign('refund_order_count',$refund_order_list['refund_show_count'][0]['total']);
		$this->assign('refund_order_price',$refund_order_list['refund_show_price'][0]['price']);
		//伙伴自购订单统计模板传值
		$this->assign("self_buy_page",$self_buy_order_list['self_buy_show_page']);
		$this->assign('self_buy_order_list',$self_buy_order_list['self_buy_show_list']);
		$this->assign('self_buy_order_count',$self_buy_order_list['self_buy_show_count'][0]['total']);
		$this->assign('self_buy_order_price',$self_buy_order_list['self_buy_show_price'][0]['price']);
		$this->assign('first_audit_status',$first_audit_status);
		//补货订单统计模板传值
		$this->assign("add_page",$add_order_list['add_show_page']);
		$this->assign('add_order_list',$add_order_list['add_show_list']);
		$this->assign('add_order_count',$add_order_list['add_show_count'][0]['total']);
		$this->assign('add_order_price',$add_order_list['add_show_price'][0]['price']);
		//公共模板传值
		$this->assign('tab',$tab);
		$this->assign('tab_child',$tab_child);
		//显示模板
		$this->display();
	}
	
	/**
	 * 结算详情
	 */
	public function info(){		
		//接收传值
		$d_id=intval($_GET['d_id']);
		$finance_id=intval($_GET['finance_id']);
		//实例化数据model类
		$financeOb=new \Admin\Model\Dealer\FinanceModel;
		//结算公司
		$finance_info_company=$financeOb->finance_company($d_id);
		//结算列表
		$finance_info_list=$financeOb->finance_info_list($d_id,$finance_id);	//var_dump($finance_info_company);exit;	
		//var_dump($finance_info_list);exit;
		//结算申请模板传值
		$this->assign("finance_info_company",$finance_info_company);
		$this->assign("finance_info_list",$finance_info_list['finance_info_list']);
		$this->assign("finance_info_price",$finance_info_list['finance_info_price']);
		$this->assign("finance_info_page",$finance_info_list['finance_info_page']);
		$this->assign("pay",$finance_info_list['pay']);
		$this->assign("d_id",$d_id);
		$this->assign("finance_id",$finance_id);
		//显示模板
		$this->display();
	}
	
	/**
	 * 结算回复信息
	 */
	public function msg(){
		//接收传值
		$finance_id=intval($_GET['finance_id']);
		$finance_message=html_filter($_GET['finance_message']);//var_dump($finance_message);exit;
		$message_status=intval($_GET['message_status']);
		//实例化数据model类
		$financeOb=new \Admin\Model\Dealer\FinanceModel;
		//查询当前的材料申请状态
        $status=M('xgj_furnish_finance')->where("finance_id={$finance_id}")->getField('state');
        //var_dump($finance_id);
        if ($status==1 || $status==2){
            echo 'already';
        }else{
        	$finance_status=$message_status==1?1:3;
			//修改一条记录
			$rs=$financeOb->updateOne('xgj_furnish_finance', array('message'=>$finance_message,'state'=>$message_status,"finance_status"=>$finance_status),"finance_id = {$finance_id}");
			//判断并返回值
			if($rs){
				echo 'success';
			}else{
				echo 'lose';
			}
		}
	}
	/**
	 * 财务结算支付
	 */
	public function pay(){
		//接收传值
		$d_id=intval($_GET['d_id']);
		$finance_id=intval($_GET['finance_id']);
		$money=M('xgj_furnish_finance')->where("finance_id=$finance_id")->getField('money');
		//$money=floatval($_GET['money']);
		//实例化数据model类
		$financeOb=new \Admin\Model\Dealer\FinanceModel;
		$rs=$financeOb->finance_pay($finance_id,$d_id, $money);
		//判断并显示提示信息
		if($rs){
			//跳转页面
			$this->success('支付成功，正在跳转...');
			die;
		}else{
			$this->error('支付失败，正在跳转...');
		}
	}
	/**
	 * 结算历史
	 */
	public function log(){
		//接收传值
		$d_id=intval($_GET['d_id']);
		//实例化数据model类
		$financeOb=new \Admin\Model\Dealer\FinanceModel;
		//结算历史列表
		$finance_log_list=$financeOb->finance_log_list($d_id);	//var_dump($finance_log_list);exit;
		//结算历史模板传值
		$this->assign("finance_log_list",$finance_log_list['finance_log_list']);
		$this->assign("finance_log_page",$finance_log_list['finance_log_page']);
		$this->assign("d_id",$d_id);
		//显示模板
		$this->display();
	}
	/**
	 * 财务结算退换货审核
	 */
	public function back(){
		$refund_id=intval($_GET['refund_id']);
		//实例化数据model类
		$financeOb=new \Admin\Model\Dealer\FinanceModel;
		//接收传值
		if(isset($_GET['review'])){
			$review=intval($_GET['review']);
			$rs=$financeOb->updateOne("xgj_furnish_order_refund", array('review_audit_status'=>$review), "refund_id=$refund_id");
		}elseif (isset($_GET['first'])){
			$first=intval($_GET['first']);
			$rs=$financeOb->updateOne("xgj_furnish_order_refund", array('first_audit_status'=>$first), "refund_id=$refund_id");
		}
		//判断并显示提示信息
		if($rs){
			//跳转页面
			$this->success('退回成功，正在跳转...');
			die;
		}else{
			$this->error('退回失败或已退回，正在跳转...');
		}
	}
	/**
	 * 退货详情信息
	 */
	public function refund_info(){
		//接收传值
		$refund_id=intval($_GET['refund_id']);
		$title=$_GET['refund']==1?'退货清单':'补货清单';
		//实例化数据model类
		$financeOb=new \Admin\Model\Dealer\FinanceModel;
		//退换货详细信息
		$status=$financeOb->get_one_refund_id($refund_id);
		$refund_list=$financeOb->refund_info($refund_id);
		//var_dump($refund_list);exit;
		$refund_status=$status['review_audit_status'];
		//退换货详细信息模板传值
		$this->assign("refund_list",$refund_list);
		$this->assign("refund_id",$refund_id);
		$this->assign("refund_status",$refund_status);
		$this->assign('title',$title);
		//显示模板
		$this->display();
	}

}