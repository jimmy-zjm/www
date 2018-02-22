<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 服务商订单数据模型类
 */
class dealer_order_model{
	/**
	 * 新订单分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function new_show_list($page){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h) on (o.order_userid=u.user_id and o.house_id=h.house_id) where o.order_dealerid = 0 order by o.order_date asc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 * 新订单总条数
	 * @return string
	 */
	function new_show_count(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h) on (o.order_userid=u.user_id and o.house_id=h.house_id) where o.order_dealerid = 0 ";
		$result=$db->getOne($sql);
		return $result;
	}
	
	
	/**
	 * 订单统计分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_statistics_list($page,$where,$search='',$province){
		$db=new db();
		$start=($page-1)*10;
		if(empty($search)){
			$sql = "SELECT * FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h) on ( o.order_userid=u.user_id and o.house_id=h.house_id ) where $where order by o.order_state asc limit ".$start.",10";
		}else{
			$sql = "SELECT * FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h) on ( o.order_userid=u.user_id and o.house_id=h.house_id ) $search order by o.order_state asc limit ".$start.",10";
		}
			//echo $sql;exit;
		$detail=$db->getAll($sql);
		//var_dump($detail);exit;
		//$result=array();
		foreach ($detail as $k=>$v){
			if ($v['order_dealerid']==0){
				$detail[$k]['d_dealer']=0;
			}else{
				$detail[$k]['d_dealer']=$db->getRow("select * from xgj_furnish_dealer where d_id = {$v['order_dealerid']}");
			}
		}
		//var_dump($detail);exit;
		return $detail;
	}
	
	/**
	 * 订单统计总条数
	 * @return string
	 */
	function show_statistics_count($where,$search,$province=''){
		$db=new db();
		if(empty($search)){
			$sql = "SELECT count(*) FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h)on (o.order_userid=u.user_id and o.house_id=h.house_id ) $search ";			
		}else{
			if(empty($province)){
				$sql = "SELECT count(*) FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h)on (o.order_userid=u.user_id and o.house_id=h.house_id ) where $where ";
			}else{
				$sql = "SELECT count(*) FROM xgj_furnish_dealer where 1=1 $province ";
			}
		}
		$result=$db->getOne($sql);
		return $result;
	}
	
	/**
	 * 订单统计总金额
	 * @return string
	 */
	function show_statistics_price(){
		$db=new db();
		$sql = "SELECT sum(d.price) FROM xgj_furnish_order o join xgj_furnish_order_detail d on o.order_id=d.order_id ";
		$result=$db->getOne($sql);
		return $result;
	}
	
	
	/**
	 * 退货订单统计
	 * @param unknown $page
	 * @param unknown $where
	 * @param string $search
	 * @return multitype:
	 */
	function refund_show_list($page,$wheres,$searchs=''){
		/* $db=new db();
		$start=($page-1)*10;
		if(empty($searchs)){
			$sql = "SELECT * FROM xgj_furnish_order_refund where {$wheres} order by refund_time desc limit ".$start.",10";
		}else{
			$sql = "SELECT * FROM xgj_furnish_order_refund r r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) order by r.refund_time desc limit ".$start.",10";
		}
		
		$detail=$db->getAll($sql);
		
		return $detail; */
		/* $db=new db();
		$start=($page-1)*10;
		if(empty($searchs)){
			$sql = "SELECT * FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_dealer d) on ( o.order_userid=u.user_id and o.house_id=h.house_id and o.order_code=od.order_code and o.order_dealerid=d.d_id) where od.shipping_status=3 $wheres order by o.order_state asc limit ".$start.",10";
		}else{
			$sql = "SELECT * FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_dealer d) on ( o.order_userid=u.user_id and o.house_id=h.house_id and o.order_code=od.order_code and o.order_dealerid=d.d_id) where od.shipping_status=3 $searchs order by o.order_state asc limit ".$start.",10";
		}
		$detail=$db->getAll($sql);
		foreach ($detail as $k=>$v){
			if ($v['order_dealerid']==0){
				$detail[$k]['d_dealer']=0;
			}else{
				$detail[$k]['d_dealer']=$db->getRow("select * from xgj_furnish_dealer where d_id = {$v['order_dealerid']}");
			}
		}
		return $detail; */
		
		$db=new db();
		$start=($page-1)*10;
		if(empty($searchs)){
			$sql = "SELECT * FROM
			xgj_furnish_order_refund r join
			(xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_dealer d)
			on (r.user_id=u.user_id and r.house_id=h.house_id and r.detail_id=od.detail_id and r.d_id=d.d_id)
			where r.refund_status=1 {$wheres} order by r.refund_time desc limit ".$start.",10";
		}else{
			$sql = "SELECT * FROM
			xgj_furnish_order_refund r join
			(xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_dealer d)
			on (r.user_id=u.user_id and r.house_id=h.house_id and r.detail_id=od.detail_id and r.d_id=d.d_id)
			where r.refund_status=1 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) order by r.refund_time desc limit ".$start.",10";
			}
		
			$detail=$db->getAll($sql);
		
			return $detail;
		
	}
	
	/**
	 * 退货订单统计总数
	 * @param unknown $page
	 * @param unknown $where
	 * @param string $search
	 * @return multitype:
	 */
	function refund_show_count($wheres,$searchs=''){
		$db=new db();
		if(empty($searchs)){
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.refund_status=1 {$wheres}  ";
		}else{
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) and r.refund_status=1 ";
		} 
		/* $db=new db();
		if(empty($searchs)){
			$sql = "SELECT count(*) from xgj_furnish_order o join (xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_dealer d) on ( o.order_userid=u.user_id and o.house_id=h.house_id and o.order_code=od.order_code and o.order_dealerid=d.d_id) where od.shipping_status=3 $searchs";
		}else{
			$sql = "SELECT count(*) FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_dealer d) on ( o.order_userid=u.user_id and o.house_id=h.house_id and o.order_code=od.order_code and o.order_dealerid=d.d_id) where od.shipping_status=3 $wheres ";
		}*/
		$detail=$db->getOne($sql);
	
		return $detail; 
	}
	
	/**
	 * 退货订单统计总金额
	 * @return string
	 */
	function refund_show_price($wheres,$searchs=''){
		$db=new db();
		if(empty($searchs)){
			$sql = "SELECT sum(g.shop_price) FROM xgj_furnish_goods g where g.goods_id in (SELECT r.master_id FROM xgj_furnish_order_refund r where r.refund_status=1 {$wheres})";
		}else{
			$sql = "SELECT sum(g.shop_price) FROM xgj_furnish_goods g where g.goods_id in (SELECT r.salve_id FROM xgj_furnish_order_refund r where r.refund_status=1 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs}))";
		}
		$result=$db->getOne($sql);
		return $result;
	}
	
	/**
	 * 补货订单统计
	 * @param unknown $page
	 * @param unknown $where
	 * @param string $search
	 * @return multitype:
	 */
	function add_show_list($page,$wheres,$searchs=''){
	    $db=new db();
		$start=($page-1)*10;
		if(empty($searchs)){
		$sql = "SELECT * FROM 
		xgj_furnish_order_refund r join 
		(xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_order o ,xgj_furnish_dealer d) 
		on (r.user_id=u.user_id and r.house_id=h.house_id and r.detail_id=od.detail_id and r.d_id=d.d_id and r.order_id=o.order_id)
		where r.refund_status=2 {$wheres} order by r.refund_time desc limit ".$start.",10";
		}else{
		$sql = "SELECT * FROM 
		xgj_furnish_order_refund r join 
		(xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_order o ,xgj_furnish_dealer d)
		on (r.user_id=u.user_id and r.house_id=h.house_id and r.detail_id=od.detail_id and r.d_id=d.d_id and r.order_id=o.order_id)
		where r.refund_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) order by r.refund_time desc limit ".$start.",10";
		}
	
		$detail=$db->getAll($sql);
	
		return $detail; 
		/* $db=new db();
		$start=($page-1)*10;
		if(empty($searchs)){
			$sql = "SELECT * FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_dealer d) on ( o.order_userid=u.user_id and o.house_id=h.house_id and o.order_code=od.order_code and o.order_dealerid=d.d_id) where od.shipping_status=4 $wheres order by o.order_state asc limit ".$start.",10";
		}else{
			$sql = "SELECT * FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_dealer d) on ( o.order_userid=u.user_id and o.house_id=h.house_id and o.order_code=od.order_code and o.order_dealerid=d.d_id) where od.shipping_status=4 $searchs order by o.order_state asc limit ".$start.",10";
		}
		$detail=$db->getAll($sql);
		foreach ($detail as $k=>$v){
			if ($v['order_dealerid']==0){
				$detail[$k]['d_dealer']=0;
			}else{
				$detail[$k]['d_dealer']=$db->getRow("select * from xgj_furnish_dealer where d_id = {$v['order_dealerid']}");
			}
		}
		return $detail; */
	}
	
	/**
	 * 补货订单统计总数
	 * @param unknown $page
	 * @param unknown $where
	 * @param string $search
	 * @return multitype:
	 */
	function add_show_count($wheres,$searchs=''){
		$db=new db();
		if(empty($searchs)){
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.refund_status=2 {$wheres}";
		}else{
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) and r.refund_status=2";
		} 
		/* $db=new db();
		if(empty($searchs)){
			$sql = "SELECT count(*) from xgj_furnish_order o join (xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_dealer d) on ( o.order_userid=u.user_id and o.house_id=h.house_id and o.order_code=od.order_code and o.order_dealerid=d.d_id) where od.shipping_status=3 $searchs";
		}else{
			$sql = "SELECT count(*) FROM xgj_furnish_order o join (xgj_users u ,xgj_users_house h ,xgj_furnish_order_detail od ,xgj_furnish_dealer d) on ( o.order_userid=u.user_id and o.house_id=h.house_id and o.order_code=od.order_code and o.order_dealerid=d.d_id) where od.shipping_status=3 $wheres ";
		} */
		$detail=$db->getOne($sql);
	
		return $detail;
	}
	
	/**
	 * 补货订单统计总金额
	 * @return string
	 */
	function add_show_price($wheres,$searchs=''){
		$db=new db();
		if(empty($searchs)){
			$sql = "SELECT sum(g.shop_price) FROM xgj_furnish_goods g where g.goods_id in (SELECT r.master_id FROM xgj_furnish_order_refund r where r.refund_status=2 {$wheres})";
		}else{
			$sql = "SELECT sum(g.shop_price) FROM xgj_furnish_goods g where g.goods_id in (SELECT r.salve_id FROM xgj_furnish_order_refund r where r.refund_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs}))";
		}
		$result=$db->getOne($sql);
		return $result;
	}
		
	
	/**
	 * 我的订单分页 
	 * SELECT * FROM tx1 left join (tx2, tx3) ON (tx1.id=tx2.tid AND tx2.tid=tx3.tid) where tx1.id = 3
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function my_show_list($page,$condition){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_furnish_order o join (xgj_furnish_dealer d ,xgj_users u ,xgj_users_house h)on (o.order_dealerid=d.d_id and o.order_userid=u.user_id and o.house_id=h.house_id) where $condition order by o.order_state asc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 * 我的订单总条数
	 * @return string
	 */
	function my_show_count($condition){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_furnish_order o join (xgj_furnish_dealer d ,xgj_users u ,xgj_users_house h)on (o.order_dealerid=d.d_id and o.order_userid=u.user_id and o.house_id=h.house_id) where $condition";
		$result=$db->getOne($sql);
		return $result;
	}
	
	/**
	 * 获取服务商信息
	 * @return array
	 */
	function get_dealer_info(){
		$db=new db();
		$sql="select * from xgj_furnish_dealer ";
 		$result=$db->getAll($sql);
		return $result;
	}
	
	/**
	 * 根据城市和省份查找服务商信息
	 * @param unknown $province 省份
	 * @param unknown $city		城市
	 * @return multitype:		array
	 */
	function get_dealer_list($province='',$city=''){
		$db=new db();
		if ($province==''){
			$sql="select * from xgj_furnish_dealer where d_city='{$city}' ";
		}else if ($city==''){
			$sql="select * from xgj_furnish_dealer where d_province='{$province}' ";
		}else{
			$sql="select * from xgj_furnish_dealer where d_province='{$province}' and d_city='{$city}' ";
		}
		$result=$db->getAll($sql);
		return $result;
	}

	/**
	 * 分配订单给服务商
	 * @param unknown $table
	 * @param unknown $data
	 * @param unknown $where
	 * @return Ambigous <boolean, PDOStatement>
	 */
	function update_order_dealer_id($table, $data, $where){
		$db=new db();
		$result=$db->update($table, $data, $where);
		return $result;
	}
	
	
	
	
	
	

    /**
     * 根据id查询一条服务商订单信息
     * @return array
     */
    function dealer_order_id($order_id){
    	$db=new db();
    	 
    	$sql="select * from xgj_furnish_order o join (xgj_users u ,xgj_users_house h) on (o.order_userid=u.user_id and o.house_id=h.house_id) where o.order_id={$order_id}";
    	
    	$result=$db->getRow($sql);
    	 
    	return $result;
    }
    
    /**
     * 根据id删除产品材料列表
     * @return array
     */
    function del_dealer_order_id($order_id){
    	$db=new db();
    	     	 
    	$result=$db->query("update xgj_furnish_order set d_runstatus=-1 where order_id=$order_id");
    	 
    	return $result;
    }
    
}