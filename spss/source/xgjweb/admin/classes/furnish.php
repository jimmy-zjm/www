<?php 
require_once(WWW_DIR."/admin/model/furnish_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
class furnish
{
	/**
	 * 健康舒适家居产品清单
	 */
	function furnish_goods(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化数据库操作类
		$furnishOb=new furnish_model();
		//$permission=$_GET['permission'];
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		//显示列表内容
		$goods_list=$furnishOb->show_list($page);
		//分页的总条数
		$goods_count=$furnishOb->show_count();
		//实例化分页类
		$t = new Page(10, $goods_count, $page, 5, "furnish.php?p=");
		//分页样式
		$page=$t->subPageCss2();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign("page",$page);
		$tpl->assign('goods_list',$goods_list);
		//显示模板
		$tpl->display('admin_furnish_goods.tpl.html');
	}
	
	/**
	 * 添加，健康舒适家居产品清单
	 */
	function furnish_goods_add(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//模板传值
		$tpl->assign('permission',$permission);
		//显示模板
		$tpl->display('admin_furnish_goods_add.tpl.html');
	}
	
	/**
	 * 添加数据库，健康舒适家居产品清单
	 */
	function furnish_goods_add_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
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
			$uploadOb->savePath='../pictures/furnish/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='200,50';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='200,50';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/furnish/upload/thumb/';
			//调用上传所有文件的方法upload
			$rs=$uploadOb->upload();
			//判断是否保存成功
			if ($rs) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				$_POST['goods_img']='';
				foreach ($arr as $v){
					$_POST['goods_img'].=$v['savename'].'|';
				}
				$_POST['goods_img']=substr($_POST['goods_img'],0,-1);
			}else{
				$str=$uploadOb->getErrorMsg();
			}
		}
		//执行并获取结果
		$rs=$db->add('xgj_furnish_goods', $_POST);
		//判断并跳转提示
		if ($rs) {
			//提示信息
			$message='产品添加成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'furnish.php');
			//跳转地址
			header("refresh:2;url='furnish.php'" );
		}else{
			//提示信息
			$message='产品添加失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,'furnish.php');
			//跳转地址
			header("refresh:2;url='furnish.php'" );
		}
	}
	
	/**
	 * 更新，健康舒适家居产品清单
	 */
	function furnish_goods_edit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$furnishOb=new furnish_model();
		//实例化smarty类
		$tpl = get_admin_smarty();
		//获取指定值
		$goods_id=$_GET['goods_id'];
		//查询一条记录
		$goods=$furnishOb->furnish_goods_id($goods_id);
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('goods',$goods);
		//显示模板
		$tpl->display('admin_furnish_goods_edit.tpl.html');
	}
	
	/**
	 * 编辑数据库，健康舒适家居产品清单
	 */
	function furnish_goods_edit_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$db=new db();
		$goods_id=$_GET['goods_id'];
		//检测上传图片是否存在
		//检测上传图片是否存在
		if(isset($_FILES)){
			//实例化UploadFile类
			$uploadOb=new UploadFile();
			//指定允许的大小
			$uploadOb->maxSize=2000000;
			//指定允许的类型
			$uploadOb->allowType=array('image/jpeg','image/jpg','image/gif','image/png');
			//指定保存路径
			$uploadOb->savePath='../pictures/furnish/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='200,50';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='200,50';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/furnish/upload/thumb/';
			//调用上传所有文件的方法upload
			$result=$uploadOb->upload();
			//判断是否保存成功
			if ($result) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				$newName='';
				foreach ($arr as $v){
					$newName.=$v['savename'].'|';
				}
				$imgName=substr($newName,0,-1);
				$re=$db->update('xgj_furnish_goods', array('goods_img'=>$imgName),"goods_id=$goods_id");
				//判断成功就删除旧图片
				if ($re) {
					$oldName=$_POST['oldimg'];
					$imgArr=explode('|',$oldName);
					foreach ($imgArr as $v){
						@unlink(WWW_DIR."/pictures/furnish/upload/$v");
						@unlink(WWW_DIR."/pictures/furnish/upload/thumb/thumb_$v");
						@unlink(WWW_DIR."/pictures/furnish/upload/thumb/s_$v");
					}
				}
			}else{
				$str=$uploadOb->getErrorMsg();
			}
		}	
		//数据源
		$data=array(
				'goods_name'=>trim($_POST['goods_name']),
				'goods_sn'=>trim($_POST['goods_sn']),
				'goods_mnemonic'=>trim($_POST['goods_mnemonic']),
				'goods_model'=>trim($_POST['goods_model']),
				'goods_unit'=>trim($_POST['goods_unit']),
				'shop_price'=>floatval($_POST['shop_price']),
				'keywords'=>trim($_POST['keywords']),
				'goods_desc'=>html_filter($_POST['goods_desc']),
				'specification'=>html_filter($_POST['specification']),
				'goods_number'=>intval($_POST['goods_number']),
				'starttime'=>strtotime($_POST['starttime']),
				'endtime'=>strtotime($_POST['endtime']),
				'is_putaway'=>intval($_POST['is_putaway']),
		);
		//更新一条记录
		$rs=$db->update('xgj_furnish_goods',$data,"goods_id=$goods_id");

		if (empty($re) || $rs || $re) {
			$message='产品修改成功，正在跳转...';
			
			echo jump(1,$message,'furnish.php');
			
			header("refresh:2;url='furnish.php'" );
		}else{
			$message='产品修改失败，正在跳转...';
			
			echo jump(2,$message,"furnish.php?edit&goods_id=$goods_id");
			
			header("refresh:2;url='furnish.php?edit&goods_id=$goods_id'" );
		}
	}
	
	/**
	 * 删除，健康舒适家居产品清单
	 */
	function furnish_goods_del(){
		//判断后台是否登录并返回权限并返回权限
		$permission=intval(admin_check_logon());
		$goods_id=$_GET['goods_id'];
		$tpl = get_admin_smarty();
		$furnishOb=new furnish_model();
		$goods=$furnishOb->furnish_goods_id($goods_id);
		$imgArr=explode('|',$goods['goods_img']);
		if (!empty($imgArr)) {
			foreach ($imgArr as $v){
				@unlink(WWW_DIR."/pictures/furnish/upload/$v");
				@unlink(WWW_DIR."/pictures/furnish/upload/thumb/thumb_$v");
				@unlink(WWW_DIR."/pictures/furnish/upload/thumb/s_$v");
			}
			$rs=$furnishOb->del_furnish_goods_id($goods_id);
			if($rs){
				$message='删除产品与图片成功，正在跳转...';
				//输出提示页面
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='删除产品与图片失败，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}

		}else{
			$rs=$furnishOb->del_furnish_goods_id($goods_id);
			if($rs){
				$message='删除产品与图片成功，正在跳转...';
				//输出提示页面
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='删除产品与图片失败，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		}
	}

}