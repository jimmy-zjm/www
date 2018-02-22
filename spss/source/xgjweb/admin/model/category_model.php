<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 舒适家居数据模型类
 */
class category_model{
	/**
	 * 分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_list($page){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_furnish_cat order by cat_id asc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 * 总条数
	 * @return string
	 */
	function show_count(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_furnish_cat";
		$result=$db->getOne($sql);
		return $result;
	}

    /**
     * 根据id查询一条服务商信息
     * @return array
     */
    function category_cat_id($cat_id){
    	$db=new db();
    	 
    	$sql="select * from xgj_furnish_cat where cat_id={$cat_id}";
    	
    	$result=$db->getRow($sql);
    	 
    	return $result;
    }
    
    /**
     * 根据id删除产品材料列表
     * @return array
     */
    function del_category_cat_id($cat_id){
    	$db=new db();
    	     	 
    	$result=$db->query("delete from xgj_furnish_cat where cat_id=$cat_id");
    	 
    	return $result;
    }
    
}