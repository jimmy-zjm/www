<?php 
require_once(WWW_DIR."/admin/model/article_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
/**
 * @author Administrator
 * 文章操作
 */
class article
{
	/**
	 * 文章分类列表
	 */
	function article_cat_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化文章管理model类
		$articleOb=new article_model();
		//显示分类列表
		$articleList=$articleOb->article_cat_list();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('articleList',$articleList);
		//显示模板
		$tpl->display("admin_article_cat_list.tpl.html");
	}
	
	/**
	 * 添加文章分类
	 */
	function article_cat_add(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化文章管理model类
		$articleOb=new article_model();
		//显示子分类
		$option=$articleOb->article_cat_option();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('option',$option);
		//显示模板
		$tpl->display("admin_article_cat_add.tpl.html");
	}
	/**
	 * 添加-操作过程
	 */
	function article_cat_add_save(){
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
		$rs=$db->add('xgj_article_cat', $data);
		if ($rs) {
			//提示信息
			$message='文章分类添加成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'article.php?cat');
			//跳转地址
			header("refresh:2;url='article.php?cat'" );
		}else{
			//提示信息
			$message='文章分类添加失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,'article.php?cat');
			//跳转地址
			header("refresh:2;url='article.php?cat'" );
		}
	}
	
	/**
	 * 修改文章分类
	 */
	function article_cat_edit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取所需的唯一id
		$cat_id=intval($_GET['cat_id']);
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化文章管理model类
		$articleOb=new article_model();
		//显示一条编辑信息
		$info=$articleOb->article_cat_id($cat_id);
		//显示子分类
		$option=$articleOb->article_cat_option(0,0,$info['parent_id']);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('info',$info);
		$tpl->assign('option',$option);
		//显示模板
		$tpl->display("admin_article_cat_edit.tpl.html");
	}
	
	/**
	 * 修改-操作过程
	 */
	function article_cat_edit_save(){
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
		$rs=$db->update('xgj_article_cat', $data , "cat_id=$cat_id");
		if ($rs) {
			//提示信息
			$message='文章分类修改成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'article.php?cat');
			//跳转地址
			header("refresh:2;url='article.php?cat'" );
		}else{
			//提示信息
			$message='文章分类修改失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"article.php?cat_edit&cat_id=$cat_id");
			//跳转地址
			header("refresh:2;url='article.php?cat_edit&cat_id=$cat_id'" );
		}
	}
	
	function article_cat_del(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取所需的唯一id
		$cat_id=intval($_GET['cat_id']);
		//实例化文章管理model类
		$articleOb=new article_model();
		//删除一条文章分类信息
		$rs=$articleOb->article_cat_del($cat_id);
		if ($rs) {
			//提示信息
			$message='文章分类删除成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}else{
			//提示信息
			$message='文章分类删除失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}
	}
	
	/**
	 * 文章列表
	 */
	function article_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化文章管理model类
		$articleOb=new article_model();
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		//显示列表内容
		$article_list=$articleOb->show_list($page);
		//分页的总条数
		$article_count=$articleOb->show_count();
		//实例化分页类
		$t = new Page(10, $article_count, $page, 5, "article.php?p=");
		//分页样式
		$page=$t->subPageCss2();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign("page",$page);
		$tpl->assign('article_list',$article_list);
		//显示模板
		$tpl->display('admin_article_list.tpl.html');
	}
	/**
	 * 添加文章信息及账号
	 */
	function article_add(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化文章管理model类
		$articleOb=new article_model();
		//显示子分类
		$option=$articleOb->article_cat_option();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('option',$option);
		//显示模板
		$tpl->display('admin_article_add.tpl.html');
	}
	/**
	 * 添加-操作过程
	 */
	function article_add_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作
		$db=new db();
		//数据源
		$data=array(
				'title'=>trim($_POST['title']),
				'content'=>html_filter($_POST['content']),
				'cat_id'=>intval($_POST['parent_id']),
				'add_time'=>intval(time()),
		);
		//添加一条记录到文章表中
		$rs=$db->add('xgj_article', $data);
		if ($rs) {
			//提示信息
			$message='文章添加成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'article.php');
			//跳转地址
			header("refresh:2;url='article.php'" );
		}else{
			//提示信息
			$message='文章添加失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,'article.php');
			//跳转地址
			header("refresh:2;url='article.php'" );
		}
	}
	/**
	 * 修改文章信息
	 */
	function article_edit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定信息
		$article_id=intval($_GET['article_id']);
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化文章管理model类
		$articleOb=new article_model();
		//根据id获取一条信息
		$article=$articleOb->article_article_id($article_id);
		//显示子分类
		$option=$articleOb->article_cat_option(0,0,$article['cat_id']);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('option',$option);
		$tpl->assign('article',$article);
		//显示模板
		$tpl->display('admin_article_edit.tpl.html');
	}
	/**
	 * 修改-操作过程
	 */
	function article_edit_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定信息
		$article_id=intval($_GET['article_id']);
		//实例化数据库操作
		$db=new db();
		//数据源
		$data=array(
				'title'=>trim($_POST['title']),
				'content'=>html_filter($_POST['content']),
				'cat_id'=>intval($_POST['parent_id']),
				'add_time'=>intval(time()),
		);
		//修改一条文章信息
		$rs=$db->update('xgj_article',$data,"article_id=$article_id");
		//判断显示提示信息并跳转
		if ($rs) {
			//提示信息
			$message='文章修改成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'article.php');
			//跳转地址
			header("refresh:2;url='article.php'" );
		}else{
			//提示信息
			$message='文章修改失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"article.php?edit&article_id=$article_id");
			//跳转地址
			header("refresh:2;url='article.php?edit&article_id=$article_id'" );
		}
	}
	
	/**
	 * 删除一条信息
	 */
	function article_del(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定信息
		$article_id=intval($_GET['article_id']);
		//实例化文章管理model类
		$articleOb=new article_model();
		//删除一条记录
		$rs=$articleOb->del_article_article_id($article_id);
		//判断并显示提示信息
		if($rs){
			//提示信息
			$message='删除文章成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}else{
			//提示信息
			$message='删除文章失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}
	}
}

