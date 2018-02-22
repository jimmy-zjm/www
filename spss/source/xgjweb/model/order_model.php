<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");

class ordermodel{
	/**
	 * 分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_list($page){
		$db=new db();
		$start=($page-1)*12;
		$sql = "SELECT * FROM xgj_eu_comment limit ".$start.",12";
		$detail=$db->getAll($sql);
		return $detail;
	
	}
	
	function show_count(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_eu_comment";
		$result=$db->getOne($sql);
		return $result;
	
	}
	
	
	
	/**
	 * 根据goods_id查询商品详情
	 */
	function getGooddetailById($class_id,$id){
		$db=new db();
		$tableName=judge_table($class_id);
		$goods=$db->getRow("select `goods_id`,`goods_sn`,`goods_name`,`shop_price`,`promote_price`,`goods_img` from {$tableName} where goods_id = $id and class_id=$class_id");
		return $goods;
		
	}
	
	/**
	 * 根据goods_id评价列表
	 */
	function getCommentById($id){
		$db=new db();
		$rs=$db->getAll("select * from xgj_eu_comment where goods_id = $id");
		return $rs;
	}
	
	
	public function showAddress($userName){
		$db = new db();
		/*
		 * 查询用户名所对应的用户ID
		 * */
		//$sql = "select user_id from xgj_users where user_name='$userName'";
		$result = $db->getRow("select `user_id` from nyy.xgj_users where user_name = '$userName'");
		
		$userId = $result['user_id'];
		if (!$userId){
			echo 'userId为空';
		}
		/*
		 * 查询用户ID所管理的所有收货地址
		 * */
		$result = $db->getAll("select * from nyy.xgj_address where user_id = '$userId'");
		
	if($result){
			return $result;
		}else{
			return null;//查询失败
		}
		
		
	}	


}