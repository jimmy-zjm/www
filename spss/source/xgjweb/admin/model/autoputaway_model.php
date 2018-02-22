<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 商品自动上下架 
 */
class autoputaway_model{
	/**
	 * 材料商品列表分页
	 * @param unknown $page
	 * @return array
	 */
	function show_list_goods($page){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_furnish_goods order by goods_id desc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 *材料商品列表总条数
	 * @return string
	 */
	function show_count_goods(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_furnish_goods";
		$result=$db->getOne($sql);
		return $result;
	}
	
	/**
	 *根据id查找材料商品信息
	 */
	function getGoodsById($goods_id){
		$db=new db();
		$sql = "SELECT * from xgj_furnish_goods where goods_id=$goods_id";
		$result=$db->getRow($sql);
		return $result;
	}
	
	/**
	 *根据id删除材料商品信息
	 */
	function del_goods_id($goods_id){
		$db=new db();
		$sql = "delete from xgj_furnish_goods where goods_id=$goods_id";
		$result=$db->query($sql);
		return $result;
	}
	
	/**
	 * 系统商品分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_list_quote($page){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_furnish_quote order by quote_id desc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 *系统商品总条数
	 * @return string
	 */
	function show_count_quote(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_furnish_quote";
		$result=$db->getOne($sql);
		return $result;
	}
	
	/**
	 *根据id查找系统商品信息
	 */
	function getQuoteById($quote_id){
		$db=new db();
		$sql = "SELECT * from xgj_furnish_quote where quote_id=$quote_id";
		$result=$db->getRow($sql);
		return $result;
	}
	
	/**
	 *根据id删除系统商品信息
	 */
	function del_quote_id($quote_id){
		$db=new db();
		$sql = "delete from xgj_furnish_quote where quote_id=$quote_id";
		$result=$db->query($sql);
		return $result;
	}
	
	/**
	 * 批量上架
	 * @param unknown $table
	 * @param unknown $field
	 * @param unknown $id
	 * @param unknown $where
	 * @return PDOStatement
	 */
	function batchUpdateTime($table,$field,$id,$where){
		$db=new db();
		$sql = "update {$table} set {$field} where {$id} in {$where}";
		//echo $sql;exit;
		$result=$db->query($sql);
		return $result;
	}
	
}