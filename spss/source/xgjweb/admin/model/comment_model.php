<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 用户评价数据模型类
 */
class comment_model{
	/**
	 * 分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function furnish_show_list($page){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM 
			       	xgj_comment c 
				left join 
			     	xgj_furnish_goods g 
				on 
				   	c.goods_id=g.goods_id 
				left join 
			     	xgj_furnish_quote q 
				on 
				   	c.quote_id=q.quote_id 
				where 
				    c.status > 0 and c.class_id=1
				order by 
		        	c.comment_id desc 
				limit {$start},10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 *总条数
	 * @return string
	 */
	function furnish_show_count(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_comment";
		$result=$db->getOne($sql);
		return $result;
	}

    /**
     * 根据id查询一条用户评价信息
     * @return array
     */
    function comment_id($comment_id){
    	$db=new db();
    	 
    	$sql="SELECT * FROM xgj_comment c left join xgj_furnish_goods g on c.goods_id=g.goods_id left join xgj_furnish_quote q on c.quote_id=q.quote_id where c.comment_id={$comment_id}";
    	
    	$result=$db->getRow($sql);
    	 
    	return $result;
    }
    
    /**
     * 根据id删除产品材料列表
     * @return array
     */
    function del_comment_id($comment_id){
    	$db=new db();
    	     	 
    	$result=$db->query("delete from xgj_comment where comment_id=$comment_id");
    	 
    	return $result;
    }
    
}