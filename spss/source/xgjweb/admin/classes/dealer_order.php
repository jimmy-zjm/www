<?php 
//header("Content-type:text/html;charset=utf-8");
require_once(WWW_DIR."/admin/model/dealer_order_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
/**
 * @author Administrator
 * 服务商订单操作
 */
class dealer_order
{
	/**
	 * 服务商订单列表
	 */
	function dealer_order_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//显示指定页面
		$tab=intval($_GET['tab']);
		$tab_child=empty($_GET['tab_child'])?'':intval($_GET['tab_child']);
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化服务商订单model类
		$dealer_orderOb=new dealer_order_model();
		//判断是否有分页
		if(!isset($_GET['p1'])){
			$page = 1;
		}else{
			$page = $_GET['p1'];
		}
		//echo $tab;//exit($tab);
		//新订单显示列表内容
		$new_dealer_order_list=$dealer_orderOb->new_show_list($page);
		//新订单分页的总条数
		$new_dealer_order_count=$dealer_orderOb->new_show_count();
		//实例化分页类
		$new_t = new Page(1, $new_dealer_order_count, $page, 5, "dealer_order.php?tab=1&p1=");
		//新订单分页样式
		$new_page=$new_t->subPageCss2();
		
		
		//订单统计搜索（根据省份查询暂时存在问题）
		//判断是否有分页
		if(!isset($_GET['p2'])){
			$page = 1;
		}else{
			$page = $_GET['p2'];
		}
		@$search=trim($_POST['keyword']);
		if (!empty($_POST['start_time']) && !empty($_POST['end_time'])){
			@$start_time=strtotime($_POST['start_time']);
			@$end_time=strtotime($_POST['end_time']);
		}
		//var_dump($start_time);exit($end_time);
		$province=empty($_POST['d_province'])?'':trim($_POST['d_province']);
		$where="1=1"; 
		if(isset($start_time) && isset($end_time)){//var_dump($order_schedule_status);exit;
			$where.=" and i.add_order_time between $start_time and $end_time ";
		}
		if(!empty($province)){
			$province=" where d_province = '$province' ";
		}
		if(!empty($search)){
			$search=" where i.order_code like '%$search%' or i.mobile_phone like '%$search%' or i.order_merchandiser like '%$search%' ";
		}
		//订单统计
		$dealer_order_statistics_list=$dealer_orderOb->show_statistics_list($page,$where,$search,$province);
		//订单统计分页的总条数
		$dealer_order_statistics_count=$dealer_orderOb->show_statistics_count($where,$search,$province);
		//实例化分页类
		$t_statistics = new Page(1, $dealer_order_statistics_count, $page, 5, "dealer_order.php?tab=2&p2=");
		//订单统计分页样式
		$page_statistics=$t_statistics->subPageCss2();
		//订单统计总金额
		$dealer_order_statistics_price=$dealer_orderOb->show_statistics_price();
		//查找服务商信息
		$dealer_info=$dealer_orderOb->get_dealer_info();
		
		//退货订单搜索
		//判断是否有分页
		if(!isset($_GET['p5'])){
			$page = 1;
		}else{
			$page = $_GET['p5'];
		}
		@$refund_searchs=trim($_POST['refund_keyword']);
		if (!empty($_POST['refund_start_time']) && !empty($_POST['refund_end_time'])){
			@$refund_start_time=strtotime($_POST['refund_start_time']);
			@$refund_end_time=strtotime($_POST['refund_end_time']);
		}
		$refund_wheres="";
		if(isset($refund_start_time) && isset($refund_end_time)){
			$refund_wheres.=" and r.refund_time between $refund_start_time and $refund_end_time ";
		}
		if(!empty($refund_searchs)){
			$refund_searchs=" where d_company like '%$refund_searchs%' ";
		}
		//退货订单统计列表
		$refund_order_list=$dealer_orderOb->refund_show_list($page, $refund_wheres,$refund_searchs);
		//退货订单统计总数
		$refund_order_count=$dealer_orderOb->refund_show_count($refund_wheres,$refund_searchs);
		//实例化分页类
		$t_refund = new Page(1, $refund_order_count, $page, 5, "dealer_order.php?tab=3&tab_child=5&p5=");
		//订单统计分页样式
		$refund_page=$t_refund->subPageCss2();
		//退货订单统计总金额
		$refund_order_price=$dealer_orderOb->refund_show_price($refund_wheres,$refund_searchs);
		
		
		//补货订单搜索
		//判断是否有分页
		if(!isset($_GET['p6'])){
			$page = 1;
		}else{
			$page = $_GET['p6'];
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
		$add_order_list=$dealer_orderOb->add_show_list($page,$add_wheres,$add_searchs);
		//补货订单统计总数
		$add_order_count=$dealer_orderOb->add_show_count($add_wheres,$add_searchs);
		//实例化分页类
		$t_add = new Page(1, $add_order_count, $page, 5, "dealer_order.php?tab=3&tab_child=6&p6=");
		//订单统计分页样式
		$add_page=$t_add->subPageCss2();
		//补货订单统计总金额
		$add_order_price=$dealer_orderOb->add_show_price($add_wheres,$add_searchs);
		
		
		//伙伴自购
		//判断是否有分页
		if(!isset($_GET['p7'])){
			$page = 1;
		}else{
			$page = $_GET['p7'];
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
		$self_buy_order_list=$dealer_orderOb->self_buy_show_list($page, $conditions);
		//伙伴自购订单统计总数
		$self_buy_order_count=$dealer_orderOb->self_buy_show_count($conditions);
		//实例化分页类
		$t_self_buy = new Page(1, $self_buy_order_count, $page, 5, "finance.php?tab=3&tab_child=7&p7=");
		//订单统计分页样式
		$self_buy_page=$t_self_buy->subPageCss2();
		//伙伴自购订单统计总金额
		$self_buy_order_price=$dealer_orderOb->self_buy_show_price($conditions);
		
		
		//我的订单搜索
		//判断是否有分页
		if(!isset($_GET['p4'])){
			$page = 1;
		}else{
			$page = $_GET['p4'];
		}
		@$my_keyword=trim($_POST['my_keyword']);
		@$my_schedule_status=intval($_POST['my_schedule_status']);
		@$my_order_code=trim($_POST['my_order_code']);
		$my_condition="";
		if(!empty($my_schedule_status)){//var_dump($order_schedule_status);exit;
			$my_condition.=" and i.schedule_status = $my_schedule_status ";
		}
		if(!empty($my_order_code)){
			$my_condition.=" and i.order_code = $my_order_code ";
		}
		if(!empty($my_keyword)){
			$my_condition.=" and d.d_company like '%$my_keyword%' ";
		}
		//我的订单显示列表内容
		$my_dealer_order_list=$dealer_orderOb->my_show_list($page,$my_condition);
		//我的订单分页的总条数
		$my_dealer_order_count=$dealer_orderOb->my_show_count($my_condition);
		//实例化分页类
		$my_t = new Page(1, $my_dealer_order_count, $page, 5, "dealer_order.php?tab=4&p4=");
		//我的订单分页样式
		$my_page=$my_t->subPageCss2();
		
		//新订单模板传值
		$tpl->assign("page_statistics",$page_statistics);
		$tpl->assign('dealer_order_statistics_list',$dealer_order_statistics_list);
		$tpl->assign('dealer_order_statistics_count',$dealer_order_statistics_count);
		$tpl->assign('dealer_order_statistics_price',$dealer_order_statistics_price);
		//订单统计模板传值
		$tpl->assign("new_page",$new_page);
		$tpl->assign('dealer_info',$dealer_info);
		$tpl->assign('new_dealer_order_list',$new_dealer_order_list);
		//退货订单统计模板传值
		$tpl->assign("refund_page",$refund_page);
		$tpl->assign('refund_order_list',$refund_order_list);
		$tpl->assign('refund_order_count',$refund_order_count);
		$tpl->assign('refund_order_price',$refund_order_price);
		//补货订单统计模板传值
		$tpl->assign("add_page",$add_page);
		$tpl->assign('add_order_list',$add_order_list);
		$tpl->assign('add_order_count',$add_order_count);
		$tpl->assign('add_order_price',$add_order_price);
		//伙伴自购订单统计模板传值
		$tpl->assign("self_buy_page",$self_buy_page);
		$tpl->assign('self_buy_order_list',$self_buy_order_list);
		$tpl->assign('self_buy_order_count',$self_buy_order_count);
		$tpl->assign('self_buy_order_price',$self_buy_order_price);
		$tpl->assign('first_audit_status',$first_audit_status);
		//我的订单模板传值
		$tpl->assign('order_code',$my_order_code);
		$tpl->assign('schedule_status',$my_schedule_status);
		$tpl->assign('keyword',$my_keyword);
		$tpl->assign("my_page",$my_page);
		$tpl->assign('my_dealer_order_list',$my_dealer_order_list);
		//公共模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('tab',$tab);
		$tpl->assign('tab_child',$tab_child);
		//显示模板
		$tpl->display('admin_dealer_order_list.tpl.html');
	}
	/**
	 * 分配订单给服务商
	 */
	function dealer_order_allot(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化服务商订单model类
		$dealer_orderOb=new dealer_order_model();
		//接收传值
		$order_id=intval($_GET['order_id']);
		if (!IS_POST){
			//查找服务商订单信息
			$dealer_order_info=$dealer_orderOb->dealer_order_id($order_id);
			//根据省份及市区查询服务商信息
			$dealer_list=$dealer_orderOb->get_dealer_list($dealer_order_info['province'], $dealer_order_info['city']);
			//查找服务商信息
			$dealer_info=$dealer_orderOb->get_dealer_info();
			//模板传值
			$tpl->assign('permission',$permission);
			$tpl->assign('dealer_info',$dealer_info);
			$tpl->assign('dealer_list',$dealer_list);
			$tpl->assign('dealer_order_info',$dealer_order_info);
			$tpl->assign('order_id',$order_id);
			//显示模板
			$tpl->display('admin_dealer_order_allot.tpl.html');
		}else{
			if (!empty($_POST)){
				if (isset($_POST['d_id'])){
					//获取值
					$d_id=intval($_POST['d_id']);
					$rs=$dealer_orderOb->update_by_id('xgj_furnish_order_info', array('d_id'=>$d_id,'allot_status'=>1), "order_id={$order_id}");
					if ($rs){
						//提示信息
						$message='分配成功，正在跳转...';
						//输出提示页面
						echo jump(1,$message,"dealer_order.php?tab=1");
						//跳转地址
						header("refresh:2;url='dealer_order.php?tab=1'" );
					}
				}else if(isset($_POST['d_province']) || isset($_POST['d_city'])){
					//获取值
					$d_province=empty($_POST['d_province'])?'':trim($_POST['d_province']);
					$d_city=empty($_POST['d_city'])?'':trim($_POST['d_city']);
					//查找服务商订单信息
					$dealer_order_info=$dealer_orderOb->dealer_order_id($order_id);
					//查找服务商信息
					$dealer_info=$dealer_orderOb->get_dealer_info();
					//根据省份及市区查询服务商信息
					$dealer_list=$dealer_orderOb->get_dealer_list($d_province, $d_city);
					//模板传值
					$tpl->assign('permission',$permission);
					$tpl->assign('dealer_info',$dealer_info);
					$tpl->assign('dealer_list',$dealer_list);
					$tpl->assign('d_province',$d_province);
					$tpl->assign('d_city',$d_city);
					$tpl->assign('dealer_order_info',$dealer_order_info);
					$tpl->assign('order_id',$order_id);
					//显示模板
					$tpl->display('admin_dealer_order_allot.tpl.html');
				}	
			}else{
				//提示信息
				$message='分配失败，请选择要分配的服务商，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		}
	}
	
	/**
	 * 健康舒适家居订单详情
	 */
	function dealer_order_info(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$order_id=intval($_GET['order_id']);
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化服务商订单model类
		$dealer_orderOb=new dealer_order_model();
		//订单详情的一条记录
		$dealer_order_info=$dealer_orderOb->get_dealer_order_info($order_id);
		//调整订单的一条信息
		$dealer_order_adjust_info=$dealer_orderOb->get_order_adjust_info($order_id);
		//订单详情的已付金额
		$dealer_order_price=$dealer_orderOb->get_dealer_order_price($order_id);
		if($dealer_order_info[0]['pay_status']==1 || $dealer_order_info[0]['pay_status']==1){
			$price=$dealer_order_price;
		}else{
			$price=$dealer_order_price*0.5;
		}
		$detail_id=empty($_GET['detail_id'])?$dealer_order_info[0]['detail_id']:intval($_GET['detail_id']);
		if(!empty($detail_id)){
			//获取施工计划信息
			$construct_list=$dealer_orderOb->get_dealer_order_construct($detail_id);
			//获取上传文件信息
			$file_list=$dealer_orderOb->get_file($order_id);
		}else{
			$construct_list=[];
		}
		//模板传值
		$tpl->assign("dealer_order_info",$dealer_order_info);
		$tpl->assign("dealer_order_adjust_info",$dealer_order_adjust_info);
		$tpl->assign("construct_list",$construct_list);
		$tpl->assign("file_list",$file_list);
		$tpl->assign("detail_id",$detail_id);
		$tpl->assign("order_id",$order_id);
		$tpl->assign("price",$price);
		//显示模板
		$tpl->display('admin_dealer_order_info.tpl.html');
	}
	
	/**
	 * 健康舒适家居材料清单页面
	 */
	function dealer_order_stuff_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$detail_id=intval($_GET['detail_id']);
		$quote_name=trim($_GET['quote_name']);
		$title=isset($_GET['master'])?'主材清单':'辅材清单';
		$stuff_id=isset($_GET['master'])?1:2;
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化服务商订单model类
		$dealer_orderOb=new dealer_order_model();
		//订单详情的一条记录
		$dealer_order_list=$dealer_orderOb->get_dealer_order_info($detail_id);
		//材料清单列表
		$dealer_order_stuff_list=$dealer_orderOb->get_dealer_order_stuff_list($detail_id,$stuff_id);
		//退货清单
		$refund_list=$dealer_orderOb->get_dealer_order_refund_list($detail_id,$stuff_id,1);
		//换货清单
		$add_list=$dealer_orderOb->get_dealer_order_refund_list($detail_id,$stuff_id,2);
		//退货清单
		$selfbuy_list=$dealer_orderOb->get_dealer_order_refund_list($detail_id,$stuff_id,3);
		$refund_price='';
		foreach ($refund_list as $v){
			if (!empty($v['list'])){
				$refund_price+=$v['num']*$v['list']['shop_price'];
			}
		}
		$add_price='';
		foreach ($add_list as $v){
			if (!empty($v['list'])){
				$add_price+=$v['num']*$v['list']['shop_price'];
			}
		}
		$selfbuy_price='';
		foreach ($selfbuy_list as $v){
			if (!empty($v['list'])){
				$selfbuy_price+=$v['num']*$v['list']['shop_price'];
			}
		}

		foreach ($add_list as $key=>$val){
			foreach ($dealer_order_stuff_list as $k=>$v){
				if(!empty($v['list'])){
					if ($add_list[$key]['goods_sn'] == $dealer_order_stuff_list[$k]['goods_sn']){
						$dealer_order_stuff_list[$k]['adjust_num']=$val['num']+$v['num'];
					}
				}
			}
		}
		foreach ($selfbuy_list as $key=>$val){
			foreach ($dealer_order_stuff_list as $k=>$v){
				if(!empty($v['list'])){
					if ($selfbuy_list[$key]['goods_sn'] == $dealer_order_stuff_list[$k]['goods_sn']){
						$dealer_order_stuff_list[$k]['outbound']="自购";
					}
				}
			}
		}
		//var_dump($dealer_order_stuff_list);
		//模板传值
		$tpl->assign("dealer_order_stuff_list",$dealer_order_stuff_list);
		$tpl->assign("dealer_order_list",$dealer_order_list);
		$tpl->assign("refund_list",$refund_list);
		$tpl->assign("add_list",$add_list);
		$tpl->assign("selfbuy_list",$selfbuy_list);
		$tpl->assign("refund_price",$refund_price);
		$tpl->assign("add_price",$add_price);
		$tpl->assign("selfbuy_price",$selfbuy_price);
		$tpl->assign("title",$title);
		$tpl->assign("quote_name",$quote_name);
		//显示模板
		$tpl->display('admin_dealer_order_stuff_list.tpl.html');
	}
	
	/**
	 * 退换货伙伴自购材料审核
	 */
	function dealer_order_audit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$refund_id=intval($_GET['refund_id']);
		$first_audit_status=intval($_GET['first_audit_status']);
		//实例化服务商订单model类
		$dealer_orderOb=new dealer_order_model();
		//查询当前的材料申请状态
		$status=$dealer_orderOb->get_first_audit_status($refund_id);
		if ($status==2 || $status==3){
			echo 'already';
		}else{
			//修改材料申请状态
			$rs=$dealer_orderOb->update_by_id("xgj_furnish_order_refund", array("first_audit_status"=>$first_audit_status), "refund_id=$refund_id");
			//判断并返回值
			if($rs){
				echo 'success';
			}else{
				echo 'lose';
			}
		}
	}
	
	
	/**
	 * 质量审核 （跟单人审核，会改变订单的进度，以及给经销商结算一笔钱）
	 */
	function dealer_order_construct_check(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$order_id=intval($_GET['order_id']);
		$detail_id=intval($_GET['detail_id']);
		$task_work=intval($_GET['task_work']);
		$construct_id=intval($_GET['construct_id']);
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化服务商订单model类
		$dealer_orderOb=new dealer_order_model();
		$status=$dealer_orderOb->check_construct_id($task_work, $order_id);
		if ($status){
			if ($task_work==1)$quote_status=1;
			elseif ($task_work==2)$quote_status=2;
			elseif ($task_work==3)$quote_status=4;
			elseif ($task_work==4)$quote_status=6;
			//进行质量审核
			if($quote_status!=1){
				$rs=$dealer_orderOb->update_by_id("xgj_furnish_order_detail", array("quote_status"=>$quote_status), "detail_id=$detail_id");
			}else{
				$rs=1;
			}
			$re=$dealer_orderOb->update_by_id("xgj_furnish_order_construct", array("status"=>3), "construct_id=$construct_id");
			if ($rs && $re){
				//查询系统的所有进度状态 判断订单的进度状态
				$info_status=$dealer_orderOb->get_dealer_order_info_status($order_id);
				if ($quote_status!=1){
					for ($i=0;$i<count($info_status);$i++){
						if ($quote_status!=$info_status[$i]['quote_status']){
							$result=0;
							break;
						}else{
							$result=1;
						}
					}
				}else{
					$result=1;
				}
				if ($result==1 && $quote_status!=1){
					$schedule_status=$quote_status==1?intval($quote_status):intval($quote_status)+1;
					$ru=$dealer_orderOb->update_by_id("xgj_furnish_order_info", array("schedule_status"=>$schedule_status), "order_id=$order_id");
				}else{
					$ru=1;
				}
				//var_dump($schedule_status);
				if ($ru){
					//提示信息
					$message='审核成功，正在跳转...';
					//输出提示页面
					echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
					//跳转地址 
					header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'");
				}else{
					//提示信息
					$message="审核失败，修改进度状态失败，正在跳转...";
					//输出提示页面
					echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
					//跳转地址
					header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
				}
			}else{
				//提示信息
				$message='审核失败，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		}else{
			//提示信息
			$message='请按照施工计划审核，同意订单多套系统需同步完成，方可审核，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}
	}
	/**
	 * 上传文件
	 */
	function dealer_order_file(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$order_id=intval($_GET['order_id']);
		//检测上传图片是否存在
		if(isset($_FILES)){
			//实例化UploadFile类
			$uploadOb=new UploadFile();
			//指定允许的大小
			$uploadOb->maxSize=2000000;
			//指定允许的类型
			$uploadOb->allowType=array('image/jpeg','image/gif','image/png','image/jpg',);
			//指定保存路径
			$uploadOb->savePath='../pictures/file/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='200,50';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='200,50';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/file/upload/thumb/';
			//调用上传所有文件的方法upload
			$rs=$uploadOb->upload();
			//判断是否保存成功
			if ($rs) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				$_POST['file_img']='';
				foreach ($arr as $v){
					$_POST['file_img'].=$v['savename'].'|';
				}
				$_POST['file_img']=substr($_POST['file_img'],0,-1);
			}else{
				$str=$uploadOb->getErrorMsg();
			}
		}
		$db=new db();
		$file_type=pathinfo($_POST['file_img'], PATHINFO_EXTENSION);
		$date=array(
			"file_name"=>trim($_POST['file_name']),
			'file_img'=>$_POST['file_img'],
			'detail_id'=>$_POST['detail_id'],
			'order_id'=>$order_id,
			'upload_name'=>$_COOKIE['adminUserName'],
			'file_type'=>$file_type,
			'class'=>3,
			'file_time'=>time(),
		);
		$rs=$db->add('xgj_furnish_order_file', $date);
		if ($rs) {
			$message='文件上传成功，正在跳转...';
			echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}else{
			$message='文件上传失败，正在跳转...';
			echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}
	}
	
	/**
	 * 订单调整审核
	 */
	function dealer_order_adjust(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$order_id=intval($_GET['order_id']);
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化服务商订单model类
		$dealer_orderOb=new dealer_order_model();
		if(isset($_GET['refuse'])){
			$rs=$dealer_orderOb->update_by_id("xgj_furnish_order_info", array("order_status"=>7,), "order_id=$order_id");
			if ($rs) {
				$message='拒绝调整成功，正在跳转...';
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='拒绝调整失败，正在跳转...';
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		}else{
		//订单调整表中获取一条记录
		$adjust_info=$dealer_orderOb->get_order_adjust_info($order_id);
		$date=array(
			"order_status"=>6,
			"goods_amount"=>$adjust_info[0]["goods_amount"],
			"money_paid"=>$adjust_info[0]["money_paid"],
			'type_area'=>$adjust_info[0]["type_area"],
			"house_layout"=>$adjust_info[0]["house_layout"],
			"total_area"=>$adjust_info[0]["total_area"]	
		);
		//更新订单表中的一条订单信息
		$rs=$dealer_orderOb->update_by_id("xgj_furnish_order_info", $date, "order_id=$order_id");
		foreach ($adjust_info as $v){
			$date1=array(
					"stuff_goods"=>$v["stuff_goods"],
					"quote_price"=>$v["quote_price"],
					"level"=>$v["level"],
			);
			$re=$dealer_orderOb->update_by_id("xgj_furnish_order_detail", $date1, "order_id=$order_id and quote_id={$v['quote_id']}");
			if (!$re){
				$re=0;
				break;
			}else{
				$re=1;
			}
		}
			if ($rs && $re) {
				$message='同意调整成功，正在跳转...';
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='同意调整失败，正在跳转...';
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		}
		
		
	}
	
	/**
	 * 下载文件
	 */
	function dealer_order_down(){
		ob_end_clean();
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//接收传值
		$file_name=trim($_GET['file_name']);
		$path="/xgj/source/xgjweb/pictures/file/upload/";
		down($path,$file_name);
	}
	
	
}

