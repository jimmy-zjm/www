<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 优惠券类型数据模型类
 */
class coupon_model{
	protected $db;
	public function __construct(){
		$db=new db();
		$this->db=$db;
	}
	/**
	 * 分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_list($page){
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_coupon_type order by type_id asc limit ".$start.",10";
		$detail=$this->db->getAll($sql);
		return $detail;
	}
	
	/**
	 * 总条数
	 * @return string
	 */
	function show_count(){
		$sql = "SELECT count(*) FROM xgj_coupon_type";
		$result=$this->db->getOne($sql);
		return $result;
	}

    /**
     * 根据id查询一条优惠券类型
     * @return array
     */
    function get_coupon_type_id($coupon_id){
    	$sql="select * from xgj_coupon_type where c_id={$coupon_id}";
    	
    	$result=$this->db->getRow($sql);
    	 
    	return $result;
    }
    
    /**
     * 根据id删除优惠券类型
     * @return array
     */
    function del_coupon_type_id($coupon_id){
    	$db=new db();
    	     	 
    	$result=$db->query("delete from xgj_coupon_type where c_id=$coupon_id");
    	 
    	return $result;
    }
    
}
