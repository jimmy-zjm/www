<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 服务商数据模型类
 */
class dealer_model{
	/**
	 * 分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_list($page,$condition){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_furnish_dealer where d_runstatus!=-1 $condition order by d_id desc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 *总条数
	 * @return string
	 */
	function show_count($condition){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_furnish_dealer where d_runstatus!=-1 $condition";
		$result=$db->getOne($sql);
		return $result;
	}

    /**
     * 根据id查询一条服务商信息
     * @return array
     */
    function dealer_d_id($d_id){
    	$db=new db();
    	 
    	$sql="select * from xgj_furnish_dealer where d_id={$d_id}";
    	
    	$result=$db->getRow($sql);
    	 
    	return $result;
    }
    
    /**
     * 根据id删除产品材料列表
     * @return array
     */
    function del_dealer_d_id($d_id){
    	$db=new db();
    	     	 
    	$result=$db->query("update xgj_furnish_dealer set d_runstatus=-1 where d_id=$d_id");
    	 
    	return $result;
    }
    
}