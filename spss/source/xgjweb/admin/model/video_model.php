<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 视频 
 */
class video_model{
	/**
	 * 视频列表分页
	 * @param unknown $page
	 * @return array
	 */
	function show_list($page){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_video order by v_id desc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 *视频列表总条数
	 * @return string
	 */
	function show_count(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_video";
		$result=$db->getOne($sql);
		return $result;
	}
	
	/**
	 *根据id查找视频信息
	 */
	function getVideoById($v_id){
		$db=new db();
		$sql = "SELECT * from xgj_video where v_id=$v_id";
		$result=$db->getRow($sql);
		return $result;
	}
	
	/**
	 *根据id删除视频信息
	 */
	function del_v_id($v_id){
		$db=new db();
		$sql = "delete from xgj_video where v_id=$v_id";
		$result=$db->query($sql);
		return $result;
	}

}