<?php
require_once(WWW_DIR."/admin/model/video_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
class video
{
	/**
	 * 视频列表
	 */
	function video_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化文章管理model类
		$videoOb=new video_model();
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		//显示列表内容
		$video_list=$videoOb->show_list($page);
		//分页的总条数
		$video_count=$videoOb->show_count();
		//实例化分页类
		$t = new Page(10, $video_count, $page, 5, "video.php?p=");
		//分页样式
		$page=$t->subPageCss2();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign("page",$page);
		$tpl->assign('video_list',$video_list);
		//显示模板
		$tpl->display('admin_video_list.tpl.html');
	}
	
	/**
	 *添加视频
	 */
	function video_add(){
		$permission=intval(admin_check_logon());
		
		$tpl = get_admin_smarty();
		//模板传值
		$tpl->assign('permission',$permission);
		
		$tpl->display('admin_video_add.tpl.html');
	}
	
	/**
	 *添加视频成功
	 */
	function video_add_save(){
		$permission=intval(admin_check_logon());
		//var_dump($_FILES['v_video']);exit;
		//检测上传图片是否存在
		if(isset($_FILES)){
			//实例化UploadFile类
			$uploadOb=new UploadFile();
			//指定允许的大小
			$uploadOb->maxSize=20000000;
			//指定允许的类型
			$uploadOb->allowType=array('mp4','image/jpeg','image/gif','image/png','image/jpg',);
			//指定保存路径
			$uploadOb->savePath='../pictures/video/upload/';
		    // 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='602,230,50';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='144,137,50';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_,ss_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/video/upload/thumb/'; 
			//调用上传所有文件的方法upload
			$rs=$uploadOb->upload();
			//判断是否保存成功
			if ($rs) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				//var_dump($arr);
				$_POST['v_video']=$arr[1]['savename'];
				$_POST['v_img']=$arr[0]['savename'];
			}else{
				$str=$uploadOb->getErrorMsg();
				//var_dump($str);
			}
		}
		$db=new db();
		$rs=$db->add('xgj_video', $_POST);
		if ($rs) {
			$message='视频和图片添加成功，正在跳转...';
			echo jump(1,$message,'video.php');
			header("refresh:2;url='video.php'" );
		}else{
			$message='视频和图片添加失败，正在跳转...';
			echo jump(2,$message,'video.php');
			header("refresh:2;url='video.php'" );
		}
	}
	
	/**
	 * 修改视频
	 */
	function video_edit(){
		$permission=intval(admin_check_logon());
		$v_id=$_GET['v_id'];
		$tpl = get_admin_smarty();
		$videoOb=new video_model();
		$video=$videoOb->getVideoById($v_id);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('video',$video);
		$tpl->display('admin_video_edit.tpl.html');
	}
	
	/**
	 * 修改视频成功
	 */
	function video_edit_save(){
		$permission=intval(admin_check_logon());
		$db=new db();
		$v_id=$_GET['v_id'];
		//检测上传图片是否存在
		if(isset($_FILES)){
			//实例化UploadFile类
			$uploadOb=new UploadFile();
			//指定允许的大小
			$uploadOb->maxSize=20000000;
			//指定允许的类型
			$uploadOb->allowType=array('mp4','image/jpeg','image/jpg','image/gif','image/png');
			//指定保存路径
			$uploadOb->savePath='../pictures/video/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='602,230,50';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='144,137,50';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_,ss_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/video/upload/thumb/';
			//调用上传所有文件的方法upload
			$result=$uploadOb->upload();
			//判断是否保存成功
			if ($result) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				$imgName=$arr[0]['savename'];
				$videoName=$arr[1]['savename'];
				$re=$db->update('xgj_video', array('v_img'=>$imgName,'v_video'=>$videoName),"v_id=$v_id");
				//判断成功就删除旧图片
				if ($re) {
					$oldName=$_POST['oldimg'];
					$oldVideo=$_POST['video'];
					// 删除单张图片
					@unlink(WWW_DIR."/pictures/video/upload/$oldVideo");
					@unlink(WWW_DIR."/pictures/video/upload/$oldName");
					@unlink(WWW_DIR."/pictures/video/upload/thumb/thumb_$oldName");
					@unlink(WWW_DIR."/pictures/video/upload/thumb/s_$oldName");
					@unlink(WWW_DIR."/pictures/video/upload/thumb/ss_$oldName");
				}
			}else{
				$str=$uploadOb->getErrorMsg();
			}
		}
		$data=array(
				'v_name'=>trim($_POST['v_name']),
				'sort_order'=>intval($_POST['sort_order']),
				'v_type'=>trim($_POST['v_type']),
		);
		$rs=$db->update('xgj_video',$data,"v_id=$v_id");
		if (empty($re) || $rs || $re) {
			$message='视频与图片修改成功，正在跳转...';
			echo jump(1,$message,'video.php');
			header("refresh:2;url='video.php'" );
		}else{
			$message='视频与图片修改失败，正在跳转...';
			echo jump(2,$message,"video.php?edit&v_id=$v_id");
			header("refresh:2;url='video.php?edit&v_id=$v_id'" );
		}
	}
	
	/**
	 * 删除视频
	 */
	function video_del(){
		$permission=intval(admin_check_logon());
		$v_id=$_GET['v_id'];
		$videoOb=new video_model();
		//模板传值
		$video=$videoOb->getVideoById($v_id);
		//$imgArr=explode('|',$ad['v_img']);
		$imgStr=$video['v_img'];
		$videoStr=$video['v_video'];
		if (!empty($imgStr) && !empty($videoStr)) {
			// 删除单张图片
			@unlink(WWW_DIR."/pictures/video/upload/$videoStr");
			@unlink(WWW_DIR."/pictures/video/upload/$imgStr");
			@unlink(WWW_DIR."/pictures/video/upload/thumb/thumb_$imgStr");
			@unlink(WWW_DIR."/pictures/video/upload/thumb/s_$imgStr");
			@unlink(WWW_DIR."/pictures/video/upload/thumb/ss_$imgStr");
			$rs=$videoOb->del_v_id($v_id);
			if($rs){
				$message='删除视频与图片成功，正在跳转...';
				//输出提示页面
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='删除视频与图片失败，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		
		}else{
			$rs=$videoOb->del_v_id($v_id);
			if($rs){
				$message='删除视频与图片成功，正在跳转...';
				//输出提示页面
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='删除视频与图片失败，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		}
	}
}