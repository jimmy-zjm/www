<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
class furnish_model{
	/**
	 * 分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_list($page){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_furnish_goods order by goods_id desc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 *总条数
	 * @return string
	 */
	function show_count(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_furnish_goods";
		$result=$db->getOne($sql);
		return $result;
	}
	
    /**
     * 产品材料列表
     * @return array
     */
    function furnish_goods_list(){
    	$db=new db();
    	
    	$sql="select * from xgj_furnish_goods limit 0,5";
    	
    	$result=$db->getAll($sql);
    	
    	return $result;
    }
    
    /**
     * 根据id查产品材料列表
     * @return array
     */
    function furnish_goods_id($goods_id){
    	$db=new db();
    	 
    	$sql="select * from xgj_furnish_goods where goods_id={$goods_id}";
    	 //var_dump($sql);
    	$result=$db->getRow($sql);
    	 
    	return $result;
    }
    
    /**
     * 根据id删除产品材料列表
     * @return array
     */
    function del_furnish_goods_id($goods_id){
    	$db=new db();
    	     	 
    	$result=$db->query("delete from xgj_furnish_goods where goods_id=$goods_id");
    	 
    	return $result;
    }
}