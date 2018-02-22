<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 报价清单 
 */
class quote_model{

	/**
	 * 修改材料
	 * renturn 1：成功 2：识别码填错
	 */
	function edit_list($goods_sn,$child_id){

		$db=new db();
		$sql = "SELECT * FROM xgj_furnish_goods where goods_sn = $goods_sn";
		$row = $db->getAll($sql);

		if (empty($row)) {
			return '2';exit;
		}

		$goods_id = $row['0']['goods_id'];

		$table = 'xgj_quote_child_list';
		$data = array('goods_sn'=>$goods_sn,'goods_id'=>$goods_id);
		$where = "child_id = $child_id";

		$result = $db->update($table,$data,$where);

		return $result;
		// $sql = "UPDATA xgj_quote_child_list set goods_sn = $goods_sn,goods_id = $goods_id where child_id = $child_id";
	}
	/**
	 * 分页列表
	 * @param unknown $page  
	 * @return multitype:unknown
	 */
	function show_list($page){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_furnish_quote order by quote_id asc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 * 总条数
	 * @return string
	 */
	function show_count(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_furnish_quote";
		$result=$db->getOne($sql);
		return $result;
	}
	
	/**
	 * 根据id查询一条数据
	 * @param unknown $quote_id
	 * @return mixed
	 */
	function getQuoteByid($quote_id){
		$db=new db();
		$sql = "SELECT * FROM xgj_furnish_quote where quote_id=$quote_id";
		$detail=$db->getRow($sql);
		return $detail;
	}
	
	/**
	 * 根据id删除数据
	 * @param unknown $quote_id
	 * @return PDOStatement
	 */
	function del_furnish_quote_id($quote_id){
		$db=new db();
		$sql = "delete from xgj_furnish_quote where quote_id=$quote_id";
		$result=$db->query($sql);
		return $result;
	}
	
	/**
	 * 总清单列表
	 */
	function furnish_goods_list($condition){
		$db=new db();
		$sql = "SELECT * FROM xgj_furnish_goods where $condition";
		$detail=$db->getAll($sql);
		return $detail;
	}
	/**
	 * 查询分类
	 */
	function furnish_cat_list(){
		$db=new db();
		$sql = "SELECT * FROM xgj_furnish_cat";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 * 根据类型和报价id查询
	 * @param unknown $quote_id 报价id
	 * @param unknown $level 经济型 舒适型 豪华型
	 */
	function child_level_quote_id($quote_id,$level){
		$db=new db();
		$sql="
		select *
		from xgj_quote_child_list ch
		join xgj_furnish_goods g on ch.goods_id=g.goods_id
		where ch.quote_id=$quote_id and ch.level=$level 
		";
		$detail=$db->getAll($sql);
		$result=array();
		foreach ($detail as $k=>$v ){
		$result[$k]=$v;
		$result[$k]['f_formula']=$db->getOne("select formula from xgj_quote_child_list where goods_id={$v['chf_id']} and quote_id=$quote_id and level=$level ");
		}
		//var_dump($result);exit;
		return $result;
	}
	
	/**
	 * 根据商品id查询子清单列表 
	 * @param unknown $goods_id
	 * @return mixed
	 */
	function child_goods_id($goods_id,$child_id){
		$db=new db();
		$sql="
		select *
		from xgj_quote_child_list ch
		join xgj_furnish_goods g on ch.goods_id=g.goods_id
		where g.goods_id=$goods_id and ch.child_id=$child_id 
		";
		$item=$db->getRow($sql);
		//var_dump($item);exit;
		
		$result=array();
		foreach ($item as $k=>$v ){
		$result[$k]=$v;
		//echo "select formula from xgj_quote_child_list where child_id={$v['chf_id']}";exit;
		if($item['chf_id']!=0){
		$result['f_formula']=$db->getOne("select formula from xgj_quote_child_list where goods_id={$item['chf_id']} and goods_id=$goods_id and child_id = $child_id ");
		}else{
			$result['f_formula']='';
		}
		}
		return $result;
	}
	
	/**
	 * 根据商品id查询清单列表
	 * @param unknown $goods_id
	 * @return mixed
	 */
	function furnish_goods_id($goods_id){
		$db=new db();
		$sql="
		select *
		from xgj_furnish_goods 
		where goods_id=$goods_id
		";
		$item=$db->getRow($sql);
		return $item;
	}
	
	/**
	 * 删除子清单
	 * @param unknown $child_id
	 * @return PDOStatement
	 */
	function del_child_id($child_id){
		$db=new db();
		$sql="
		delete from xgj_quote_child_list where child_id=$child_id
		";
		$item=$db->query($sql);
		return $item;
	}
	
	/**
	 * 检测是否添加相同的
	 * @return string|mixed
	 */
	function check_identical($level,$chf_id,$quote_id,$goods_id){
		$db=new db();
		$sql="select count(*) 
		from xgj_quote_child_list 
		where level=$level and chf_id=$chf_id and quote_id=$quote_id and goods_id=$goods_id";
		$result=$db->getOne($sql);
		return $result;
	}
}