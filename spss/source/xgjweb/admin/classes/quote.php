<?php 
require_once(WWW_DIR."/admin/model/quote_model.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
/**
 * @author Administrator
 *
 */
class quote{

	function goods_sn(){

		$goods_sn = $_GET['goods_sn'];
		$child_id = $_GET['child_id'];
		$level = $_GET['level'];
		$quote_id = $_GET['quote_id'];

		$quoteOb = new quote_model();

		$list = $quoteOb->edit_list($goods_sn,$child_id);

		header("Location:quote.php?level=$level&quote_id=$quote_id&row=$row");exit;
	}
	/**
	 * 报价清单列表
	 */
	function quote_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化数据库操作类
		$quoteOb=new quote_model();
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		//显示列表内容
		$quote_list=$quoteOb->show_list($page);
		//分页的总条数
		$quote_count=$quoteOb->show_count();
		//实例化分页类
		$t = new Page(10, $quote_count, $page, 5, "quote.php?p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign("page",$page);
		$tpl->assign('quote_list',$quote_list);
		//显示模板
		$tpl->display('admin_furnish_quote_list.tpl.html');
	}
	
	/**
	 * 添加，报价清单
	 */
	function quote_add(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$quoteOb=new quote_model();
		//实例化smarty类
		$tpl = get_admin_smarty();
		//获取分类
		$cate=$quoteOb->furnish_cat_list();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('cate',$cate);
		//显示模板
		$tpl->display('admin_furnish_quote_add.tpl.html');
	}
	
	/**
	 * 添加数据库，报价清单
	 */
	function quote_add_save(){
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
			$uploadOb->savePath='../pictures/quote_sys/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='200,50';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='200,50';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/quote_sys/upload/thumb/';
			//调用上传所有文件的方法upload
			$rs=$uploadOb->upload();
			//判断是否保存成功
			if ($rs) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				$_POST['img']=$arr[0]['savename'];
			}else{
				//上传过程错误信息
				$str=$uploadOb->getErrorMsg();
			}
		}
		//执行并获取结果
		$rs=$db->add('xgj_furnish_quote', $_POST);
		//判断并跳转提示
		if ($rs) {
			//提示信息
			$message='添加成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'quote.php');
			//跳转地址
			header("refresh:2;url='quote.php'" );
		}else{
			//提示信息
			$message='添加失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,'quote.php');
			//跳转地址
			header("refresh:2;url='quote.php'" );
		}
	}
	
	/**
	 * 更新，报价清单
	 */
	function quote_edit(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$quoteOb=new quote_model();
		//实例化smarty类
		$tpl = get_admin_smarty();
		//获取指定值
		$quote_id=$_GET['quote_id'];
		//查询一条信息
		$quote=$quoteOb->getQuoteByid($quote_id);
		//获取分类
		$cate=$quoteOb->furnish_cat_list();
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('cate',$cate);
		$tpl->assign('quote',$quote);
		//显示模板
		$tpl->display('admin_furnish_quote_edit.tpl.html');
	}
	
	/**
	 * 编辑数据库，报价清单
	 */
	function quote_edit_save(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$db=new db();
		//获取指定值
		$quote_id=$_GET['quote_id'];
		//检测上传图片是否存在
		if(isset($_FILES)){
			//实例化UploadFile类
			$uploadOb=new UploadFile();
			//指定允许的大小
			$uploadOb->maxSize=2000000;
			//指定允许的类型
			$uploadOb->allowType=array('image/jpeg','image/jpg','image/gif','image/png');
			//指定保存路径
			$uploadOb->savePath='../pictures/quote_sys/upload/';
			// 使用对上传图片进行缩略图处理
			$uploadOb->thumb=true;
			// 缩略图最大宽度
			$uploadOb->thumbMaxWidth='200,50';
			// 缩略图最大高度
			$uploadOb->thumbMaxHeight='200,50';
			// 缩略图前缀
			$uploadOb->thumbPrefix='thumb_,s_';
			// 缩略图保存路径
			$uploadOb->thumbPath='../pictures/quote_sys/upload/thumb/';
			//调用上传所有文件的方法upload
			$result=$uploadOb->upload();
			//判断是否保存成功
			if ($result) {
				//获取上传图片的信息
				$arr=$uploadOb->getUploadFileInfo();
				$imgName=$arr[0]['savename'];
				$re=$db->update('xgj_furnish_quote', array('img'=>$imgName),"quote_id=$quote_id");
				//判断成功就删除旧图片
				if ($re) {
					$oldName=$_POST['oldimg'];
					// 删除单张图片
					@unlink(WWW_DIR."/pictures/quote_sys/upload/$oldName");
					@unlink(WWW_DIR."/pictures/quote_sys/upload/thumb/thumb_$oldName");
					@unlink(WWW_DIR."/pictures/quote_sys/upload/thumb/s_$oldName");
				}
			}else{
				//返回错误信息
				$str=$uploadOb->getErrorMsg();
			}
		}
		//数据源
		$data=array(
				'house_type'=>trim($_POST['house_type']),
				'house_area'=>trim($_POST['house_area']),
				'cat_id'=>trim($_POST['cat_id']),
				'price'=>floatval($_POST['price']),
				'sort_order'=>intval($_POST['sort_order']),
				'is_putaway'=>intval($_POST['is_putaway']),
				'quote_name'=>trim($_POST['quote_name']),
				'starttime'=>strtotime($_POST['starttime']),
				'endtime'=>strtotime($_POST['endtime']),
		);
		//执行并获取结果
		$rs=$db->update('xgj_furnish_quote',$data,"quote_id=$quote_id");
		//判断结果
		if (empty($re) || $rs || $re) {
			//提示信息
			$message='修改成功，正在跳转...';
			//输出提示页面
			echo jump(1,$message,'quote.php');
			//跳转地址
			header("refresh:2;url='quote.php'" );
		}else{
			//提示信息
			$message='修改失败，正在跳转...';
			//输出提示页面
			echo jump(2,$message,"quote.php?edit&quote_id=$quote_id");
			//跳转地址
			header("refresh:2;url='quote.php?edit&quote_id=$quote_id'" );
		}
	}
	
	/**
	 * 删除，报价清单
	 */
	function quote_del(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$quoteOb=new quote_model();
		//获取指定值
		$quote_id=$_GET['quote_id'];
		//根据id查询一条数据
		$quote=$quoteOb->getQuoteByid($quote_id);
		//获取图片名称
		$imgName=$quote['img'];
		//判断并删除图片
		if (!empty($imgName)) {
			// 删除单张图片
			@unlink(WWW_DIR."/pictures/quote_sys/upload/$imgName");
			@unlink(WWW_DIR."/pictures/quote_sys/upload/thumb/thumb_$imgName");
			@unlink(WWW_DIR."/pictures/quote_sys/upload/thumb/s_$imgName");
			$rs=$quoteOb->del_furnish_quote_id($quote_id);
			if($rs){
				//提示信息
				$message='删除成功，正在跳转...';
				//输出提示页面
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				//提示信息
				$message='删除失败，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}	
		}else{
			$rs=$quoteOb->del_furnish_quote_id($quote_id);
			if($rs){
				//提示信息
				$message='删除成功，正在跳转...';
				//输出提示页面
				echo jump(1,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}else{
				//提示信息
				$message='删除失败，正在跳转...';
				//输出提示页面
				echo jump(2,$message,"{$_SERVER['HTTP_REFERER']}");
				//跳转地址
				header("refresh:2;url='{$_SERVER['HTTP_REFERER']}'" );
			}	
		}
	}
	
	/**
	 * 报价商品配置候选清单列表
	 */
	function quote_ini_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$quoteOb=new quote_model();
		//实例化smarty类
		$tpl = get_admin_smarty();
		//搜索
		@$keyword=trim($_POST['keyword']);
		$condition="1=1";
		if(!empty($keyword)){
			$condition.=" and goods_name like '%$keyword%' ";
		}
		//获取指定值
		$quote_id=$_GET['quote_id'];
		$level=$_GET['level'];
		//获取清单列表
		$goods=$quoteOb->furnish_goods_list($condition);
		//获取系统名称
		$quote=$quoteOb->getQuoteByid($quote_id);
		//查询一条记录
		$child_list=$quoteOb->child_level_quote_id($quote_id, $level);	
		//模板传值
		$tpl->assign('permission',$permission);
		$tpl->assign('keyword',$keyword);
		$tpl->assign('quote_name',$quote['quote_name']);
		$tpl->assign('goods',$goods);
		$tpl->assign('child_list',$child_list);
		$tpl->assign('level',$level);
		$tpl->assign('quote_id',$quote_id);
		//显示模板
		$tpl->display('admin_furnish_quote_ini_list.tpl.html');
	}
	
	/**
	 * 添加子列表
	 */
	function child_list_add(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$quoteOb=new quote_model();
		$db=new db();
		//实例化smarty类
		$tpl = get_admin_smarty();
		//获取指定值
		$quote_id=intval($_GET['id']);
		$level=intval($_GET['level']);
		$chf_id=intval($_GET['chf_id']);
		$goods_id=intval($_GET['goods_id']);
		//判断是否有相同项
		if($quoteOb->check_identical($level, $chf_id, $quote_id,$goods_id)){
			echo "<script>alert('操作失败：已存在相同项')</script>";
			exit;
		}
		//获取一条记录
		$goods=$quoteOb->furnish_goods_id($goods_id);
		//获取商品货号
		$goods_sn=$goods['goods_sn'];
		//获取数据源
		$data=array(
				'goods_id'=>$goods_id,
				'level'=>$level,
				'chf_id'=>$chf_id,
				'quote_id'=>$quote_id,
				'goods_sn'=>$goods_sn
		);
		//添加一条记录
		$child_id=$db->add('xgj_quote_child_list', $data);
		//清楚之前的输出
		ob_start();
		//获取一条信息		
		$item=$quoteOb->child_goods_id($goods_id,$child_id);
		//输出一条信息内容
		echo "<tr id='child_list_tr_{$item['child_id']}'>
		<td>
		<input type='radio' name='goods_id' value='{$item['goods_id']}'/>
		{$item['goods_id']}
		</td>
		<td>{$item['goods_name']}</td>
		<td>{$item['goods_model']}</td>
		<td><select id='f_formula' name='f_formula' class='quinput'>
					<option value='' >公式</option>
					<option value='{$item['f_formula']}' >{$item['f_formula']}</option>	
			</select>
		</td>
		<td><select name='batch_{$item['child_id']}' class='bainput'>
					<option  value='0' >第一批</option>
					<option  value='1' >第二批</option>	
					<option  value='2' >第三批</option>
			</select>
		</td>
		<td>
			<form action='./quote.php' method='get'>
				<input class='quinput' type='text' name='goods_sn'>
				<input class='quinput' type='hidden' name='child_id' value='{$item['child_id']}'>
				<input class='quinput' type='hidden' name='level' value='{$item['level']}'>
				<input class='quinput' type='hidden' name='quote_id' value='{$item['quote_id']}'>
				<button>确认修改</button>

			</form>
		</td>
		<td>
		<input type='button' value='移除' onclick='remove_list({$item['child_id']})'>
		|
		<input type='button' value='修改规则' onclick='change_list({$item['child_id']})'>
		</td>
		</tr>";
		ob_get_flush();
	}
	
	/**
	 * 删除子清单中的一条记录
	 */
	function child_list_del(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$quoteOb=new quote_model();
		//获取指定值
		$child_id=intval($_GET['child_id']);
		//删除一条记录
		$re=$quoteOb->del_child_id($child_id);
		//返回值
		if($re){
			echo 'success';
		}
	}
	
	/**
	 * 修改子清单中的一条记录
	 */
	function child_list_change(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//获取指定值
		$child_id=intval($_GET['child_id']);
		//实例化数据库操作类
		$db=new db();
		//数据源
		$data=array(
			'minarea'=>	trim($_GET['minarea']),
			'maxarea'=>	trim($_GET['maxarea']),
			'batch'=>	trim($_GET['batch']),
			'formula'=>	trim($_GET['formula']),
		);
		//更新一条数据
		$re=$db->update('xgj_quote_child_list', $data, "child_id=$child_id");
		//判断并得到返回值
		if($re){
			echo 'success';
		}else{
			echo 'lose';
		}
	}
	
	function parent_formula(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$quoteOb=new quote_model();
		//获取指定值
		$quote_id=intval($_GET['id']);
		$level=intval($_GET['level']);
		//获取列表
		$list=$quoteOb->child_level_quote_id($quote_id,$level);
		echo"	<tr>
					<th width='60px'><input type='radio' name='goods_id' value='0'/>编号</th> 
					<th width='60px'>父编号</th>
					<th width='150px'>名称</th> <th>型号</th> <th>父类公式</th> <th>批次</th> <th>最小面积</th> <th>最大面积</th> <th>公式</th> <th>操作</th>
				</tr> ";
				
		foreach ($list as $item){
		echo "	
		<tr id='child_list_tr_{$item['child_id']}'>
		<td>
		<input type='radio' name='goods_id' value='{$item['goods_id']}'/>
		{$item['goods_id']}
		</td>
		<td>{$item['chf_id']}</td>
		<td>{$item['goods_name']}</td>
		<td>{$item['goods_model']}</td>
		<td><select id='f_formula' name='f_formula_{$item['child_id']}' class='quinput'>
								<option value='' >公式</option>
								<option value='{$item['f_formula']}' >{$item['f_formula']}</option>	
					</select></td>
		<td><select name='batch_{$item['child_id']}' class='bainput'>
			<option  value='0' ";if(isset($item['batch'])&&$item['batch']==0){echo "selected";} echo ">第一批</option>
			<option  value='1' ";if(isset($item['batch'])&&$item['batch']==1){echo "selected";} echo ">第二批</option>	
			<option  value='2' ";if(isset($item['batch'])&&$item['batch']==2){echo "selected";} echo ">第三批</option>
						</select>
					</td>
		<td><input class='minput' type='text' name='minarea_{$item['child_id']}'value='{$item['minarea']}'/></td>	
		<td><input class='minput' type='text' name='maxarea_{$item['child_id']}'value='{$item['maxarea']}'/></td>
		<td><input class='quinput' type='text' name='formula_{$item['child_id']}'value='{$item['formula']}'/></td>
		<td>
		<input type='button' value='移除' onclick='remove_list({$item['child_id']})'>
		|
		<input type='button' value='修改规则' onclick='change_list({$item['child_id']},{$item['level']},{$item['quote_id']})'>
		</td>
		</tr>";
		}
	}
	
}