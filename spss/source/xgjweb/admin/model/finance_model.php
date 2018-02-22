<?php
header ( 'content-type:text/html;charset=utf-8' );
require_once (WWW_DIR . "/conf/mysql_db.php");
/**
 *
 * @author Administrator
 *         财务结算数据模型
 */
class finance_model {
	/**
	 * 结算申请列表
	 * 
	 * @param unknown $page        	
	 * @param unknown $condition        	
	 * @return multitype:
	 */
	function finance_apply_list($page, $condition) {
		$db = new db ();
		$start = ($page - 1) * 10;
		$sql = "SELECT * FROM xgj_furnish_finance f join xgj_furnish_dealer d on f.d_id=d.d_id where $condition order by f.finance_status asc limit " . $start . ",10";
		$result = $db->getAll ( $sql );
		return $result;
	}
	/**
	 * 结算申请条数
	 * 
	 * @param unknown $condition        	
	 * @return Ambigous <string, mixed>
	 */
	function finance_apply_count($condition) {
		$db = new db ();
		$sql = "SELECT count(*) from xgj_furnish_finance f join xgj_furnish_dealer d on f.d_id=d.d_id where $condition";
		$result = $db->getOne ( $sql );
		return $result;
	}
	
	/**
	 * 结算详情公司
	 * 
	 * @param unknown $d_id        	
	 * @return Ambigous <multitype:, mixed>
	 */
	function finance_company($d_id) {
		$db = new db ();
		$result = $db->getRow ( "select d_company from xgj_furnish_dealer where d_id=$d_id" );
		return $result;
	}
	
	/**
	 * 财务结算详情列表
	 * 
	 * @param unknown $page        	
	 * @param unknown $d_id        	
	 * @return multitype:
	 */
	function finance_info_list($page, $d_id) {
		$db = new db ();
		$start = ($page - 1) * 10;
		$sql = "SELECT * FROM 
				xgj_furnish_order_info i 
				join (
				xgj_furnish_dealer d ,
				xgj_furnish_order_detail de ,
				xgj_furnish_finance_construct_rate r
				) on (
				i.d_id=d.d_id 
				and 
				i.order_id=de.order_id
				and 
				de.quote_id = r.quote_id) 
				where 
				i.d_id={$d_id} 
				order by 
				i.add_order_time asc 
				limit 
				" . $start . ",10";
		$result = $db->getAll ( $sql );
		return $result;
	}
	/**
	 * 财务结算详情总条数
	 * 
	 * @param unknown $d_id        	
	 * @return Ambigous <string, mixed>
	 */
	function finance_info_count($d_id) {
		$db = new db ();
		$sql = "SELECT count(*) FROM 
				xgj_furnish_order_info i 
				join (
				xgj_furnish_dealer d ,
				xgj_furnish_order_detail de ,
				xgj_furnish_finance_construct_rate r
				) on (
				i.d_id=d.d_id 
				and 
				i.order_id=de.order_id
				and 
				de.quote_id = r.quote_id) 
		        where d.d_id={$d_id} ";
		$result = $db->getOne ( $sql );
		return $result;
	}
	/**
	 * 财务结算详情总金额
	 * 
	 * @param unknown $d_id        	
	 * @return number
	 */
	function finance_info_price($d_id) {
		$db = new db ();
		$sql = "SELECT * FROM
				xgj_furnish_order_info i 
				join (
				xgj_furnish_dealer d ,
				xgj_furnish_order_detail de ,
				xgj_furnish_finance_construct_rate r
				) on (
				i.d_id=d.d_id 
				and 
				i.order_id=de.order_id
				and 
				de.quote_id = r.quote_id)
				where
				d.d_id={$d_id}
			";
		$result = $db->getAll ( $sql );
		$finance_price = 0;
		foreach ( $result as $v ) {
			if ($v ['quote_status'] == 4) {
				$finance_price += $v ['quote_price'] * $v ['first_rate'] * $v ['construct_rate'] * 0.85 + $v ['quote_price'] * $v ['first_rate'] * 0.1;
			} else if ($v ['quote_status'] == 6) {
				$finance_price += $v ['quote_price'] * $v ['mid_rate'] * $v ['construct_rate'] * 0.85 + $v ['quote_price'] * $v ['mid_rate'] * 0.1;
			}
		}
		// var_dump($finance_price);exit;round($num,2)
		return round ( $finance_price, 2 );
	}
	/**
	 * 提交结算信息
	 * 
	 * @param unknown $table        	
	 * @param unknown $data        	
	 * @param unknown $where        	
	 * @return Ambigous <boolean, PDOStatement>
	 */
	function finance_message_d_id($table, $data, $where) {
		$db = new db ();
		$result = $db->update ( $table, $data, $where );
		return $result;
	}
	/**
	 * 财务支付的一些操作
	 * 
	 * @param unknown $d_id        	
	 * @param unknown $finance_info_price        	
	 * @return boolean
	 */
	function finance_pay($d_id, $finance_info_price) {
		$db = new db ();
		$sql = "select d_price from xgj_furnish_dealer where d_id={$d_id}";
		$d_price = $db->getOne ( $sql );
		$update_price = $d_price + $finance_info_price;
		$rs = $db->update ( 'xgj_furnish_dealer', array (
				'd_price' => $update_price 
		), "d_id={$d_id}" );
		$data = array (
				'd_id' => $d_id,
				'finance_price' => $finance_info_price,
				'log_time' => time () 
		);
		$ru = $db->add ( 'xgj_furnish_finance_log', $data );
		$select = "SELECT i.order_id,de.detail_id,de.quote_status FROM
				xgj_furnish_order_info i 
				join (
				xgj_furnish_dealer d ,
				xgj_furnish_order_detail de ,
				xgj_furnish_finance_construct_rate r
				) on (
				i.d_id=d.d_id 
				and 
				i.order_id=de.order_id
				and 
				de.quote_id = r.quote_id)
				where
				d.d_id={$d_id}
		";
		$result = $db->getAll ( $select );
		foreach ( $result as $v ) {
			if ($v ['quote_status'] == 4 || $v ['quote_status'] == 6) {
				$re = $db->update ( 'xgj_furnish_order_detail', array (
						'quote_status' => $v ['quote_status'] . '1' 
				), "detail_id={$v['detail_id']}" );
			} else {
				$re = 1;
			}
		}
		if ($rs && $re && $ru) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 财务结算历史列表
	 * 
	 * @param unknown $page        	
	 * @param unknown $d_id        	
	 * @return multitype:
	 */
	function finance_log_list($page, $d_id) {
		$db = new db ();
		$start = ($page - 1) * 10;
		$sql = "select * from xgj_furnish_finance_log where d_id={$d_id} order by log_time desc limit " . $start . ",10";
		$result = $db->getAll ( $sql );
		return $result;
	}
	
	/**
	 * 财务结算总数
	 * 
	 * @param unknown $d_id        	
	 * @return Ambigous <string, mixed>
	 */
	function finance_log_count($d_id) {
		$db = new db ();
		$sql = "SELECT count(*) FROM xgj_furnish_finance_log where d_id={$d_id}";
		$result = $db->getOne ( $sql );
		return $result;
	}
	/**
	 * 退货订单统计
	 * 
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function refund_show_list($page, $wheres, $searchs = '') {
		$db = new db ();
		$start = ($page - 1) * 10;
		if (empty ( $searchs )) {
			$sql = "SELECT * FROM
			xgj_furnish_order_refund r join
			(xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de )
			on (de.order_id=i.order_id and r.d_id=d.d_id and de.detail_id=r.detail_id)
			where r.refund_status=1 and r.first_audit_status=2 {$wheres} order by r.refund_time desc limit " . $start . ",10";
		} else {
			$sql = "SELECT * FROM
			xgj_furnish_order_refund r join
			(xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
			on (de.order_id=i.order_id and r.d_id=d.d_id and de.detail_id=r.detail_id)
			where r.refund_status=1 and r.first_audit_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) order by r.refund_time desc limit " . $start . ",10";
		}
		$detail = $db->getAll ( $sql );
		return $detail;
	}
	
	/**
	 * 退货订单统计总数
	 * 
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function refund_show_count($wheres, $searchs = '') {
		$db = new db ();
		if (empty ( $searchs )) {
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.refund_status=1 and r.first_audit_status=2 {$wheres}  ";
		} else {
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) and r.refund_status=1 and r.first_audit_status=2 ";
		}
		$detail = $db->getOne ( $sql );
		return $detail;
	}
	
	/**
	 * 退货订单统计总金额
	 * 
	 * @return string
	 */
	function refund_show_price($wheres, $searchs = '') {
		$db = new db ();
		if (empty ( $searchs )) {
			$sql = "SELECT sum(r.refund_price) FROM xgj_furnish_order_refund r where r.refund_status=1 and r.first_audit_status=2 {$wheres}";
		} else {
			$sql = "SELECT sum(r.refund_price) FROM xgj_furnish_order_refund r where r.refund_status=1 and r.first_audit_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs})";
		}
		$result = $db->getOne ( $sql );
		return $result;
	}
	
	/**
	 * 退换货详细信息
	 * 
	 * @param unknown $refund_id        	
	 * @return Ambigous <multitype:, mixed>
	 */
	function refund_info($refund_id) {
		$db = new db ();
		$sql = "select `refund_goods`,`refund_price`,`refund_msg`,`refund_code` from xgj_furnish_order_refund where refund_id=$refund_id";
		$result = $db->getRow ( $sql );
		$arr = explode ( ';', $result ['refund_goods'] );
		$list = explode ( ',', $arr [0] );
		$refund_num = explode ( ',', $arr [1] );
		$cc = array ();
		foreach ( $list as $k => $v ) {
			$cc [$k] ['goods_sn'] = $v;
			$cc [$k] ['num'] = $refund_num [$k];
			$cc [$k] ['refund_price'] = $result ['refund_price'];
			$cc [$k] ['refund_code'] = $result ['refund_code'];
			$cc [$k] ['refund_msg'] = $result ['refund_msg'];
		}
		foreach ( $cc as $k => $v ) {
			$cc [$k] ['list'] = $db->getRow ( "select goods_img,goods_name,goods_model,goods_unit,goods_brand,shop_price from xgj_furnish_goods where goods_sn={$v['goods_sn']}" );
		}
		return $cc;
	}
	
	
	/**
	 * 补货订单统计
	 * 
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function add_show_list($page, $wheres, $searchs = '') {
		$db = new db ();
		$start = ($page - 1) * 10;
		if (empty ( $searchs )) {
			$sql = "SELECT * FROM
			xgj_furnish_order_refund r join
			(xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
			on (de.order_id=i.order_id and r.d_id=d.d_id and de.detail_id=r.detail_id)
			where r.refund_status=2 and r.first_audit_status=2 {$wheres} order by r.refund_time desc limit " . $start . ",10";
		} else {
			$sql = "SELECT * FROM
			xgj_furnish_order_refund r join
			(xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
			on (de.order_id=i.order_id and r.d_id=d.d_id and de.detail_id=r.detail_id)
			where r.refund_status=2 and r.first_audit_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) order by r.refund_time desc limit " . $start . ",10";
		}
		$detail = $db->getAll ( $sql );
		return $detail;
	}
	
	/**
	 * 补货订单统计总数
	 * 
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function add_show_count($wheres, $searchs = '') {
		$db = new db ();
		if (empty ( $searchs )) {
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.refund_status=2 and r.first_audit_status=2 {$wheres}";
		} else {
			$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) and r.refund_status=2 and r.first_audit_status=2 ";
		}
		$detail = $db->getOne ( $sql );
		return $detail;
	}
	
	/**
	 * 补货订单统计总金额
	 * 
	 * @return string
	 */
	function add_show_price($wheres, $searchs = '') {
		$db = new db ();
		if (empty ( $searchs )) {
			$sql = "SELECT sum(r.refund_price) FROM xgj_furnish_order_refund r where r.refund_status=2 and r.first_audit_status=2 {$wheres}";
		} else {
			$sql = "SELECT sum(r.refund_price) FROM xgj_furnish_order_refund r where r.refund_status=2 and r.first_audit_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs})";
		}
		$result = $db->getOne ( $sql );
		return $result;
	}
	
	/**
	 * 伙伴自购订单统计
	 * 
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function self_buy_show_list($page, $conditions) {
		$db = new db ();
		$start = ($page - 1) * 10;
		$sql = "SELECT * FROM
		xgj_furnish_order_refund r join
		(xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
		on (de.order_id=i.order_id and r.d_id=d.d_id and de.detail_id=r.detail_id)
		where r.refund_status=3 and r.first_audit_status=2 {$conditions} order by r.refund_time desc limit " . $start . ",10";
		//exit($sql);
		$detail = $db->getAll ( $sql );
		return $detail;
	}
	
	/**
	 * 伙伴自购订单统计总数
	 * 
	 * @param unknown $page        	
	 * @param unknown $where        	
	 * @param string $search        	
	 * @return multitype:
	 */
	function self_buy_show_count($conditions) {
		$db = new db ();
		$sql = "SELECT count(*) FROM xgj_furnish_order_refund r where r.refund_status=3 and r.first_audit_status=2 {$conditions}";
		$detail = $db->getOne ( $sql );
		return $detail;
	}
	
	/**
	 * 伙伴自购订单统计总金额
	 * 
	 * @return string
	 */
	function self_buy_show_price($conditions) {
		$db = new db ();
		$sql = "SELECT sum(r.refund_price) FROM xgj_furnish_order_refund r where r.refund_status=3 and r.first_audit_status=2 {$conditions}";
		$result = $db->getOne ( $sql );
		return $result;
	}
	
	/**
	 * 获取退换货状态信息
	 *
	 * @param unknown $refund_id
	 * @return multitype:
	 */
	function get_one_refund_id($refund_id) {
		$db = new db ();
		$re = $db->getRow ( "select `first_audit_status` ,`review_audit_status` from xgj_furnish_order_refund where refund_id=$refund_id " );
		return $re;
	}
	
}