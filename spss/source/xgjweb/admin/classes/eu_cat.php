<?php 
require_once(WWW_DIR."/admin/model/eu_cat_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
/**
 * @author Administrator
 * 商品操作
 */
class eu_cat
{
	/**
	 * 商品分类列表
	 */
	function eu_cat_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化商品管理model类
		$euOb=new eu_cat_model();
		//显示分类列表
		$euList=$euOb->eu_cat_list();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('euList',$euList);
		//显示模板
		$tpl->display("admin_eu_cat_list.tpl.html");
	}
	
	/**
	 * 添加商品分类
	 */
	function eu_cat_add(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化商品管理model类
		$euOb=new eu_cat_model();
		//显示子分类
		$option=$euOb->eu_cat_option();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('option',$option);
		//显示模板
		$tpl->display("admin_eu_cat_add.tpl.html");
	}
	/**
	 * 添加-操作过程
	 */
	function eu_cat_add_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作
		$db=new db();
		//数据源
		$data=array(
			'cat_name'=>trim($_POST['cat_name']),
			'parent_id'=>intval($_POST['parent_id']),
			'sort_order'=>intval($_POST['sort_order']),
		);
		//添加一条记录到数据库
		$rs=$db->add('xgj_eu_cat', $data);
		if ($rs) {
			//提示信息
			$message='商品分类添加成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'eu_cat.php');
			//跳转地址
			header("refresh:2;url='eu_cat.php'" );
		}else{
			//提示信息
			$message='商品分类添加失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,'eu_cat.php');
			//跳转地址
			header("refresh:2;url='eu_cat.php'" );
		}
	}
	
	/**
	 * 修改商品分类
	 */
	function eu_cat_edit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取所需的唯一id
		$cat_id=intval($_GET['cat_id']);
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化商品管理model类
		$euOb=new eu_cat_model();
		//显示一条编辑信息
		$info=$euOb->eu_cat_id($cat_id);
		//显示子分类
		$option=$euOb->eu_cat_option(0,0,$info['parent_id']);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('info',$info);
		$tpl->assign('option',$option);
		//显示模板
		$tpl->display("admin_eu_cat_edit.tpl.html");
	}
	
	/**
	 * 修改-操作过程
	 */
	function eu_cat_edit_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取所需的唯一id
		$cat_id=intval($_GET['cat_id']);
		//实例化数据库操作
		$db=new db();
		//数据源
		$data=array(
				'cat_name'=>trim($_POST['cat_name']),
				'parent_id'=>intval($_POST['parent_id']),
				'sort_order'=>intval($_POST['sort_order']),
		);
		//修改一条记录到数据库
		$rs=$db->update('xgj_eu_cat', $data , "cat_id=$cat_id");
		if ($rs) {
			//提示信息
			$message='商品分类修改成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'eu_cat.php');
			//跳转地址
			header("refresh:2;url='eu_cat.php'" );
		}else{
			//提示信息
			$message='商品分类修改失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"eu_cat.php_edit&cat_id=$cat_id");
			//跳转地址
			header("refresh:2;url='eu_cat.php_edit&cat_id=$cat_id'" );
		}
	}
	
	function eu_cat_del(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取所需的唯一id
		$cat_id=intval($_GET['cat_id']);
		//实例化商品管理model类
		$euOb=new eu_cat_model();
		//删除一条商品分类信息
		$rs=$euOb->eu_cat_del($cat_id);
		if ($rs) {
			//提示信息
			$message='商品分类删除成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}else{
			//提示信息
			$message='商品分类删除失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}
	}
	
}

