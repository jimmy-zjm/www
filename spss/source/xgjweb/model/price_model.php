<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");

class price {
	/**
	 * 报价
	 */
	
	/**
	 * 根据house_id查房屋信息
	 */
	function user_house_data($user_id){
		$db=new db();
		$sql = "SELECT * FROM xgj_users_house where user_id = $user_id";
		$result=$db->getAll($sql);
		return $result;
	
	}

	/**
	 * 根据quote_id查系统信息
	 */
	function xgj_furnish_quote($id){
		$db=new db();
		$sql = "SELECT * FROM xgj_furnish_quote where quote_id = $id";
		$result=$db->getAll($sql);
		return $result;
	
	}

	public function getDealerAdjustInfo($id){
        $db = new db();
        $sql = "select * from xgj_dealer_adjust_info where id=".$id;
        $result = $db->getRow($sql);
        return $result;
    }

	/**
	 * 查经销商信息
	 */
	public function xgj_furnish_dealer($xcd){
		$db=new db();
		$sql = "SELECT d_service_city_all,d_province,d_city,d_area FROM xgj_furnish_dealer WHERE d_runstatus='1'";
		$result=$db->getAll($sql);
		$retule = false;
		foreach ($result as $k => $v) {
			$citys = $v['d_province'].'-'.$v['d_city'].'-'.$v['d_area'];
			$city = explode('|', $v['d_service_city_all']);
			if ($citys == $xcd) $retule = true;
			else if (in_array($xcd, $city)) $retule = true;
		}
		return $retule;
	}

	/**
	 * 根据cat_id查新风材料信息
	 */
	function money($cat_id,$lv){
		$db=new db();
		$sql = "SELECT q.*,g.goods_name,g.shop_price,g.goods_model,g.goods_id,g.goods_sn,g.goods_brand,g.goods_img,g.goods_lv,g.goods_unit,g.features,g.origin FROM xgj_quote_child_list q JOIN xgj_furnish_goods g ON q.goods_sn = g.goods_sn WHERE q.quote_id = $cat_id  and q.level = $lv ORDER BY sort ASC";
		
		$result=$db->getAll($sql);

		return $result;
	
	}

	function complexList($complex){
		$where = '';
		foreach ($complex as $k => $v) {
			$where .= "child_id = $v or ";
		}
		$where = rtrim($where,'or ');
		$mysql = new db();
    	$sql = "SELECT * FROM xgj_quote_child_list where $where";
    	$return = $mysql->getAll($sql);
        return $return;
	}

	//查询主机数
    function hostnum($id){
    	$mysql = new db();
    	$sql = "SELECT * FROM xgj_quote_child_list WHERE host='1' and quote_id = $id";
    	$return = $mysql->getAll($sql);
        return $return;
    }

    //查询关联
    public function guanlian($id){
		$db = new db();
		$sql = "SELECT * FROM xgj_quote_child_list where child_id={$id}";
		$result=$db->getRow($sql);
		return $result;
	}

    //查询系统可用优惠券
    function coupon($id){

    	$mysql = new db();
    	$sql = "SELECT coupon,quote_name,gift FROM xgj_furnish_quote WHERE quote_id = $id";
    	$return = $mysql->getAll($sql);
        return $return;
    }
    //获取购物车信息
    function getCart(){
    	if (!empty($_SESSION['userId'])) {
    		$db = new db();
	    	$sql = "SELECT * FROM xgj_furnish_cart where user_id={$_SESSION['userId']}";
			$detail=$db->getAll($sql);
			return $detail;
    	}else{
    		return ;
    	}
    	
    }
	//查询同类其他系统
	public function getTuijian($quote_id){
		$db = new db();

		$sql = "SELECT cat_id FROM xgj_furnish_quote where quote_id={$quote_id}";
		$result=$db->getRow($sql);
		$sql = "SELECT quote_id,quote_name FROM xgj_furnish_quote where cat_id={$result['cat_id']} and quote_id <> {$quote_id}";
		$data=$db->getAll($sql);
		return $data;
	}
	public function feilei($quote_id){
		$db = new db();
		$sql = "SELECT * FROM xgj_quote_type where quote_id={$quote_id}";
		$result=$db->getAll($sql);
		return $result;
	}

	public function filename($quote_id){
		$db = new db();
		$sql = "SELECT * FROM xgj_furnish_quote where quote_id={$quote_id}";
		$result=$db->getRow($sql);
		return $result;
	}

	public function getCustomerInfo($id){
		$db = new db();
        $sql = "select * from pad_customer where id=".$id;
        $result = $db->getAll($sql);
        return $result;
	}

	public function getCustomerQuoteInfo($id){
		$db = new db();
        $sql = "select * from pad_customer_quote where id=".$id;
        $data = $db->getRow($sql);
        $areaAll = explode('|', $data['area']);
        foreach ($areaAll as $k => $v) {
        	$area[$k] = explode(',', $v);
        	$totalArea[] = array_sum($area[$k]);
        	$houseLayout[] = count($area[$k])==1 && $area[$k]['0']==0?'0':count($area[$k]);
        }

        $result = $this->getCustomerInfo($data['c_id']);

		$result['0']['area']          = $areaAll['0'];
		$result['0']['basement_area'] = $areaAll['6'];
		$result['0']['attic_area']    = $areaAll['5'];
		$result['0']['basement']      = count($area['6'])==1 && $area['6']['0']==0?'0':count($area['6']);
		$result['0']['attic']         = count($area['5'])==1 && $area['6']['0']==0?'0':count($area['5']);
		$result['0']['type_area']     = $areaAll['0'].','.$areaAll['5'].','.$areaAll['6'].'|'.$areaAll['1'].'|'.$areaAll['2'].'|'.$areaAll['3'].'|'.$areaAll['4'];
		$result['0']['total_area']    = array_sum($totalArea);
		$result['0']['house_layout']  = ($houseLayout['0']+$houseLayout['5']+$houseLayout['6']).','.$houseLayout['1'].','.$houseLayout['2'].','.$houseLayout['3'].','.$houseLayout['4'];

        return $result;
	}

	public function userData($userId){
		$db = new db();
        $sql = "select * from pad_user where id=".$userId;
        $result = $db->getRow($sql);
        return $result;
	}

	public function judgeCity($city){
		$db = new db();
        $sql = "select * from xgj_quote_judge where city='{$city}'"; 
        $result = $db->getRow($sql);
        return $result;
	}

	public function getCustomerQuote($id){
		$db = new db();
        $sql = "select * from pad_customer_quote where id=".$id;
        $result = $db->getRow($sql);
        return $result;
	}

	public function find($table,$where){
		$db = new db();
        $sql = "select * from $table where $where";
        $result = $db->getRow($sql);
        return $result;
	}

}