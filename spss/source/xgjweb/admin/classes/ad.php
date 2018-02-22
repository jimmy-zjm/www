<?php
require_once(WWW_DIR."/admin/model/ad_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
class ad
{
	/**
	 * 广告列表
	 */
	function ad_list(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());

		$tpl = get_admin_smarty();

		$tab=intval($_GET['tab']);
		
		$adOb=new ad_model();
				
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		
		$ad_list_nav=$adOb->show_list_nav($page);//显示列表内容
		
		$ad_count_nav=$adOb->show_count_nav();//分页的总条数
		
		$t_nav = new Page(10, $ad_count_nav, $page, 5, "ad.php?p=");
		
		$page_nav=$t_nav->subPageCss2();//分页样式

		$ad_list_custom=$adOb->show_list_custom($page);//显示列表内容
		
		$ad_count_custom=$adOb->show_count_custom();//分页的总条数
		
		$t_custom = new Page(10, $ad_count_custom, $page, 5, "ad.php?p=");
		
		$page_custom=$t_custom->subPageCss2();//分页样式
		
		//模板传值
		$tpl->assign("page_nav",$page_nav);
		
		$tpl->assign('ad_list_nav',$ad_list_nav);

		$tpl->assign('permission',$permission);
		
		$tpl->assign('tab',$tab);
		
		$tpl->assign("page_custom",$page_custom);
		
		$tpl->assign('ad_list_custom',$ad_list_custom);
		
		$tpl->display('admin_ad_list.tpl.html');
	}
	
	/**
	 *添加导航广告
	 */
	function nav_add(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
		
		$tpl = get_admin_smarty();
		
		//模板传值
		$tpl->assign('permission',$permission);
		
		$tpl->display('admin_ad_nav_add.tpl.html');
	}
	
	/**
	 *添加导航广告成功
	 */
	function nav_add_save(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
		//检测上传图片是否存在
		if(isset($_FILES)){
			//实例化UploadFile类
			$uploadOb=new UploadFile();
			//指定允许的大小
			$uploadOb->maxSize=2000000;
			//指定允许的类型
			$uploadOb->allowType=array('image/jpeg','image/gif','image/png','image/jpg',);
			//指定保存路径
			$uploadOb->savePath='../pictures/ad_nav/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='394,192';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='118,59';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/ad_nav/upload/thumb/';
			//调用上传所有文件的方法upload
			$rs=$uploadOb->upload();
			//判断是否保存成功
			if ($rs) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				/* 
				 * 处理多张图片
				 * $_POST['ad_img']='';
				foreach ($arr as $v){
					$_POST['ad_img'].=$v['savename'].'|';
				}
				$_POST['ad_img']=substr($_POST['ad_img'],0,-1); */
				$_POST['ad_img']=$arr[0]['savename'];
			}else{
				$str=$uploadOb->getErrorMsg();
			}
		}
		$db=new db();
		$rs=$db->add('xgj_ad_nav', $_POST);
		if ($rs) {
			$message='广告添加成功，正在跳转...';
			echo jump(1,$message,'ad.php?tab=1');
			header("refresh:2;url='ad.php?tab=1'" );
		}else{
			$message='广告添加失败，正在跳转...';
			echo jump(2,$message,'ad.php?tab=1');
			header("refresh:2;url='ad.php?tab=1'" );
		}
	}
	
	/**
	 * 修改广告
	 */
	function nav_edit(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
		$ad_id=$_GET['ad_id'];
		$tpl = get_admin_smarty();
		$adOb=new ad_model();
		$ad=$adOb->getNavAdById($ad_id);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('ad',$ad);
		$tpl->display('admin_ad_nav_edit.tpl.html');
	}
	
	/**
	 * 修改广告成功
	 */
	function nav_edit_save(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
		$db=new db();
		$ad_id=$_GET['ad_id'];
		//检测上传图片是否存在
		if(isset($_FILES)){
			//实例化UploadFile类
			$uploadOb=new UploadFile();
			//指定允许的大小
			$uploadOb->maxSize=2000000;
			//指定允许的类型
			$uploadOb->allowType=array('image/jpeg','image/jpg','image/gif','image/png');
			//指定保存路径
			$uploadOb->savePath='../pictures/ad_nav/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='394,192';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='118,59';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/ad_nav/upload/thumb/';
			//调用上传所有文件的方法upload
			$result=$uploadOb->upload();
			//判断是否保存成功
			if ($result) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				/*
				 *  处理多张图片
				 * 
				 *  $newName='';
				foreach ($arr as $v){
					$newName.=$v['savename'].'|';
				}
				$imgName=substr($newName,0,-1); */
				$imgName=$arr[0]['savename'];
				$re=$db->update('xgj_ad_nav', array('ad_img'=>$imgName),"ad_id=$ad_id");
				//判断成功就删除旧图片
				if ($re) {
					$oldName=$_POST['oldimg'];
					/* 
					 * 处理多张图片
					 * $imgArr=explode('|',$oldName);
					foreach ($imgArr as $v){
						@unlink(WWW_DIR."/pictures/ad_nav/upload/$v");
						@unlink(WWW_DIR."/pictures/ad_nav/upload/thumb/thumb_$v");
						@unlink(WWW_DIR."/pictures/ad_nav/upload/thumb/s_$v");
					} */
					// 删除单张图片
					@unlink(WWW_DIR."/pictures/ad_nav/upload/$oldName");
					@unlink(WWW_DIR."/pictures/ad_nav/upload/thumb/thumb_$oldName");
					@unlink(WWW_DIR."/pictures/ad_nav/upload/thumb/s_$oldName");
				}
			}else{
				$str=$uploadOb->getErrorMsg();
			}
		}
		$data=array(
				'ad_name'=>trim($_POST['ad_name']),
				'ad_url'=>trim($_POST['ad_url']),
				'sort_order'=>intval($_POST['sort_order']),
				'ad_type'=>trim($_POST['ad_type']),
		);
		$rs=$db->update('xgj_ad_nav',$data,"ad_id=$ad_id");
		if (empty($re) || $rs || $re) {
			$message='广告修改成功，正在跳转...';
			echo jump(1,$message,'ad.php?tab=1');
			header("refresh:2;url='ad.php?tab=1'" );
		}else{
			$message='广告修改失败，正在跳转...';
			echo jump(2,$message,"ad.php?edit_nav&ad_id=$ad_id");
			header("refresh:2;url='ad.php?edit_nav&ad_id=$ad_id'" );
		}
	}
	
	/**
	 * 删除导航广告
	 */
	function nav_del(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
		$ad_id=$_GET['ad_id'];
		$tpl = get_admin_smarty();
		$adOb=new ad_model();
		//模板传值
		$ad=$adOb->getNavAdById($ad_id);
		//$imgArr=explode('|',$ad['ad_img']);
		$imgStr=$ad['ad_img'];
		if (!empty($imgStr)) {
			/* 
			 * 处理多张图片
			 * foreach ($imgArr as $v){
				@unlink(WWW_DIR."/pictures/ad_nav/upload/$v");
				@unlink(WWW_DIR."/pictures/ad_nav/upload/thumb/thumb_$v");
				@unlink(WWW_DIR."/pictures/ad_nav/upload/thumb/s_$v");
			} */
			// 删除单张图片
			@unlink(WWW_DIR."/pictures/ad_nav/upload/$imgStr");
			@unlink(WWW_DIR."/pictures/ad_nav/upload/thumb/thumb_$imgStr");
			@unlink(WWW_DIR."/pictures/ad_nav/upload/thumb/s_$imgStr");
			$rs=$adOb->del_nav_ad_id($ad_id);
			if($rs){
				$message='删除广告与图片成功，正在跳转...';
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='删除广告与图片失败，正在跳转...';
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		
		}else{
			$rs=$adOb->del_nav_ad_id($ad_id);
			if($rs){
				$message='删除广告与图片成功，正在跳转...';
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='删除广告与图片失败，正在跳转...';
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		}
	}
	
	/**
	 *添加自定义广告
	 */
	function custom_add(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
	
		$tpl = get_admin_smarty();
		
		//模板传值
		$tpl->assign('permission',$permission);
		
		$tpl->display('admin_ad_custom_add.tpl.html');
	}
	
	/**
	 *添加自定义广告成功
	 */
	function custom_add_save(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
		//检测上传图片是否存在
		if(isset($_FILES)){
			//实例化UploadFile类
			$uploadOb=new UploadFile();
			//指定允许的大小
			$uploadOb->maxSize=2000000;
			//指定允许的类型
			$uploadOb->allowType=array('image/jpeg','image/gif','image/png','image/jpg',);
			//指定保存路径
			$uploadOb->savePath='../pictures/ad_custom/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='394,192';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='118,59';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/ad_custom/upload/thumb/';
			//调用上传所有文件的方法upload
			$rs=$uploadOb->upload();
			//判断是否保存成功
			if ($rs) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				/* 
				 * 处理多图
				 * $_POST['ad_img']='';
				foreach ($arr as $v){
					$_POST['ad_img'].=$v['savename'].'|';
				}
				$_POST['ad_img']=substr($_POST['ad_img'],0,-1); */
				$_POST['ad_img']=$arr[0]['savename'];
			}else{
				$str=$uploadOb->getErrorMsg();
			}
		}
		$db=new db();
		$rs=$db->add('xgj_ad_custom', $_POST);
		if ($rs) {
			$message='广告添加成功，正在跳转...';
			echo jump(1,$message,'ad.php?tab=2');
			header("refresh:2;url='ad.php?tab=2'" );
		}else{
			$message='广告添加失败，正在跳转...';
			echo jump(2,$message,'ad.php?tab=2');
			header("refresh:2;url='ad.php?tab=2'" );
		}
	}
	
	/**
	 * 修改自定义广告
	 */
	function custom_edit(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
		$ad_id=$_GET['ad_id'];
		$tpl = get_admin_smarty();
		$adOb=new ad_model();
		$ad=$adOb->getCustomAdById($ad_id);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('ad',$ad);
		$tpl->display('admin_ad_custom_edit.tpl.html');
	}
	
	/**
	 * 修改自定义广告成功
	 */
	function custom_edit_save(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
		$db=new db();
		$ad_id=$_GET['ad_id'];
		//检测上传图片是否存在
		if(isset($_FILES)){
			//实例化UploadFile类
			$uploadOb=new UploadFile();
			//指定允许的大小
			$uploadOb->maxSize=2000000;
			//指定允许的类型
			$uploadOb->allowType=array('image/jpeg','image/jpg','image/gif','image/png');
			//指定保存路径
			$uploadOb->savePath='../pictures/ad_custom/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='394,192';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='118,59';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/ad_custom/upload/thumb/';
			//调用上传所有文件的方法upload
			$result=$uploadOb->upload();
			//判断是否保存成功
			if ($result) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				/* $newName='';
				foreach ($arr as $v){
					$newName.=$v['savename'].'|';
				}
				$imgName=substr($newName,0,-1); */
				$imgName=$arr[0]['savename'];
				$re=$db->update('xgj_ad_custom', array('ad_img'=>$imgName),"ad_id=$ad_id");
				//判断成功就删除旧图片
				if ($re) {
					$oldName=$_POST['oldimg'];
					/* $imgArr=explode('|',$oldName);
					foreach ($imgArr as $v){
						@unlink(WWW_DIR."/pictures/ad_custom/upload/$v");
						@unlink(WWW_DIR."/pictures/ad_custom/upload/thumb/thumb_$v");
						@unlink(WWW_DIR."/pictures/ad_custom/upload/thumb/s_$v");
					} */
					// 删除单张图片
					@unlink(WWW_DIR."/pictures/ad_nav/upload/$oldName");
					@unlink(WWW_DIR."/pictures/ad_nav/upload/thumb/thumb_$oldName");
					@unlink(WWW_DIR."/pictures/ad_nav/upload/thumb/s_$oldName");
				}
			}else{
				$str=$uploadOb->getErrorMsg();
			}
		}
		$data=array(
				'ad_name'=>trim($_POST['ad_name']),
				'ad_url'=>trim($_POST['ad_url']),
				'ad_type'=>trim($_POST['ad_type']),
				'sort_order'=>intval($_POST['sort_order']),
				'position'=>trim($_POST['position']),
		);
		$rs=$db->update('xgj_ad_custom',$data,"ad_id=$ad_id");
		if (empty($re) || $rs || $re) {
			$message='广告修改成功，正在跳转...';
			echo jump(1,$message,'ad.php?tab=2');
			header("refresh:2;url='ad.php?tab=2'" );
		}else{
			$message='广告修改失败，正在跳转...';
			echo jump(2,$message,"ad.php?edit_custom&ad_id=$ad_id");
			header("refresh:2;url='ad.php?edit_custom&ad_id=$ad_id'" );
		}
	}
	
	/**
	 * 删除自定义广告
	 */
	function custom_del(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
		$ad_id=$_GET['ad_id'];
		$tpl = get_admin_smarty();
		$adOb=new ad_model();
		//模板传值
		$ad=$adOb->getCustomAdById($ad_id);
		//$imgArr=explode('|',$ad['ad_img']);
		$imgStr=$ad['ad_img'];
		if (!empty($imgStr)) {
			/* 
			 * 处理多张图片
			 * foreach ($imgArr as $v){
				@unlink(WWW_DIR."/pictures/ad_custom/upload/$v");
				@unlink(WWW_DIR."/pictures/ad_custom/upload/thumb/thumb_$v");
				@unlink(WWW_DIR."/pictures/ad_custom/upload/thumb/s_$v");
			} */
			// 删除单张图片
			@unlink(WWW_DIR."/pictures/ad_custom/upload/$imgStr");
			@unlink(WWW_DIR."/pictures/ad_custom/upload/thumb/thumb_$imgStr");
			@unlink(WWW_DIR."/pictures/ad_custom/upload/thumb/s_$imgStr");
			$rs=$adOb->del_custom_ad_id($ad_id);
			if($rs){
				$message='删除广告与图片成功，正在跳转...';
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='删除广告与图片失败，正在跳转...';
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
	
		}else{
			$rs=$adOb->del_custom_ad_id($ad_id);
			if($rs){
				$message='删除广告与图片成功，正在跳转...';
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='删除广告与图片失败，正在跳转...';
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		}
	}
}