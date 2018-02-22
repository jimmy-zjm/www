<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 欧团每日推荐数据模型类
 */
class eu_recommend_model
{	
	/**
	 * 分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_list($page){
		$db=new db();
		$start=($page-1)*8;
		$sql = "SELECT * FROM xgj_eu_recommend r join xgj_eu_goods g on r.goods_id=g.goods_id order by r.recommend_id desc limit ".$start.",8";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 *总条数
	 * @return string
	 */
	function show_count(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_eu_recommend";
		$result=$db->getOne($sql);
		return $result;
	}
	
	/**
	 * 获取欧洲团代购推荐列表
	 */
	function get_en_recommend_goods(){
		$db=new db();
		$sql = "SELECT * FROM xgj_eu_recommend r join xgj_eu_goods g on r.goods_id=g.goods_id order by r.recommend_id asc ";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 * 获取欧洲团代购所有商品
	 */
	function get_eu_goods(){
		$db=new db();
		$sql = "SELECT * FROM xgj_eu_goods";
		$result=$db->getAll($sql);
		return $result;
	}
	
	/**
	 * 检测是否添加相同的
	 * @return string|mixed
	 */
	function check_identical($goods_id){
		$db=new db();
		$sql="select count(*)
		from xgj_eu_recommend
		where goods_id=$goods_id";
		$result=$db->getOne($sql);
		return $result;
	}

    /**
     * 根据id查询一条欧团每日推荐信息
     * @return array
     */
    function eu_recommend_id($recommend_id,$goods_id="1=1"){
    	$db=new db();
    	 
    	$sql="select * from xgj_eu_recommend r join xgj_eu_goods g on r.goods_id=g.goods_id where r.goods_id={$goods_id} and r.recommend_id={$recommend_id}";
    	
    	$result=$db->getRow($sql);
    	 
    	return $result;
    }
    
    /**
     * 根据id删除欧团每日推荐列表中的一条数据
     * @return array
     */
    function del_eu_recommend_id($recommend_id){
    	$db=new db();
    	     	 
    	$result=$db->query("delete from xgj_eu_recommend where recommend_id=$recommend_id");
    	 
    	return $result;
    }
    
}