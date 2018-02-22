<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 *广告 
 */
class ad_model{
	/**
	 * 导航广告列表分页
	 * @param unknown $page
	 * @return array
	 */
	function show_list_nav($page){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_ad_nav order by ad_id desc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 *导航广告列表总条数
	 * @return string
	 */
	function show_count_nav(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_ad_nav";
		$result=$db->getOne($sql);
		return $result;
	}
	
	/**
	 *根据id查找导航广告信息
	 */
	function getNavAdById($ad_id){
		$db=new db();
		$sql = "SELECT * from xgj_ad_nav where ad_id=$ad_id";
		$result=$db->getRow($sql);
		return $result;
	}
	
	/**
	 *根据id删除导航广告信息
	 */
	function del_nav_ad_id($ad_id){
		$db=new db();
		$sql = "delete from xgj_ad_nav where ad_id=$ad_id";
		$result=$db->query($sql);
		return $result;
	}
	
	/**
	 * 自定义广告分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_list_custom($page){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_ad_custom order by ad_id desc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 *自定义广告总条数
	 * @return string
	 */
	function show_count_custom(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_ad_custom";
		$result=$db->getOne($sql);
		return $result;
	}
	
	/**
	 *根据id查找自定义广告信息
	 */
	function getCustomAdById($ad_id){
		$db=new db();
		$sql = "SELECT * from xgj_ad_custom where ad_id=$ad_id";
		$result=$db->getRow($sql);
		return $result;
	}
	
	/**
	 *根据id删除自定义广告信息
	 */
	function del_custom_ad_id($ad_id){
		$db=new db();
		$sql = "delete from xgj_ad_custom where ad_id=$ad_id";
		$result=$db->query($sql);
		return $result;
	}
	
}