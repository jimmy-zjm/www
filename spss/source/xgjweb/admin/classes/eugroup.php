<?php 
require_once(WWW_DIR."/admin/model/eugroup_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
class eugroup
{
	/**
	 * 欧洲团代购产品清单
	 */
	function eugroup_goods(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		
		$tpl = get_admin_smarty();
		
		$eugroupOb=new eugroup_model();
				
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		
		$goods_list=$eugroupOb->show_list($page);//显示列表内容
		
		$goods_count=$eugroupOb->show_count();//分页的总条数
		
		$t = new Page(10, $goods_count, $page, 5, "eugroup.php?p=");
		
		$page=$t->subPageCss2();//分页样式
		
		//模板传值
		$tpl->assign('permission',$permission);
		
		$tpl->assign("page",$page);
		
		$tpl->assign('goods_list',$goods_list);
		
		$tpl->display('admin_eugroup_goods.tpl.html');
	}
	
	/**
	 * 添加，欧洲团代购产品清单
	 */
	function eugroup_goods_add(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());

		$tpl = get_admin_smarty();
		
		$euOb=new eugroup_model();
		
		$fcate=$euOb->getCateByPid("parent_id = 0");
		
		$scate=$euOb->getCateByPid("parent_id != 0");
		
		//模板传值
		$tpl->assign('permission',$permission);
		
		$tpl->assign('fcate',$fcate);
		
		$tpl->assign('scate',$scate);
	
		$tpl->display('admin_eugroup_goods_add.tpl.html');
	}
	
	/**
	 * 添加数据库，欧洲团代购产品清单
	 */
	function eugroup_goods_add_save(){
		//判断后台是否登录并返回权限
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
			$uploadOb->savePath='../pictures/eugroup/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='200,50';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='200,50';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/eugroup/upload/thumb/';
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
		
		$db=new db();
		
		$rs=$db->add('xgj_eu_goods', $_POST);
		if ($rs) {
			$message='产品添加成功，正在跳转...';
			echo jump(1,$message,'eugroup.php');
			header("refresh:2;url='eugroup.php'" );
		}else{
			$message='产品添加失败，正在跳转...';
			echo jump(2,$message,'eugroup.php');
			header("refresh:2;url='eugroup.php'" );
		}
	}
	
	/**
	 * 更新，欧洲团代购产品清单
	 */
	function eugroup_goods_edit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		$goods_id=$_GET['goods_id'];
		$tpl = get_admin_smarty();	
		$eugroupOb=new eugroup_model();
		$goods=$eugroupOb->eugroup_goods_id($goods_id);
		
		$fcate=$eugroupOb->getCateByPid("parent_id = 0");
		
		$scate=$eugroupOb->getCateByPid("parent_id != 0");
		
		//模板传值
		$tpl->assign('permission',$permission);
		
		$tpl->assign('fcate',$fcate);
		
		$tpl->assign('scate',$scate);
		 		
		$tpl->assign('goods',$goods);
		
		$tpl->display('admin_eugroup_goods_edit.tpl.html');
	}
	
	/**
	 * 编辑数据库，欧洲团代购产品清单
	 */
	function eugroup_goods_edit_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		
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
			$uploadOb->savePath='../pictures/eugroup/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='200,50';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='200,50';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/eugroup/upload/thumb/';
			//调用上传所有文件的方法upload
			$result=$uploadOb->upload();
			//判断是否保存成功
			if ($result) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
// 				var_dump($arr);exit;
				$newName='';
				foreach ($arr as $v){
					$newName.=$v['savename'].'|';
				}
				// 		var_dump($_POST);exit;
				$imgName=substr($newName,0,-1);
				$re=$db->update('xgj_eu_good', array('goods_img'=>$imgName),"goods_id=$goods_id");
				//判断成功就删除旧图片
				if ($re) {
					$oldName=$_POST['oldimg'];
					$imgArr=explode('|',$oldName);
					foreach ($imgArr as $v){
						@unlink(WWW_DIR."/pictures/eugroup/upload/$v");
						@unlink(WWW_DIR."/pictures/eugroup/upload/thumb/thumb_$v");
						@unlink(WWW_DIR."/pictures/eugroup/upload/thumb/s_$v");
					}
				}
			}else{
				$str=$uploadOb->getErrorMsg();
			}
		}	

		$data=array(
				'promote_start_date'=>strtotime($_POST['promote_start_date']),
				'promote_end_date'=>strtotime($_POST['promote_end_date']),
				'goods_name'=>trim($_POST['goods_name']),
				'goods_sn'=>trim($_POST['goods_sn']),
				'keywords'=>trim($_POST['keywords']),
				'sex'=>trim($_POST['sex']),
				'classes'=>trim($_POST['classes']),
				'brand'=>trim($_POST['brand']),
				'market_price'=>floatval($_POST['market_price']),
				'shop_price'=>floatval($_POST['shop_price']),
				'goods_desc'=>html_filter($_POST['goods_desc']),
				'specification'=>html_filter($_POST['specification']),
				'goods_number'=>intval($_POST['goods_number']),
				'is_putaway'=>intval($_POST['is_putaway']),
				'is_panic_buy'=>intval($_POST['is_panic_buy']),
		);

		$rs=$db->update('xgj_eu_good',$data,"goods_id=$goods_id");

		if ($rs || @$re) {
			//var_dump($_SERVER);exit;
			$message='产品修改成功，正在跳转...';
			
			echo jump(1,$message,'eugroup.php');
			
			header("refresh:2;url='eugroup.php'" );
		}else{
			$message='产品修改失败，正在跳转...';
			
			echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
			
			header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
		}
	}
	
	/**
	 * 删除，欧洲团代购产品清单
	 */
	function eugroup_goods_del(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		$goods_id=$_GET['goods_id'];
		$eugroupOb=new eugroup_model();
		$goods=$eugroupOb->eugroup_goods_id($goods_id);
		$imgArr=explode('|',$goods['goods_img']);
		if (!empty($imgArr)) {
			foreach ($imgArr as $v){
				@unlink(WWW_DIR."/pictures/eugroup/upload/$v");
				@unlink(WWW_DIR."/pictures/eugroup/upload/thumb/thumb_$v");
				@unlink(WWW_DIR."/pictures/eugroup/upload/thumb/s_$v");
			}
			$rs=$eugroupOb->del_eugroup_goods_id($goods_id);
			if($rs){
				$message='删除产品与图片成功，正在跳转...';
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='删除产品与图片失败，正在跳转...';
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}

		}else{
			$rs=$eugroupOb->del_eugroup_goods_id($goods_id);
			if($rs){
				$message='删除产品与图片成功，正在跳转...';
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				$message='删除产品与图片失败，正在跳转...';
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}
		}
	}

}