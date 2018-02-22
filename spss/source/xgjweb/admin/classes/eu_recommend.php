<?php
require_once(WWW_DIR."/admin/model/eu_recommend_model.php");
require_once(WWW_DIR."/libs/page.php");
class eu_recommend{
	function eu_recommend_list(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//调用smarty
		$tpl = get_admin_smarty();
		//实例化数据库操作类
		$eu_recommendOb=new eu_recommend_model();
		//推荐列表
		$eu_recommend_list=$eu_recommendOb->get_en_recommend_goods();
		//商品列表
		$eu_goods=$eu_recommendOb->get_eu_goods();
		//模板传值
		$tpl->assign("eu_goods",$eu_goods);
		$tpl->assign('permission',$permission);
		$tpl->assign('eu_recommend_list',$eu_recommend_list);
		//显示模板
		$tpl->display('admin_eu_recommend_list.tpl.html');
	}
	public function recommend_list_add(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$eu_recommendOb=new eu_recommend_model();
		$db=new db();
		//实例化smarty类
		$tpl = get_admin_smarty();
		//获取指定值
		$goods_id=intval($_GET['goods_id']);
		//判断是否有相同项
		if($eu_recommendOb->check_identical($goods_id)){
			echo "<script>alert('操作失败：已存在相同项')</script>";
			exit;
		}
		//获取数据源
		$data=array(
				'goods_id'=>$goods_id,
		);
		//添加一条记录
		$recommend_id=$db->add('xgj_eu_recommend', $data);
		//清除之前的输出
		ob_start();
		//获取一条信息
		$item=$eu_recommendOb->eu_recommend_id($recommend_id,$goods_id);
		if ($item['is_putaway']==0){
			$is_putaway='否';
		}else if($item['is_putaway']==1){
			$is_putaway='是';
		}
		if ($item['is_panic_buy']==0){
			$is_panic_buy='否';
		}else if($item['is_panic_buy']==1){
			$is_panic_buy='是';
		}
		$promote_start_date=date('Y-m-d H:i:s',$item['promote_start_date']);
		$promote_end_date=date('Y-m-d H:i:s',$item['promote_end_date']);
		echo "<tr id='recommend_list_tr_{$item['recommend_id']}'>
		<td>{$item['recommend_id']}</td>
		<td>{$item['goods_name']}</td>
		<td>{$item['goods_sn']}</td>
		<td>{$item['shop_price']}元</td>
		<td>{$is_putaway}</td>
		<td>{$is_panic_buy}</td>
		<td>{$promote_start_date}</td>
		<td>{$promote_end_date}</td>
		<td><input type='button' value='移除' onclick='remove_list({$item['recommend_id']})'></td>
		</tr>";
		ob_get_flush();
	}
	
	/**
	 * 删除子清单中的一条记录
	 */
	function recommend_list_del(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());
		//实例化数据库操作类
		$eu_recommendOb=new eu_recommend_model();
		//获取指定值
		$recommend_id=intval($_GET['recommend_id']);
		//删除一条记录
		$re=$eu_recommendOb->del_eu_recommend_id($recommend_id);
		//返回值
		if($re){
			echo 'success';
		}
	}
}
