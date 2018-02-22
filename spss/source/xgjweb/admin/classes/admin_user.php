<?php 
require_once(WWW_DIR."/admin/model/admin_model.php");
require_once(WWW_DIR."/libs/page.php");
class admin_user
{
	/**
	 * 后台首页
	 */
	public function admin_index(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
						
		$tpl = get_admin_smarty();
		
		$tpl->assign('permission',$permission);
				
		$tpl->display('admin_main.tpl.html');
	}
	/**
	 * 后台登录
	 */
	public function admin_login()
	{
        $tpl = get_admin_smarty();
		
		$tpl->display('admin_login.tpl.html');
	}
	/**
	 * doLogin	表示实行登录
	 */
	public function doLogin(){
	
		if(!empty($_POST)){
			
      $username=trim($_POST["userName"]);
      
      $password=md5(trim($_POST["passWord"]));
	        
			$adminLogin = new admin_model();
	
			$admin=$adminLogin->doLogin($username,$password);

			if(!empty($admin)){
				//使用cookie存储用户ID
				setcookie("adminUserId",$admin["user_id"]);
				//使用cookie存储用户名
				setcookie("adminUserName",$username);				
				//跳转到首页
				header("Location: index.php");
			}else{
				//如果登录失败
				header("Location: index.php?login");
			}
		}else{
			header("Location: index.php?login");
		}
	}
	
	/**
	 * 后台公共头部页面
	 */
	public function admin_top(){
		$tpl = get_admin_smarty();

		$tpl->display('admin_top.tpl.html');
	}
	
	/**
	 * 后台公共左侧页面
	 */
	public function admin_left(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		$tpl = get_admin_smarty();
		$tpl->assign('permission',$permission);
		$tpl->display('admin_left.tpl.html');
	}
	
	/**
	 * 后台默认右侧
	 */
	public function admin_right(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		$tpl = get_admin_smarty();
		$tpl->assign('permission',$permission);
		$tpl->display('admin_right.tpl.html');
	}
	
	/**
	 * 后台底部
	 */
	public function admin_footer(){
		$tpl = get_admin_smarty();

		$tpl->display('admin_footer.tpl.html');
	}
    
	/**
	 * 后台404页面
	 */
	public function admin_error(){
		$tpl = get_admin_smarty();
	
		$tpl->display('admin_error.tpl.html');
	}
	
	/**
	 * 退出后台
	 */
	public function logout(){
		setcookie("adminUserId","",-1);	//清除用户名信息
		setcookie("adminUserName","",-1);//清除用户名信息
		setcookie("last_login","",-1);
		header("Location:index.php");
	}
	
	/**
	 * 管理员列表
	 */
	public function permission_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化文章管理model类
		$adminOb = new admin_model();
		//搜索
		@$keyword=trim($_POST['keyword']);
		$condition="1=1";
		if(!empty($keyword)){
			$condition.=" and user_name like '%$keyword%' ";
		}
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		//显示列表内容
		$admin_list=$adminOb->show_list($page,$condition);
		//分页的总条数
		$admin_count=$adminOb->show_count();
		//实例化分页类
		$t = new Page(10, $admin_count, $page, 5, "index.php?permission&p=");
		//分页样式
		$page=$t->subPageCss2();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('keyword',$keyword);
		$tpl->assign("page",$page);
		$tpl->assign('admin_list',$admin_list);
		//显示模板
		$tpl->display('admin_permission_list.tpl.html');
	}
	
	/**
	 * 添加管理员
	 */
	public function permission_add(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
	
		$tpl = get_admin_smarty();
	
		//模板传值
		$tpl->assign('permission',$permission);
	
		$tpl->display('admin_permission_add.tpl.html');
	}
	
	/**
	 * 添加操作过程
	 */
	public function permission_add_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作
		$db=new db();
		//数据源
		$data=array(
				'user_name'=>trim($_POST['user_name']),
				'password'=>md5(trim($_POST['password'])),//密码
				'email'=>trim($_POST['email']),//email
				'tel'=>intval($_POST['tel']),//联系电话
				'linkman'=>trim($_POST['linkman']),//联系人
				'permission'=>intval($_POST['permission']),
				'add_time'=>intval(time()),
		);
		//添加一条记录到文章表中
		$rs=$db->add('xgj_admin_user', $data);
		if ($rs) {
			//提示信息
			$message='管理员添加成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'index.php?permission');
			//跳转地址
			header("refresh:2;url='index.php?permission'" );
		}else{
			//提示信息
			$message='管理员添加失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,'index.php?add');
			//跳转地址
			header("refresh:2;url='index.php?add'" );
		}
	}
	
	/**
	 * 更新管理员
	 */
	public function permission_edit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用stmarty
		$tpl = get_admin_smarty();
		//获取指定值
		$user_id=intval($_GET['user_id']);
		//实例化文章管理model类
		$adminOb = new admin_model();
		//获取一条记录
		$admin=$adminOb->getOneByid($user_id);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('admin',$admin);
		//显示模板
		$tpl->display('admin_permission_edit.tpl.html');
	}
	
	/**
	 * 更新操作过程
	 */
	public function permission_edit_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作
		$db=new db();
		//判断当前页面页码
		$page = intval($_POST['page']);
		//获取指定值
		$user_id=intval($_GET['user_id']);
		//数据源
		$data=array(
				'user_name'=>trim($_POST['user_name']),
				'password'=>md5(trim($_POST['password'])),//密码
				'email'=>trim($_POST['email']),//email
				'tel'=>intval($_POST['tel']),//联系电话
				'linkman'=>trim($_POST['linkman']),//联系人
				'permission'=>intval($_POST['permission']),
				'add_time'=>intval(time()),
		);
		//修改一条记录到文章表中
		$rs=$db->update('xgj_admin_user', $data,"user_id=$user_id");
		if ($rs) {
			//提示信息
			$message='管理员更新成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,"index.php?permission&p={$page}");
			//跳转地址
			header("refresh:2;url='index.php?permission&p={$page}'" );
		}else{
			//提示信息
			$message='管理员更新失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"index.php?edit&user_id=$user_id&p={$page}");
			//跳转地址
			header("refresh:2;url='index.php?edit&user_id=$user_id&p={$page}'" );
		}
	}
	/**
	 * 删除一条信息
	 */
	public function permission_del(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定信息
		$user_id=intval($_GET['user_id']);
		//实例化管理员管理model类
		$adminOb=new admin_model();
		//删除一条记录
		$rs=$adminOb->del_admin_user_id($user_id);
		//判断并显示提示信息
		if($rs){
			//提示信息
			$message='删除管理员成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}else{
			//提示信息
			$message='删除管理员失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
			//跳转地址
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}
	}
	
	/**
	 * 普通管理员修改信息
	 */
	public function permission_info(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用stmarty
		$tpl = get_admin_smarty();
		//获取指定值
		$user_id=intval($_GET['user_id']);
		//实例化文章管理model类
		$adminOb = new admin_model();
		//获取一条记录
		$admin=$adminOb->getOneByid($user_id);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('admin',$admin);
		//显示模板
		$tpl->display('admin_permission_info.tpl.html');
	}
	
	/**
	 * 普通管理员修改信息操作过程
	 */
	public function permission_info_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作
		$db=new db();
		//获取指定值
		$user_id=intval($_GET['user_id']);
		//实例化文章管理model类
		$adminOb = new admin_model();
		//获取一条记录
		$admin=$adminOb->getOneByid($user_id);
		if(!empty($_POST['oldpassword']) && md5(trim($_POST['oldpassword']))==$admin['password']){
			if(!empty($_POST['password1']) && !empty($_POST['password2']) && trim($_POST['password1'])==trim($_POST['password2'])){
				//数据源
				$data=array(
						'password'=>md5(trim($_POST['password1'])),//密码
						'email'=>trim($_POST['email']),//email
						'tel'=>intval($_POST['tel']),//联系电话
						'linkman'=>trim($_POST['linkman']),//联系人
						'add_time'=>intval(time()),
				);
				//修改一条记录到文章表中
				$rs=$db->update('xgj_admin_user', $data,"user_id=$user_id");
				if ($rs) {
					//提示信息
					$message='信息修改成功，正在跳转...';
					//输出提示页面
					echo jump(1,$message,"index.php");
					//跳转地址
					header("refresh:2;url='index.php'" );
				}else{
					//提示信息
					$message='信息修改失败，正在跳转...';
					//输出提示页面
					echo jump(2,$message,"index.php?info&user_id=$user_id");
					//跳转地址
					header("refresh:2;url='index.php?info&user_id=$user_id'" );
				}
			}else{
				//提示信息
				$message='两次密码不一致，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"index.php?info&user_id=$user_id");
				//跳转地址
				header("refresh:2;url='index.php?info&user_id=$user_id'" );
			}
		}else{
			//提示信息
			$message='旧密码错误，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"index.php?info&user_id=$user_id");
			//跳转地址
			header("refresh:2;url='index.php?info&user_id=$user_id'" );
		}
		
		
	}
}