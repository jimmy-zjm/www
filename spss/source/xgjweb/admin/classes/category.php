<?php 
require_once(WWW_DIR."/admin/model/category_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
/**
 * @author Administrator
 * 系统分类操作
 */
class category
{
	/**
	 * 系统分类列表
	 */
	function category_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化系统分类model类
		$categoryOb=new category_model();
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		//显示列表内容
		$category_list=$categoryOb->show_list($page);
		//分页的总条数
		$category_count=$categoryOb->show_count();
		//实例化分页类
		$t = new Page(10, $category_count, $page, 5, "category.php?p=");
		//分页样式
		$page=$t->subPageCss2();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign("page",$page);
		$tpl->assign('category_list',$category_list);
		//显示模板
		$tpl->display('admin_category_list.tpl.html');
	}
	/**
	 * 添加系统分类信息及账号
	 */
	function category_add(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//显示模板
		$tpl->display('admin_category_add.tpl.html');
	}
	/**
	 * 添加-操作过程
	 */
	function category_add_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作
		$db=new db();
		//获取数据
		$data=array(
				'cat_name'=>trim($_POST['cat_name']),//系统分类名称
		);
		//添加一条记录到系统分类表中
		$rs=$db->add('xgj_furnish_cat', $data);
		if ($rs) {
			//提示信息
			$message='系统分类添加成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'category.php');
			//跳转地址
			header("refresh:2;url='category.php'" );
		}else{
			//提示信息
			$message='系统分类添加失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,'category.php');
			//跳转地址
			header("refresh:2;url='category.php'" );
		}
	}
	/**
	 * 修改系统分类信息
	 */
	function category_edit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定信息
		$cat_id=intval($_GET['cat_id']);
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化系统分类model类
		$categoryOb=new category_model();
		//根据id获取一条信息
		$category=$categoryOb->category_cat_id($cat_id);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('category',$category);
		//显示模板
		$tpl->display('admin_category_edit.tpl.html');
	}
	/**
	 * 修改-操作过程
	 */
	function category_edit_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作
		$db=new db();
		//获取指定信息
		$cat_id=intval($_GET['cat_id']);
		//获取数据
		$data=array(
				'cat_name'=>trim($_POST['cat_name']),//系统分类名称
		);
		//修改一条系统分类信息
		$rs=$db->update('xgj_furnish_cat',$data,"cat_id=$cat_id");
		//判断显示提示信息并跳转
		if ($rs) {
			//提示信息
			$message='系统分类修改成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'category.php');
			//跳转地址
			header("refresh:2;url='category.php'" );
		}else{
			//提示信息
			$message='系统分类修改失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"category.php?edit&cat_id=$cat_id");
			//跳转地址
			header("refresh:2;url='category.php?edit&cat_id=$cat_id'" );
		}
	}
	
	/**
	 * 删除一条信息
	 */
	function category_del(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定信息
		$cat_id=intval($_GET['cat_id']);
		//实例化系统分类model类
		$categoryOb=new category_model();
		//删除一条记录
		$rs=$categoryOb->del_category_cat_id($cat_id);
		//判断并显示提示信息
		if($rs){
			//提示信息
			$message='删除系统分类成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}else{
			//提示信息
			$message='删除系统分类失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}
	}
}
