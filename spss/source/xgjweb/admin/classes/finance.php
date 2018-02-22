<?php 
require_once(WWW_DIR."/admin/model/finance_model.php");
require_once(WWW_DIR."/libs/page.php");
/**
 * 财务管理
 * @author Administrator
 */
class finance
{
	/**
	 * 财务管理
	 */
	function finance_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//显示指定页面
		$tab=intval($_GET['tab']);
		$tab_child=empty($_GET['tab_child'])?'':intval($_GET['tab_child']);
		//var_dump($tab_child);exit;
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化数据model类
		$financeOb=new finance_model();
		
		//结算申请搜索
		//判断是否有分页
		if(!isset($_GET['p1'])){
			$page = 1;
		}else{
			$page = $_GET['p1'];
		}
		@$keywords=trim($_POST['keywords']);
		@$finance_status=intval($_POST['finance_status']);
		$condition="1=1";
		if(!empty($keywords)){
			$condition.=" and d.d_company like '%$keywords%' ";
		}
		if(!empty($finance_status)){
			$condition.=" and finance_status = $finance_status ";
		}
		//结算申请列表
		$finance_apply_list=$financeOb->finance_apply_list($page,$condition);
		//结算申请条数
		$finance_apply_count=$financeOb->finance_apply_count($condition);
		//实例化分页类
		$finance_apply_t = new Page(10, $finance_apply_count, $page, 5, "finance.php?tab=1&p1=");
		//结算申请分页样式
		$finance_apply_page=$finance_apply_t->subPageCss2();
		
		
		//退货订单搜索
		//判断是否有分页
		if(!isset($_GET['p2'])){
			$page = 1;
		}else{
			$page = $_GET['p2'];
		}
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
		$refund_order_list=$financeOb->refund_show_list($page, $wheres,$searchs);
		//退货订单统计总数
		$refund_order_count=$financeOb->refund_show_count($wheres,$searchs);
		//实例化分页类
		$t_refund = new Page(10, $refund_order_count, $page, 5, "finance.php?tab=2&tab_child=4&p2=");
		//订单统计分页样式
		$refund_page=$t_refund->subPageCss2();
		//退货订单统计总金额
		$refund_order_price=$financeOb->refund_show_price($wheres,$searchs);
		
		
		//补货订单搜索
		//判断是否有分页
		if(!isset($_GET['p3'])){
			$page = 1;
		}else{
			$page = $_GET['p3'];
		}
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
		$add_order_list=$financeOb->add_show_list($page, $add_wheres,$add_searchs);
		//补货订单统计总数
		$add_order_count=$financeOb->add_show_count($add_wheres,$add_searchs);
		//实例化分页类
		$t_add = new Page(10, $add_order_count, $page, 5, "finance.php?tab=2&tab_child=5&p3=");
		//订单统计分页样式
		$add_page=$t_add->subPageCss2();
		//补货订单统计总金额
		$add_order_price=$financeOb->add_show_price($add_wheres,$add_searchs);
		
		
		//伙伴自购
		//判断是否有分页
		if(!isset($_GET['p4'])){
			$page = 1;
		}else{
			$page = $_GET['p4'];
		}
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
		$self_buy_order_list=$financeOb->self_buy_show_list($page, $conditions);
		//伙伴自购订单统计总数
		$self_buy_order_count=$financeOb->self_buy_show_count($conditions);
		//实例化分页类
		$t_self_buy = new Page(10, $self_buy_order_count, $page, 5, "finance.php?tab=3&p4=");
		//订单统计分页样式
		$self_buy_page=$t_self_buy->subPageCss2();
		//伙伴自购订单统计总金额
		$self_buy_order_price=$financeOb->self_buy_show_price($conditions);
		
		
		//结算申请模板传值
		$tpl->assign("finance_apply_page",$finance_apply_page);
		$tpl->assign('finance_apply_list',$finance_apply_list);
		$tpl->assign('finance_status',$finance_status);
		$tpl->assign('keyword',$keywords);
		//退货订单统计模板传值
		$tpl->assign("refund_page",$refund_page);
		$tpl->assign('refund_order_list',$refund_order_list);
		$tpl->assign('refund_order_count',$refund_order_count);
		$tpl->assign('refund_order_price',$refund_order_price);
		//伙伴自购订单统计模板传值
		$tpl->assign("self_buy_page",$self_buy_page);
		$tpl->assign('self_buy_order_list',$self_buy_order_list);
		$tpl->assign('self_buy_order_count',$self_buy_order_count);
		$tpl->assign('self_buy_order_price',$self_buy_order_price);
		$tpl->assign('first_audit_status',$first_audit_status);
		//补货订单统计模板传值
		$tpl->assign("add_page",$add_page);
		$tpl->assign('add_order_list',$add_order_list);
		$tpl->assign('add_order_count',$add_order_count);
		$tpl->assign('add_order_price',$add_order_price);
		//公共模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('tab',$tab);
		$tpl->assign('tab_child',$tab_child);
		//显示模板
		$tpl->display('admin_dealer_finance_list.tpl.html');
	}
	
	/**
	 * 结算详情
	 */
	function finance_info(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$d_id=intval($_GET['d_id']);
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化数据model类
		$financeOb=new finance_model();
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		//结算公司
		$finance_info_company=$financeOb->finance_company($d_id);
		//结算列表
		$finance_info_list=$financeOb->finance_info_list($page,$d_id);	//var_dump($finance_info);exit;	
		//结算订单条数
		$finance_info_count=$financeOb->finance_info_count($d_id);     //var_dump($finance_info_count);exit;
		//结算总金额
		$finance_info_price=$financeOb->finance_info_price($d_id);
		//实例化分页类
		$finance_info_t = new Page(10, $finance_info_count, $page, 5, "finance.php?info&p=");
		//我的订单分页样式
		$finance_info_page=$finance_info_t->subPageCss2();
		//结算申请模板传值
		$tpl->assign("finance_info_company",$finance_info_company);
		$tpl->assign("finance_info_list",$finance_info_list);
		$tpl->assign("finance_info_price",$finance_info_price);
		$tpl->assign("finance_info_page",$finance_info_page);
		$tpl->assign("d_id",$d_id);
		//显示模板
		$tpl->display('admin_dealer_finance_info.tpl.html');
	}
	
	/**
	 * 结算回复信息
	 */
	function finance_msg(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$d_id=intval($_GET['d_id']);
		$finance_message=html_filter($_GET['finance_message']);//var_dump($finance_message);exit;
		$message_status=intval($_GET['message_status']);
		//实例化数据model类
		$financeOb=new finance_model();
		//修改一条记录
		$rs=$financeOb->finance_message_d_id('xgj_furnish_dealer', array('finance_message'=>$finance_message,'message_status'=>$message_status), "d_id = {$d_id}");
		//判断并返回值
		if($rs){
			echo 'success';
		}else{
			echo 'lose';
		}
	}
	/**
	 * 财务结算支付
	 */
	function finance_pay(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$d_id=intval($_GET['d_id']);
		$finance_info_price=floatval($_GET['finance_info_price']);
		//实例化数据model类
		$financeOb=new finance_model();
		$rs=$financeOb->finance_pay($d_id, $finance_info_price);
		//判断并显示提示信息
		if($rs){
			//提示信息
			$message='支付成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}else{
			//提示信息
			$message='支付失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}
	}
	/**
	 * 结算历史
	 */
	function finance_log(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$d_id=intval($_GET['d_id']);
		//调用smarty
		$tpl = get_admin_smarty();
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		//实例化数据model类
		$financeOb=new finance_model();
		//结算历史列表
		$finance_log_list=$financeOb->finance_log_list($page,$d_id);	//var_dump($finance_info);exit;
		//结算历史条数
		$finance_log_count=$financeOb->finance_log_count($d_id);
		//实例化分页类
		$finance_log_t = new Page(10, $finance_log_count, $page, 5, "finance.php?log&p=");
		//结算历史分页样式
		$finance_log_page=$finance_log_t->subPageCss2();
		//结算历史模板传值
		$tpl->assign("finance_log_list",$finance_log_list);
		$tpl->assign("finance_log_page",$finance_log_page);
		$tpl->assign("d_id",$d_id);
		//显示模板
		$tpl->display('admin_dealer_finance_log.tpl.html');
	}
	/**
	 * 退换货审核
	 */
	function finance_back(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		$refund_id=intval($_GET['refund_id']);
		//实例化数据model类
		$db=new db();
		//接收传值
		if(isset($_GET['review'])){
			$review=intval($_GET['review']);
			$rs=$db->update("xgj_furnish_order_refund", array('review_audit_status'=>$review), "refund_id=$refund_id");
		}elseif (isset($_GET['first'])){
			$first=intval($_GET['first']);
			$rs=$db->update("xgj_furnish_order_refund", array('first_audit_status'=>$first), "refund_id=$refund_id");
		}
		//判断并显示提示信息
		if($rs){
			//提示信息
			$message='退回成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,"finance.php?tab=2&tab_child=4");
			//跳转地址
			header("refresh:2;url='finance.php?tab=2&tab_child=4'" );
		}else{
			//提示信息
			$message='退回失败或已退回，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}
	}
	/**
	 * 退货详情信息
	 */
	function finance_refund_info(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$refund_id=intval($_GET['refund_id']);
		$title=isset($_GET['refund'])?'退货清单':'补货清单';
		//调用smarty
		$tpl = get_admin_smarty();
		//判断是否有分页
		if(!isset($_GET['pg'])){
			$page = 1;
		}else{
			$page = $_GET['pg'];
		}
		$start=($page-1)*10;
		$pagesize=10;
		//实例化数据model类
		$financeOb=new finance_model();
		//退换货详细信息
		$status=$financeOb->get_one_refund_id($refund_id);
		$refund_list=$financeOb->refund_info($refund_id);
		$refund_status=$status['review_audit_status'];
		//退换货详细信息模板传值
		$tpl->assign("refund_list",$refund_list);
		$tpl->assign("refund_id",$refund_id);
		$tpl->assign("refund_status",$refund_status);
		$tpl->assign('title',$title);
		//显示模板
		$tpl->display('admin_dealer_finance_refund_info.tpl.html');
	}
}