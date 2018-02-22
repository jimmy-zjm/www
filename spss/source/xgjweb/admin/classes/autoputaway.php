<?php
require_once(WWW_DIR."/admin/model/autoputaway_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
class autoputaway
{
	/**
	 * 商品上下架列表
	 */
	function autoputaway_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//判断tab页面
		$tab=intval($_GET['tab']);
		//实例化数据库操作类
		$autoputawayOb=new autoputaway_model();
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		//显示列表内容
		$autoputaway_list_goods=$autoputawayOb->show_list_goods($page);
		//分页的总条数
		$autoputaway_count_goods=$autoputawayOb->show_count_goods();
		//实例化分页类
		$t_goods = new Page(10, $autoputaway_count_goods, $page, 5, "autoputaway.php?tab=1&p=");
		//分页样式
		$page_goods=$t_goods->subPageCss2();
		//显示列表内容
		$autoputaway_list_quote=$autoputawayOb->show_list_quote($page);
		//分页的总条数
		$autoputaway_count_quote=$autoputawayOb->show_count_quote();
		//实例化分页类
		$t_quote = new Page(10, $autoputaway_count_quote, $page, 5, "autoputaway.php?tab=2&p=");
		//分页样式
		$page_quote=$t_quote->subPageCss2();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign("page_goods",$page_goods);
		$tpl->assign('autoputaway_list_goods',$autoputaway_list_goods);
		$tpl->assign('tab',$tab);
		$tpl->assign("page_quote",$page_quote);
		$tpl->assign('autoputaway_list_quote',$autoputaway_list_quote);
		//显示模板
		$tpl->display('admin_autoputaway_list.tpl.html');
	}
	
	/**
	 * 修改材料商品上下架
	 */
	function goods_edit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定值
		$goods_id=intval($_GET['goods_id']);
		//实例化数据库操作类
		$db=new db();
		//获取数据源
		$data=array(
				//'is_putaway'=>intval($_GET['is_putaway']),
				'starttime'=>strtotime($_GET['starttime']),
				'endtime'=>strtotime($_GET['endtime']),
		);
		//执行并获取更新结果
		$rs=$db->update('xgj_furnish_goods',$data,"goods_id=$goods_id");
		//判断并输出
		if($rs){
			//返回成功
			echo trim('success');
		}else{
			//返回失败
			echo trim('lose');
		}
	}
	
	/**
	 *批量修改材料商品上下架
	 */
	function goods_edit_all(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$autoputawayOb=new autoputaway_model();
		//获取批量上下架时间
		$batchtime=strtotime($_POST['batchtime']);
		//判断是否有分页
		$page = intval($_POST['page']);
		//获取批量上下架的条件
		$str='';
		foreach ($_POST['goods'] as $v){
			$str.="'$v',";
		}
		$where=rtrim($str,',');
		//判断是上架还是下架
		if (isset($_POST['batch']) && !empty($_POST['batch']) && trim($_POST['batch'])=='批量上架'){
			$field="starttime='$batchtime'";//上架字段及值
		}else if(isset($_POST['batch']) && !empty($_POST['batch']) && trim($_POST['batch'])=='批量下架'){
			$field="endtime='$batchtime'";//下架字段及值
		}
		//执行并获取更新结果
		$rs=$autoputawayOb->batchUpdateTime("xgj_furnish_goods",$field,"goods_id","($where)");
		//判断并跳转提示
		if($rs){
			//提示信息
			$message="{$_POST['batch']}成功，正在跳转...";
			//输出提示页面
			echo jump(1,$message,"autoputaway.php?tab=1&p={$page}");
			//跳转地址
			header("refresh:2;url='autoputaway.php?tab=1&p={$page}'" );
		}else{
			//提示信息
			$message="{$_POST['batch']}失败，正在跳转...";
			//输出提示页面
			echo jump(2,$message,"autoputaway.php?tab=1&p={$page}");
			//跳转地址
			header("refresh:2;url='autoputaway.php?tab=1&p={$page}'" );
		}
	}
	
	/**
	 * 修改系统商品上下架
	 */
	function quote_edit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定值
		$quote_id=intval($_GET['quote_id']);
		//实例化数据库操作类
		$db=new db();
		//数据源
		$data=array(
				//'is_putaway'=>intval($_GET['is_putaway']),
				'starttime'=>strtotime($_GET['starttime']),
				'endtime'=>strtotime($_GET['endtime']),
		);
		//执行并获取更新结果
		$rs=$db->update('xgj_furnish_quote',$data,"quote_id=$quote_id");
		//判断并输出
		if($rs){
			//返回成功
			echo trim('success');
		}else{
			//返回失败
			echo trim('lose');
		}
	}
	
	/**
	 *批量修改系统商品上下架
	 */
	function quote_edit_all(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//var_dump($_POST['page']);exit;
		//实例化数据库操作类
		$autoputawayOb=new autoputaway_model();
		//获取批量上下架时间
		$batchtime=strtotime($_POST['batchtime']);
		//判断是否有分页
		$page = intval($_POST['page']);
		//获取批量上下架的条件
		$str='';
		foreach ($_POST['quote'] as $v){
			$str.="'$v',";
		}
		$where=rtrim($str,',');
		//判断是上架还是下架
		if (isset($_POST['batch']) && !empty($_POST['batch']) && trim($_POST['batch'])=='批量上架'){
			$field="starttime='$batchtime'";//上架字段及值
		}else if(isset($_POST['batch']) && !empty($_POST['batch']) && trim($_POST['batch'])=='批量下架'){
			$field="endtime='$batchtime'";//下架字段及值
		}
		//执行并获取更新结果
		$rs=$autoputawayOb->batchUpdateTime("xgj_furnish_quote",$field,"quote_id","($where)");
		//判断并跳转提示
		if($rs){
			//提示信息
			$message="{$_POST['batch']}成功，正在跳转...";
			//输出提示页面
			echo jump(1,$message,"autoputaway.php?tab=2&p={$page}");
			//跳转地址
			header("refresh:2;url='autoputaway.php?tab=2&p={$page}'" );
		}else{
			//提示信息
			$message="{$_POST['batch']}失败，正在跳转...";
			//输出提示页面
			echo jump(2,$message,"autoputaway.php?tab=2&p={$page}");
			//跳转地址
			header("refresh:2;url='autoputaway.php?tab=2&p={$page}'" );
		}
	}
}