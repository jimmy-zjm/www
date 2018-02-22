<?php 
require_once(WWW_DIR."/admin/model/comment_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
/**
 * @author Administrator
 * 用户评价操作
 */
class comment
{
	/**
	 * 用户评价列表
	 */
	function furnish_comment_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化用户评价model类
		$commentOb=new comment_model();
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		//显示列表内容
		$furnish_comment_list=$commentOb->furnish_show_list($page);
		//分页的总条数
		$furnish_comment_count=$commentOb->furnish_show_count();
		//实例化分页类
		$t = new Page(10, $furnish_comment_count, $page, 5, "comment.php?p=");
		//分页样式
		$page=$t->subPageCss2();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign("page",$page);
		$tpl->assign('furnish_comment_list',$furnish_comment_list);
		//显示模板
		$tpl->display('admin_furnish_comment_list.tpl.html');
	}
	
	/**
	 * 用户评论详细信息
	 */
	function comment_info(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化用户评价model类
		$commentOb=new comment_model();
		//调用smarty
		$tpl = get_admin_smarty();
		//获取指定值
		$comment_id=intval($_GET['comment_id']);
		//获取一条信息
		$info_list=$commentOb->comment_id($comment_id);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign("info_list",$info_list);
		//显示模板
		$tpl->display('admin_comment_info.tpl.html');
	}
	
	function comment_change_status(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化用户评价model类
		$db=new db();
		//获取指定值
		$comment_id=intval($_GET['comment_id']);
		$status=intval($_GET['status']);
		//var_dump($status);exit;
		//数据源
		$data=array(
				"status"=>$status,
		);
		//更新一条信息
		$rs=$db->update('xgj_comment',$data, "comment_id=$comment_id");
		if ($rs){
			//跳转页面
			header("Location:comment.php?info&comment_id=$comment_id" );
		}else{
			//跳转页面
			header("Location:comment.php?info&comment_id=$comment_id" );
		}
	}
	
	function comment_del_status(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化用户评价model类
		$db=new db();
		//获取指定值
		$comment_id=intval($_GET['comment_id']);
		$status=intval($_GET['status']);
		//var_dump($status);exit;
		//数据源
		$data=array(	
				"status"=>$status,
		);
		//更新一条信息
		$rs=$db->update('xgj_comment',$data, "comment_id=$comment_id");
		if ($rs){
			//跳转页面
			header("Location:comment.php?furnish" );
		}else{
			//跳转页面
			header("Location:comment.php?furnish" );
		}
	}
}

