<?php
header ( 'content-type:text/html;charset=utf-8' );
require_once (WWW_DIR . "/conf/mysql_db.php");
/**
 *
 * @author L
 *         服务商订单数据模型类
 */
class dealer_order_model {
	/**
	 * 新订单分页
	 * @param unknown $page        	
	 * @return multitype:unknown
	 */
	function new_show_list($page) {
		$db = new db ();
		$start = ($page - 1) * 1;
		$sql = "SELECT i.* FROM xgj_furnish_order_info i where i.allot_status = 0 order by i.add_order_time asc limit " . $start . ",1";
		$detail = $db->getAll ( $sql );
		return $detail;
	}
	
	/**
	 * 新订单总条数
	 * @return string
	 */
	function new_show_count() {
		$db = new db ();
		$sql = "SELECT count(*) FROM xgj_furnish_order_info i where i.allot_status = 0 ";
		$result = $db->getOne ( $sql );
		return $result;
	}
	
	/**
	 * 订单统计分页
	 * @param unknown $page        	
	 * @return multitype:unknown
	 */
	function show_statistics_list($page, $where, $search = '', $province) {
		$db = new db ();
		$start = ($page - 1) * 1;
		if (empty ( $search )) {
			$sql = "SELECT 
			i.* FROM xgj_furnish_order_info i 
			where $where order by i.add_order_time asc limit " . $start . ",1";
		} else {
			$sql = "SELECT 
			i.* FROM xgj_furnish_order_info i 
			$search order by i.add_order_time asc limit " . $start . ",1";
		}
		$detail = $db->getAll ( $sql );
		foreach ( $detail as $k => $v ) {
			if ($v ['allot_status'] == 0) {
				$detail [$k] ['d_dealer'] = 0;
			} else {
				$detail [$k] ['d_dealer'] = $db->getRow ( "select * from xgj_furnish_dealer where d_id = {$v['d_id']}" );
			}
		}
		return $detail;
	}
	
	/**
	 * 订单统计总条数
	 * @return string
	 */
	function show_statistics_count($where, $search, $province = '') {
		$db = new db ();
		if (empty ( $search )) {
			$sql = "SELECT count(*) FROM xgj_furnish_order_info i $search ";
		} else {
			if (empty ( $province )) {
				$sql = "SELECT count(*) FROM xgj_furnish_order_info i where $where ";
			} else {
				$sql = "SELECT count(*) FROM xgj_furnish_dealer where 1=1 $province ";
			}
		}
		$result = $db->getOne ( $sql );
		return $result;
	}
	
	/**
	 * 订单统计总金额
	 * @return string
	 */
	function show_statistics_price() {
		$db = new db ();
		$sql = "SELECT sum(i.goods_amount) FROM xgj_furnish_order_info i ";
		$result = $db->getOne ( $sql );
		return $result;
	}
	
	/**
	 * 退货订单统计
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function refund_show_list($page, $wheres, $searchs = '') {
		$db = new db ();
		$start = ($page - 1) * 1;
		if (empty ( $searchs )) {
			$sql = "SELECT * FROM
			xgj_furnish_order_refund r join
			(xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
			on (r.detail_id=de.detail_id and r.d_id=d.d_id and de.order_id=i.order_id)
			where r.refund_status=1 {$wheres} order by r.refund_time desc limit " . $start . ",1";
		} else {
			$sql = "SELECT * FROM
			xgj_furnish_order_refund r join
			(xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
			on (r.detail_id=de.detail_id and r.d_id=d.d_id and de.order_id=i.order_id)
			where r.refund_status=1 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) order by r.refund_time desc limit " . $start . ",1";
		}
		
		$detail = $db->getAll ( $sql );
		
		return $detail;
	}
	
	/**
	 * 退货订单统计总数
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function refund_show_count($wheres, $searchs = '') {
		$db = new db ();
		if (empty ( $searchs )) {
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.refund_status=1 {$wheres}  ";
		} else {
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) and r.refund_status=1 ";
		}
		$detail = $db->getOne ( $sql );
		
		return $detail;
	}
	
	/**
	 * 退货订单统计总金额
	 * @return string
	 */
	function refund_show_price($wheres, $searchs = '') {
		$db = new db ();
		if (empty ( $searchs )) {
			$sql = "SELECT sum(r.refund_price) FROM xgj_furnish_order_refund r where r.refund_status=1 {$wheres}";
		} else {
			$sql = "SELECT sum(r.refund_price) FROM xgj_furnish_order_refund r where r.refund_status=1 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs})";
		}
		$result = $db->getOne ( $sql );
		return $result;
	}
	
	/**
	 * 补货订单统计
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function add_show_list($page, $wheres, $searchs = '') {
		$db = new db ();
		$start = ($page - 1) * 1;
		if (empty ( $searchs )) {
			$sql = "SELECT * FROM 
		xgj_furnish_order_refund r join 
		(xgj_furnish_order_info i ,xgj_furnish_dealer d,xgj_furnish_order_detail de)
		on (r.detail_id=de.detail_id and r.d_id=d.d_id and de.order_id=i.order_id)
		where r.refund_status=2 {$wheres} order by r.refund_time desc limit " . $start . ",1";
		} else {
			$sql = "SELECT * FROM 
		xgj_furnish_order_refund r join 
		(xgj_furnish_order_info i ,xgj_furnish_dealer d)
		on (r.order_id=i.order_id and r.d_id=d.d_id)
		where r.refund_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) order by r.refund_time desc limit " . $start . ",1";
		}
		
		$detail = $db->getAll ( $sql );
		
		return $detail;
	}
	
	/**
	 * 补货订单统计总数
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function add_show_count($wheres, $searchs = '') {
		$db = new db ();
		if (empty ( $searchs )) {
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.refund_status=2 {$wheres}";
		} else {
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) and r.refund_status=2";
		}
		$detail = $db->getOne ( $sql );
		
		return $detail;
	}
	
	/**
	 * 补货订单统计总金额
	 * @return string
	 */
	function add_show_price($wheres, $searchs = '') {
		$db = new db ();
		if (empty ( $searchs )) {
			$sql = "SELECT sum(r.refund_price) FROM xgj_furnish_order_refund r where r.refund_status=2 {$wheres}";
		} else {
			$sql = "SELECT sum(r.refund_price) FROM xgj_furnish_order_refund r where r.refund_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs})";
		}
		$result = $db->getOne ( $sql );
		return $result;
	}
	
	/**
	 * 伙伴自购订单统计
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function self_buy_show_list($page, $conditions) {
		$db = new db ();
		$start = ($page - 1) * 1;
		$sql = "SELECT * FROM
		xgj_furnish_order_refund r join
		(xgj_furnish_order_info i ,xgj_furnish_dealer d,xgj_furnish_order_detail de)
		on (r.detail_id=de.detail_id and r.d_id=d.d_id and de.order_id=i.order_id)
		where r.refund_status=3 {$conditions} order by r.refund_time desc limit " . $start . ",1";
		// exit($sql);
		$detail = $db->getAll ( $sql );
		return $detail;
	}
	
	/**
	 * 伙伴自购订单统计总数
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function self_buy_show_count($conditions) {
		$db = new db ();
		$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.refund_status=3 {$conditions}";
		$detail = $db->getOne ( $sql );
		return $detail;
	}
	
	/**
	 * 伙伴自购订单统计总金额
	 * @return string
	 */
	function self_buy_show_price($conditions) {
		$db = new db ();
		$sql = "SELECT sum(r.refund_price) FROM xgj_furnish_order_refund r where r.refund_status=3 {$conditions}";
		$result = $db->getOne ( $sql );
		return $result;
	}
	
	/**
	 * 我的订单分页
	 * SELECT * FROM tx1 left join (tx2, tx3) ON (tx1.id=tx2.tid AND tx2.tid=tx3.tid) where tx1.id = 3
	 * @param unknown $page        	
	 * @return multitype:unknown
	 */
	function my_show_list($page, $condition) {
		$db = new db ();
		$start = ($page - 1) * 1;
		$sql = "SELECT 
		i.*,d.* 
		FROM xgj_furnish_order_info i 
		join xgj_furnish_dealer d 
		on i.d_id=d.d_id 
		where i.allot_status = 1 and i.admin_id={$_COOKIE['adminUserId']} 
		$condition order by i.allot_time asc limit " . $start . ",1";
		$detail = $db->getAll ( $sql );
		return $detail;
	}
	
	/**
	 * 我的订单总条数
	 * @return string
	 */
	function my_show_count($condition) {
		$db = new db ();
		$sql = "SELECT count(*) 
		FROM xgj_furnish_order_info i 
		join xgj_furnish_dealer d 
		on i.d_id=d.d_id 
		where i.allot_status = 1 and i.admin_id={$_COOKIE['adminUserId']} $condition";
		$result = $db->getOne ( $sql );
		return $result;
	}
	
	/**
	 * 根据id获取订单金额
	 * @param unknown $order_id
	 * @return Ambigous <multitype:, mixed>
	 */
	function get_dealer_order_price($order_id){
		$db = new db ();
		$sql = "SELECT i.goods_amount FROM xgj_furnish_order_info i where i.order_id = $order_id";
		$result = $db->getOne( $sql );
		return $result;
	}
	
	/**
	 * 根据id获取清单列表
	 * @param unknown $order_id
	 * @return Ambigous <multitype:, mixed>
	 */
	function get_dealer_order_stuff_list($detail_id,$stuff){
		$db = new db ();
		$sql = "SELECT stuff_goods
		FROM xgj_furnish_order_detail 
		where detail_id = $detail_id";
		$result = $db->getRow( $sql );
		$stuff_goods = explode ( ';', $result['stuff_goods'] );
		$goods_sn = explode ( ',', $stuff_goods[0] );
		$goods_num=explode ( ',', $stuff_goods[1] );
		$stuff_list=array();
		foreach ($goods_sn as $k => $v){
			$stuff_list[$k]['num']=$goods_num[$k];
			$stuff_list[$k]['goods_sn']=$v;
		}
		foreach ( $stuff_list as $k => $v ) {
			$stuff_list [$k] ['list'] = $db->getRow ( "select goods_img,goods_name,goods_model,goods_unit,goods_brand,shop_price from xgj_furnish_goods where goods_sn={$v['goods_sn']} and goods_lv=$stuff" );
		}
		return $stuff_list;
	}
	
	/**
	 * 根据id获取退换货自购清单列表
	 * @param unknown $detail_id
	 * @return Ambigous <multitype:, mixed>
	 */
	function get_dealer_order_refund_list($detail_id,$stuff,$refund_status){
		$db = new db ();
		$sql = "SELECT r.refund_goods,r.refund_msg,r.refund_id
		FROM xgj_furnish_order_refund r
		where r.detail_id = $detail_id and r.refund_status=$refund_status";
		$result = $db->getRow( $sql );
		$stuff_goods = explode ( ';', $result['refund_goods'] );
		//var_dump($stuff_goods);exit;
		$goods_sn = explode ( ',', $stuff_goods[0] );
		$goods_num=explode ( ',', $stuff_goods[1] );
		$stuff_list=array();
		foreach ($goods_sn as $k => $v){
			$stuff_list[$k]['num']=$goods_num[$k];
			$stuff_list[$k]['goods_sn']=$v;
			$stuff_list[$k]['refund_msg']=$result['refund_msg'];
			$stuff_list[$k]['refund_id']=$result['refund_id'];
		}
		foreach ( $stuff_list as $k => $v ) {
			$stuff_list [$k] ['list'] = $db->getRow ( "select goods_img,goods_name,goods_model,goods_unit,goods_brand,shop_price from xgj_furnish_goods where goods_sn={$v['goods_sn']} and goods_lv=$stuff" );
		}
		return $stuff_list;
	}
	
	/**
	 * 根据id获取订单详情
	 * @param unknown $order_id
	 * @return Ambigous <multitype:, mixed>
	 */
	function get_dealer_order_info($order_id){
		$db = new db ();
		$sql = "SELECT d.*,i.*
		FROM xgj_furnish_order_detail d
		join xgj_furnish_order_info i
		on d.order_id=i.order_id
		where d.order_id = $order_id";
		$result = $db->getAll ( $sql );
		//var_dump($result);exit;
		return $result;
	}
	
	/**
	 * 根据id获取订单详情的所有系统状态
	 * @param unknown $order_id
	 * @return Ambigous <multitype:, mixed>
	 */
	function get_dealer_order_info_status($order_id){
		$db = new db ();
		$sql = "SELECT d.quote_status
		FROM xgj_furnish_order_detail d
		where d.order_id = $order_id";
		$result = $db->getAll ( $sql );
		//var_export($result);exit;
		return $result;
	}
	
	/**
	 * 根据id获取施工计划质量检测信息
	 * @param unknown $order_id
	 * @return array
	 */
	function get_dealer_order_construct($detail_id){
		$db = new db ();
		$sql = "SELECT c.*,d.*
		FROM xgj_furnish_order_construct c
		join xgj_furnish_order_detail d
		on c.detail_id=d.detail_id
		where c.detail_id = $detail_id";
		$result = $db->getAll( $sql );
		//var_dump($sql);exit;
		return $result;
	}
	
	/**
	 * 根据上传文件信息
	 * @param unknown $class
	 * @return array
	 */
	function get_file($order_id,$class=''){
		$db = new db ();
		if($class==''){
			$sql = "SELECT *
			FROM xgj_furnish_order_file where order_id=$order_id";
		}else{
			$sql = "SELECT *
			FROM xgj_furnish_order_file
			where order_id=$order_id and class = $class";
		}
		$result = $db->getAll( $sql );
		return $result;
	}
	
	/**
	 * 订单调整表的订单信息
	 * @param unknown $order_id
	 * @return multitype:
	 */
	function get_order_adjust_info($order_id){
		$db = new db ();
		$sql = "SELECT *
		FROM xgj_furnish_order_adjust_info i
		join xgj_furnish_order_adjust_detail d
		on i.adjust_id=d.adjust_id
		where i.order_id = $order_id";
		$result = $db->getAll( $sql );
		//var_dump($result);exit;
		return $result;
	}
	
	/**
	 * 获取退换货伙伴自购的申请状态
	 * @return bool
	 */
	function get_first_audit_status($refund_id) {
		$db = new db ();
		$sql = "select first_audit_status from xgj_furnish_order_refund where refund_id={$refund_id} ";
		$result = $db->getOne( $sql );
		return $result;
	}
	
	/**
	 * 获取服务商信息
	 * @return array
	 */
	function get_dealer_info() {
		$db = new db ();
		$sql = "select * from xgj_furnish_dealer ";
		$result = $db->getAll ( $sql );
		return $result;
	}
	
	/**
	 * 根据城市和省份查找服务商信息
	 * @param unknown $province
	 *        	省份
	 * @param unknown $city        	
	 * @return multitype:
	 */
	function get_dealer_list($province = '', $city = '') {
		$db = new db ();
		if ($province == '') {
			$sql = "select * from xgj_furnish_dealer where d_city='{$city}' ";
		} else if ($city == '') {
			$sql = "select * from xgj_furnish_dealer where d_province='{$province}' ";
		} else {
			$sql = "select * from xgj_furnish_dealer where d_province='{$province}' and d_city='{$city}' ";
		}
		$result = $db->getAll ( $sql );
		foreach ( $result as $k => $v ) {
			$result [$k] ['count'] = $db->getOne ( "select count(order_code) from xgj_furnish_order_info where d_id={$v['d_id']}" );
		}
		return $result;
	}
	
	/**
	 * 分配订单给服务商或质量检测审核
	 * @param unknown $table        	
	 * @param unknown $data        	
	 * @param unknown $where        	
	 * @return Ambigous <boolean, PDOStatement>
	 */
	function update_by_id($table, $data, $where) {
		$db = new db ();
		$result = $db->update ( $table, $data, $where );
		return $result;
	}
	
	/**
	 * 检测质量审核，第一步没完成不能审核下一步施工信息
	 * @return array
	 */
	function check_construct_id($task_work,$order_id) {
		$db = new db ();
		$sql = "select status from
		xgj_furnish_order_construct 
		where task_work < $task_work and order_id={$order_id}";
		$result = $db->getAll( $sql );
		if ($task_work!=1){
			for ($i=0;$i<count($result);$i++){
				if (3!=$result[$i]['status']){
					$re=0;
					break;
				}else{
					$re=1;
				}
			}
		}else{
			$re=1;
		}
		//var_dump($result);exit;
		return $re;
	}
	
	/**
	 * 根据id查询一条服务商订单信息
	 * @return array
	 */
	function dealer_order_id($order_id) {
		$db = new db ();
		$sql = "select * from 
    	xgj_furnish_order_info i 
    	where i.order_id={$order_id}";
		
		$result = $db->getRow ( $sql );
		
		return $result;
	}
	
	// /**
	// * 根据id删除产品材料列表
	// * @return array
	// */
	// function del_dealer_order_id($order_id){
	// $db=new db();
	
	// $result=$db->query("update xgj_furnish_order set d_runstatus=-1 where order_id=$order_id");
	
	// return $result;
	// }
}