<?php 
require_once(WWW_DIR."/admin/model/dealer_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
/**
 * @author Administrator
 * 服务商操作
 */
class dealer
{
	/**
	 * 服务商列表
	 */
	function dealer_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化服务商model类
		$dealerOb=new dealer_model();
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		//搜索d_district
		@$keyword=trim($_POST['keyword']);
		@$district=trim($_GET['district']);
		//var_dump($district);exit;
		$condition="";
		if(!empty($keyword)){
			$condition.=" and d_company like '%$keyword%' ";
		}else if(!empty($district)){
			$condition.=" and d_district = '$district' ";
		}
		//显示列表内容
		$dealer_list=$dealerOb->show_list($page,$condition);
		//分页的总条数
		$dealer_count=$dealerOb->show_count($condition);
		//实例化分页类
		$t = new Page(10, $dealer_count, $page, 5, "dealer.php?p=");
		//分页样式
		$page=$t->subPageCss2();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('keyword',$keyword);
		$tpl->assign("page",$page);
		$tpl->assign('dealer_list',$dealer_list);
		//显示模板
		$tpl->display('admin_dealer_list.tpl.html');
	}
	/**
	 * 添加服务商信息及账号
	 */
	function dealer_add(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//模板传值
		$tpl->assign('permission',$permission);
		//显示模板
		$tpl->display('admin_dealer_add.tpl.html');
	}
	/**
	 * 添加-操作过程
	 */
	function dealer_add_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作
		$db=new db();
		//检测上传图片是否存在
		if(isset($_FILES)){
			//实例化UploadFile类
			$uploadOb=new UploadFile();
			//指定允许的大小
			$uploadOb->maxSize=2000000;
			//指定允许的类型
			$uploadOb->allowType=array('image/jpeg','image/gif','image/png','image/jpg',);
			//指定保存路径
			$uploadOb->savePath='../pictures/dealer/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='200,50';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='200,50';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/dealer/upload/thumb/';
			//调用上传所有文件的方法upload
			$rs=$uploadOb->upload();
			//判断是否保存成功
			if ($rs) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				//var_dump($arr);exit;
				//产生图片新的名称
				$_POST['d_store_figure']=$arr[0]['savename'];
				$_POST['d_map']=$arr[1]['savename'];
				$count  =  count($arr);
				$d_hall_figure='';
				for  ($i=2;  $i<$count;  $i++)  {
					$d_hall_figure.=$arr[$i]['savename'].'|';
				}
				$_POST['d_hall_figure']=rtrim($d_hall_figure,'|');
			}else{
				//上传时出现的错误信息（调试时使用）
				$str=$uploadOb->getErrorMsg();
			}
		}	
		//记录添加时间
		$_POST['add_time']=time();
		//添加一条记录到服务商表中
		$rs=$db->add('xgj_furnish_dealer', $_POST);
		if ($rs) {
			//提示信息
			$message='服务商添加成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'dealer.php');
			//跳转地址
			header("refresh:2;url='dealer.php'" );
		}else{
			//提示信息
			$message='服务商添加失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,'dealer.php');
			//跳转地址
			header("refresh:2;url='dealer.php'" );
		}
	}
	/**
	 * 修改服务商信息
	 */
	function dealer_edit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定信息
		$d_id=intval($_GET['d_id']);
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化服务商model类
		$dealerOb=new dealer_model();
		//根据id获取一条信息
		$dealer=$dealerOb->dealer_d_id($d_id);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('dealer',$dealer);
		//显示模板
		$tpl->display('admin_dealer_edit.tpl.html');
	}
	/**
	 * 修改-操作过程
	 */
	function dealer_edit_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定信息
		$d_id=intval($_GET['d_id']);
		//实例化数据库操作
		$db=new db();
		//检测上传图片是否存在
		if(isset($_FILES)){
			//实例化UploadFile类
			$uploadOb=new UploadFile();
			//指定允许的大小
			$uploadOb->maxSize=2000000;
			//指定允许的类型
			$uploadOb->allowType=array('image/jpeg','image/jpg','image/gif','image/png');
			//指定保存路径
			$uploadOb->savePath='../pictures/dealer/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='200,50';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='200,50';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/dealer/upload/thumb/';
			//调用上传所有文件的方法upload
			$result=$uploadOb->upload();
			//判断是否保存成功
			if ($result) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				//var_dump($arr);exit;
				if ($arr[0]['key']=='d_store_figure' && @$arr[1]['key']=='d_map' && @$arr[2]['key']=='d_hall_figure'){
					//产生图片的新名称
					$d_store_figure=$arr[0]['savename'];
					$d_map=$arr[1]['savename'];
					$count  =  count($arr);
					$d_hall_figure='';
					for  ($i=2;  $i<$count;  $i++)  {
						$d_hall_figure.=$arr[$i]['savename'].'|';
					}
					$d_hall_figures=rtrim($d_hall_figure,'|');
					$date=array('d_store_figure'=>$d_store_figure,'d_map'=>$d_map,'d_hall_figure'=>$d_hall_figures);
					$re=$db->update('xgj_furnish_dealer', $date,"d_id=$d_id");
					//判断成功就删除旧图片
					if ($re) {
						//获取旧图片的名称
						$old_store_figure=$_POST['old_store_figure'];
						$old_map=$_POST['old_map'];
						$old_hall_figure=explode('|', $_POST['old_hall_figure']);
						//删除旧图片
						@unlink(WWW_DIR."/pictures/dealer/upload/$old_store_figure");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$old_store_figure");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$old_store_figure");
						@unlink(WWW_DIR."/pictures/dealer/upload/$old_map");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$old_map");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$old_map");
						foreach ($old_hall_figure as $k=>$v){
							@unlink(WWW_DIR."/pictures/dealer/upload/$v");
							@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$v");
							@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$v");
						}
					}
				}else if($arr[0]['key']=='d_store_figure' && @$arr[1]['key']=='d_map'){
					$d_store_figure=$arr[0]['savename'];
					$d_map=$arr[1]['savename'];
					$date=array('d_map'=>$d_map,'d_store_figure'=>$d_store_figure);
					$re=$db->update('xgj_furnish_dealer', $date,"d_id=$d_id");
					//判断成功就删除旧图片
					if ($re) {
						//获取旧图片的名称
						$old_store_figure=$_POST['old_store_figure'];
						$old_map=$_POST['old_map'];
						//删除旧图片
						@unlink(WWW_DIR."/pictures/dealer/upload/$old_store_figure");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$old_store_figure");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$old_store_figure");
						@unlink(WWW_DIR."/pictures/dealer/upload/$old_map");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$old_map");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$old_map");
					}
				}else if($arr[0]['key']=='d_map' && @$arr[1]['key']=='d_hall_figure'){
					$d_map=$arr[0]['savename'];
					$count  =  count($arr);
					$d_hall_figure='';
					for  ($i=1;  $i<$count;  $i++)  {
						$d_hall_figure.=$arr[$i]['savename'].'|';
					}
					$d_hall_figures=rtrim($d_hall_figure,'|');
					$date=array('d_map'=>$d_map,'d_hall_figure'=>$d_hall_figures);
					$re=$db->update('xgj_furnish_dealer', $date,"d_id=$d_id");
					//判断成功就删除旧图片
					if ($re) {
						//获取旧图片的名称
						$old_map=$_POST['old_map'];
						$old_hall_figure=explode('|', $_POST['old_hall_figure']);
						//删除旧图片
						@unlink(WWW_DIR."/pictures/dealer/upload/$old_map");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$old_map");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$old_map");
						foreach ($old_hall_figure as $k=>$v){
							@unlink(WWW_DIR."/pictures/dealer/upload/$v");
							@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$v");
							@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$v");
						}
					}
				}else if($arr[0]['key']=='d_store_figure' && @$arr[1]['key']=='d_hall_figure'){
					$d_store_figure=$arr[0]['savename'];
					$count  =  count($arr);
					$d_hall_figure='';
					for  ($i=1;  $i<$count;  $i++)  {
						$d_hall_figure.=$arr[$i]['savename'].'|';
					}
					$d_hall_figures=rtrim($d_hall_figure,'|');
					$date=array('d_store_figure'=>$d_store_figure,'d_hall_figure'=>$d_hall_figures);
					$re=$db->update('xgj_furnish_dealer', $date,"d_id=$d_id");
					//判断成功就删除旧图片
					if ($re) {
						//获取旧图片的名称
						$old_store_figure=$_POST['old_store_figure'];
						$old_hall_figure=explode('|', $_POST['old_hall_figure']);
						//删除旧图片
						@unlink(WWW_DIR."/pictures/dealer/upload/$old_store_figure");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$old_store_figure");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$old_store_figure");
						foreach ($old_hall_figure as $k=>$v){
							@unlink(WWW_DIR."/pictures/dealer/upload/$v");
							@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$v");
							@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$v");
						}
					}
				}else if($arr[0]['key']=='d_store_figure'){
					$d_store_figure=$arr[0]['savename'];
					$date=array('d_store_figure'=>$d_store_figure);
					$re=$db->update('xgj_furnish_dealer', $date,"d_id=$d_id");
					//判断成功就删除旧图片
					if ($re) {
						//获取旧图片的名称
						$old_store_figure=$_POST['old_store_figure'];
						$old_map=$_POST['old_map'];
						//删除旧图片
						@unlink(WWW_DIR."/pictures/dealer/upload/$old_store_figure");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$old_store_figure");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$old_store_figure");
					}
				}else if($arr[0]['key']=='d_map'){
					$d_map=$arr[0]['savename'];
					$date=array('d_map'=>$d_map);
					$re=$db->update('xgj_furnish_dealer', $date,"d_id=$d_id");
					//判断成功就删除旧图片
					if ($re) {
						//获取旧图片的名称
						$old_store_figure=$_POST['old_store_figure'];
						$old_map=$_POST['old_map'];
						//删除旧图片
						@unlink(WWW_DIR."/pictures/dealer/upload/$old_map");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$old_map");
						@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$old_map");
					}
				}else{
					$count  =  count($arr);
					$d_hall_figure='';
					for  ($i=0;  $i<$count;  $i++)  {
						$d_hall_figure.=$arr[$i]['savename'].'|';
					}
					$d_hall_figures=rtrim($d_hall_figure,'|');
					$date=array('d_hall_figure'=>$d_hall_figures);
					$re=$db->update('xgj_furnish_dealer', $date,"d_id=$d_id");
					//判断成功就删除旧图片
					if ($re) {
						//获取旧图片的名称
						$old_hall_figure=explode('|', $_POST['old_hall_figure']);
						//删除旧图片
						foreach ($old_hall_figure as $k=>$v){
							@unlink(WWW_DIR."/pictures/dealer/upload/$v");
							@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$v");
							@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$v");
						}
					}	
				}
			}else{
				//上传时出现的错误信息（调试时使用）
				$str=$uploadOb->getErrorMsg();
			}
			//获取数据
			$data=array(
					'd_company'=>trim($_POST['d_company']),//服务商公司名称
					'd_name'=>trim($_POST['d_name']),//账号
					'd_pwd'=>trim($_POST['d_pwd']),//密码
					'd_email'=>trim($_POST['d_email']),//email
					'd_district'=>trim($_POST['d_district']),//区域
					'd_province'=>trim($_POST['d_province']),//省份
					'd_city'=>trim($_POST['d_city']),//城市
					'd_linkman'=>trim($_POST['d_linkman']),//联系人
					'd_link_phone'=>intval($_POST['d_link_phone']),//联系电话
					'd_legalperson'=>trim($_POST['d_legalperson']),//负责人
					'd_legalperson_phone'=>intval($_POST['d_legalperson_phone']),//负责人电话
					'd_rel_address'=>trim($_POST['d_rel_address']),//办公地点
					'd_pickup_address'=>trim($_POST['d_pickup_address']),//送货地点
					'd_hall_address'=>trim($_POST['d_hall_address']),//展厅地点
					'd_company_desc'=>html_filter($_POST['d_company_desc']),//服务商公司简介
					'd_rank'=>trim($_POST['d_rank']),//级别
					'd_runstatus'=>intval($_POST['d_runstatus']),//状态
					'add_time'=>intval(time()),//修改时间
					
			);
			//修改一条服务商信息
			$rs=$db->update('xgj_furnish_dealer',$data,"d_id=$d_id");
			//判断显示提示信息并跳转
			if (empty($re) || $rs || $re) {
				//提示信息
				$message='服务商修改成功，正在跳转...';
				//输出提示页面
				echo jump(1,$message,'dealer.php');
				//跳转地址
				header("refresh:2;url='dealer.php'" );
			}else{
				//提示信息
				$message='服务商修改失败，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		}
	}
	/**
	 * 删除一条信息
	 */
	function dealer_del(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定信息
		$d_id=intval($_GET['d_id']);
		//实例化服务商model类
		$dealerOb=new dealer_model();
		/* //根据id获取一条信息
		$dealer=$dealerOb->dealer_d_id($d_id);
		//得到图片名称
		$imgName=$dealer['d_img'];
		//判断进行删除操作
		if (!empty($imgName)) {
			//删除图片
			@unlink(WWW_DIR."/pictures/dealer/upload/$imgName");
			@unlink(WWW_DIR."/pictures/dealer/upload/thumb/thumb_$imgName");
			@unlink(WWW_DIR."/pictures/dealer/upload/thumb/s_$imgName");
			//删除一条记录
			$rs=$dealerOb->del_dealer_d_id($d_id);
			//判断并显示提示信息
			if($rs){
				//提示信息
				$message='删除服务商与图片成功，正在跳转...';
				//输出提示页面
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				//提示信息
				$message='删除服务商与图片失败，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		}else{ */
			//删除一条记录
			$rs=$dealerOb->del_dealer_d_id($d_id);
			//判断并显示提示信息
			if($rs){
				//提示信息
				$message='删除服务商成功，正在跳转...';
				//输出提示页面
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				//提示信息
				$message='删除服务商失败，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		/* } */
	}
	
	/**
	 * 服务商信息详情
	 */
	function dealer_info(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定信息
		$d_id=intval($_GET['d_id']);
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化服务商model类
		$dealerOb=new dealer_model();
		//根据id获取一条信息
		$dealer=$dealerOb->dealer_d_id($d_id);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('dealer',$dealer);
		//显示模板
		$tpl->display('admin_dealer_info.tpl.html');
	}
}

