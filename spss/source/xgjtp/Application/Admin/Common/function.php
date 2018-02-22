<?php
/**
 * 后台公共函数库
 */



//获取省市县中的省份
function getPCD(){
    $area = M('xgj_area')->where('pid=100000')->field('id,name')->select();
    return $area;
}

//根据id获取省市县名称
function getPCDName($id){
    $area = M('xgj_area')->where("id=$id")->getField('name');
    return $area;
}
/**
 *健康舒适家居服务商订单结算状态转换
 *
 */
function switch_money($money){
	/*雅虎汇率转换*/
	$file = fopen('http://download.finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s=EURCNY=x','r'); //参数s=EURCNY, 欧元换人民币, 根据需要更换

	while ($data = fgetcsv($file)) //读取CSV文件里的每一行内容
	{
		$goods_list[] = $data;
	}
	 //$shijian = $goods_list[0][2] .' '. $goods_list[0][3];根据 quotes.csv文件得到的数据, 当前时间 在3,4列位置
	$huilv = !empty($goods_list[0][1])?$goods_list[0][1]:7.6; //根据 quotes.csv文件得到的数据, 汇率 在2列位置

	fclose($file);
	return sprintf("%.2f",$money*$huilv);

   /*百度汇率转换代码 
	$ch = curl_init();
    $url = 'http://apis.baidu.com/apistore/currencyservice/currency?fromCurrency=EUR&toCurrency=CNY&amount='.$money;
    $header = array(
        'apikey: 942f71d6e29e9443300e22f042ffebbe',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);
    //echo "<pre>";
    $price=json_decode($res, true);

    return sprintf("%.2f",$price['retData']['convertedamount']);*/
}

/**
 *健康舒适家居服务商订单结算状态转换
 *
 */
function dealer_order_pay_status($order_pay_status){
	switch ($order_pay_status) {
		case 0 :
			return '未结算';
			break;
		case 1 :
			return '一次性结算';
			break;
		case 2 :
			return '第一笔结算';
			break;
		case 3 :
			return '第二笔结算';
			break;
	}
}

/**
 * 获取某个商品分类的 儿子 孙子  重子重孙 的 id
 * @param type $cat_id
 */
function getCatGrandson ($cat_id,$tableName='xgj_ov_category')
{
    $GLOBALS['catGrandson'] = array();
    $GLOBALS['category_id_arr'] = array();
    // 先把自己的id 保存起来
    $GLOBALS['catGrandson'][] = $cat_id;
    // 把整张表找出来
    $GLOBALS['category_id_arr'] = M($tableName)->getField('id,pid');
    // 先把所有儿子找出来
    $son_id_arr = M($tableName)->where("pid = $cat_id")->getField('id',true);
    foreach($son_id_arr as $k => $v)
    {
        getCatGrandson2($v);
    }
    return $GLOBALS['catGrandson'];
}

/**
 * @param $arr
 * @param $key_name
 * @return array
 * 将数据库中查出的列表以指定的 id 作为数组的键名 
 */
function convert_arr_key($arr, $key_name)
{
	$arr2 = array();
	foreach($arr as $key => $val){
		$arr2[$val[$key_name]] = $val;        
	}
	return $arr2;
}

/**
 * 递归调用找到 重子重孙
 * @param type $cat_id
 */
function getCatGrandson2($cat_id)
{
    $GLOBALS['catGrandson'][] = $cat_id;
    foreach($GLOBALS['category_id_arr'] as $k => $v)
    {
        // 找到孙子
        if($v == $cat_id)
        {
            getCatGrandson2($k); // 继续找孙子
        }
    }
}


/**
 *健康舒适家居服务商订单进度状态转换
 *
 */
function dealer_order_schedule_status($order_schedule_status){
	switch ($order_schedule_status) {
		case 1 :
			return '未设计';
			break;
		case 2 :
			return '已设计';
			break;
		case 3 :
			return '辅材施工中';
			break;
		case 4 :
			return '辅材完工';
			break;
		case 5 :
			return '主设备施工中';
			break;
		case 6 :
			return '主设备完工';
			break;
		case 7 :
			return '订单已完成';
			break;
	}
}

/**
 *健康舒适家居服务商订单状态转换
 *
 */
function dealer_order_state($order_state){
	switch ($order_state) {
		case 0 :
			return '<font color="red">new</font>';
			break;
		case 1 :
			return '无';
			break;
		/* case 3 :
			return 'xgj_greenfood_goods';
			break;
		case 5 :
			return 'new';
			break; */
	}
}

/**
 *健康舒适家居服务商订单详情表结算状态转换
 *
 */
function dealer_order_deatil_state($state){
	switch ($state) {
		case 0 :
			return '未付款';
			break;
		case 1 :
			return '一次性付款';
			break;
		case 2 :
			return '第一笔付款';
			break;
		case 3 :
			return '第二笔付款';
			break;
		case 4 :
			return '第三笔付款';
			break;
		case 5 :
			return '结算完毕';
			break;
	}
}

/**
 *健康舒适家居服务商订单详情进度状态转换
 *
 */
function dealer_order_detail_quote_status($order_detail_quote_status){
	switch ($order_detail_quote_status) {
		case 0 :
			return '未设计';
			break;
		case 1 :
			return '已设计';
			break;
		case 2 :
			return '辅材未施工';
			break;
		case 3 :
			return '辅材完工';
			break;
		case 4 :
			return '主设备未施工';
			break;
		case 5 :
			return '主设备完工';
			break;
		case 41 :
			return '辅材已清算';
			break;
		case 61 :
			return '全部清算结束';
			break;
	}
}

/**
 *健康舒适家居订单管理发货状态转换
 *
 */
function dealer_order_shipping_status($state){
	switch ($state) {
		case 0 :
			return '未发货';
			break;
		case 1 :
			return '已发货';
			break;
		case 2 :
			return '已收货';
			break;
		case 4 :
			return '退货';
			break;
	}
}

/**
 * 健康舒适家居财务结算状态转换
 * @param unknown $finance_status
 * @return string
 */
function dealer_finance_status($finance_status){
	switch ($finance_status+1){
		case 1:
			return "<span style='color:green'>申请中</span>";
			break;
		case 2:
			return "<span style='color:blue'>未结算</span>";
			break;
		case 3:
			return "<span style='color:red'>已结算</span>";
			break;
		case 4:
			return "<span style='color:orange'>已拒绝</span>";
			break;
	}
}

/**
 * 健康舒适家居审核状态转换
 * @param unknown $finance_status
 * @return string
 */
function dealer_finance_audit_status($audit_status){
	switch ($audit_status){
		case 1:
			return '未审核';
			break;
		case 2:
			return '已审核';
			break;
		case 3:
			return '审核未通过';
			break;
	}
}

/**
 * 健康舒适家居财务管理行为状态转换
 * @param unknown $finance_status
 * @return string
 */
function dealer_finance_action_status($finance_status){
	switch ($finance_status+1){
		case 1:
			return '<font color="red">new</font>';
			break;
		case 2:
			return '<font color="red">new</font>';
			break;
		case 3:
			return '无';
			break;
		case 4:
			return '无';
			break;
	}
}
/**
 * 工作计划任务
 * @param unknown $task_work
 * @return string
 */
function dealer_order_task_work($task_work){
	switch ($task_work) {
		case 1 :
			return '上门测量';
			break;
		case 2 :
			return '深度设计';
			break;
		case 3 :
			return '辅材施工';
			break;
		case 4 :
			return '主设备安装';
			break;
	}
}
/**
 * 将字符串装换为几室几厅
 * @param unknown $str
 * @return string
 */
function house_layout($str){
	$arr=explode(',', $str);
	$count=count($arr);
	switch ($count){
		case 1 :
			return $arr[0].'室';
			break;
		case 2 :
			return $arr[0].'室'.$arr[1].'厅';
			break;
		case 3 :
			return $arr[0].'室'.$arr[1].'厅'.$arr[2].'厨';
			break;
		case 4 :
			return $arr[0].'室'.$arr[1].'厅'.$arr[2].'厨'.$arr[3].'卫';
			break;
		case 5 :
			return $arr[0].'室'.$arr[1].'厅'.$arr[2].'厨'.$arr[3].'卫'.$arr[4].'阳台';
			break;
	}
}

/**
 * 将字符串转换为卧室：[][][]客厅[][]
 * @param unknown $aa
 * @return string
 */
function type_area($aa){
	$arr=explode('|', $aa);
	$arr1=explode(',', $arr[0]);
	$bb1='卧室：';
	foreach ($arr1 as $v){
		if($v!=0){
			$bb1.="[<a style='color:red'>".$v.'</a>m²]';
		}
	}
	$arr2=explode(',', $arr[1]);
	$bb2='客厅：';
	foreach ($arr2 as $v){
		$bb2.="[<a style='color:red'>".$v.'</a>m²]';
	}
	$arr3=explode(',', $arr[2]);
	$bb3='厨房：';
	foreach ($arr3 as $v){
		$bb3.="[<a style='color:red'>".$v.'</a>m²]';
	}
	$arr4=explode(',', $arr[3]);
	$bb4='卫生间：';
	foreach ($arr4 as $v){
		$bb4.="[<a style='color:red'>".$v.'</a>m²]';
	}
	$arr5=explode(',', $arr[4]);
	$bb5='阳台：';
	foreach ($arr5 as $v){
		$bb5.="[<a style='color:red'>".$v.'</a>m²]';
	}
	$bb=$bb1.$bb2.$bb3.$bb4.$bb5;
	return $bb;
}


///////////////////////////////////////////////////////////////////////////////
////////////////////////////////通用//////////////////////////////////////////
function pay_method($state){
	switch ($state) {
		case 1:
			return '支付宝';
			break;
		case 2:
			return '网银';
			break;
		case 3:
			return '微信';
			break;
		case 4:
			return '蚂蚁';
			break;
		case 5:
			return '银联';
			break;
		case 6:
			return '钱包';
			break;
	}
}




////////////////////欧洲团代购&德国母婴////////////////////////////////////////
/*
	订单状态
*/
function eu_order_status($status){
	switch ($status) {
		case 0:
			return '等待付款';
			break;
		case 1:
			return '等待发货';
			break;
		case 2:
			return '等待收货';
			break;
		case 3:
			return '退货中';
			break;
		case 4:
			return '待评论';
			break;
		case 5:
			return '已完成';
			break;
		case 6:
			return '已取消';
			break;
		case 7:
			return '已删除';
			break;
	}
}

/*
	发货状态
*/
function eu_post_status($status){
	switch ($status) {
		case 0:
			return '未发货';
			break;
		
		case 1:
			return '已发货';
			break;
		case 3:
			return '已确认收货';
			break;
	}
}
/*
	退货状态
*/
function eu_return_status($status){
	switch ($status) {
		case 0:
			return '未申请';
			break;
		
		case 1:
			return '已申请退货';
			break;
		case 2:
			return '管理员已同意';
			break;
		case 3:
			return '管理员不同意';
			break;
		case 4:
			return '用户退货';
			break;
		case 5:
			return '退货已完成';
			break;
	}
}


/**提交OMS*/
function OMS($data_,$class='B'){
	header("Content-Type:text/html; charset=utf-8");
	$data=$data_['info'];
	if($class='B'){
		$str=$data['sn'].'01';
	}else if($class='C'){
		$str=$data['sn'].'08';
	}
	if($data['info']['pay_method']==1){
		$pay=M('xgj_eu_payment')->where("order_id='$str'")->find();
	}else if($data['info']['pay_method']==9){
		if($class='B'){
			$pay['trade_no']='Int'.$data['sn'];
		}else if($class='C'){
			$pay['trade_no']='Ovs'.$data['sn'];
		}
	}else{
		$pay=M('xgj_chinapay')->where("order_id='$str'")->find();
	}
	$detail=$data_['detail'];
	$content='';
	foreach ($detail as $k => $v) {
		$content.=$v['goods_title'].'|';
	}
	$arr=array();
	foreach ($detail as $k => $v) {
		$arr[]=array("entryId" =>$v['id'],	//分录ID-------？？？？
				"outerNumber"     =>$v['goods_mnemonic'],		//商品编码   goodssn
				"goodNumber"      =>$v['goods_sn'],		//商家编码   助记码
				"goodName"        =>$v['goods_title'],		//商品名称
				"qty"             =>$v['goods_num'],		//数量
				"price"           =>$v['goods_price'],		//单价
				"discountAmount"  =>'1',		//折扣金额
			);
	}
	//var_dump($arr);exit;
	$secret = "4tjizi1t5otxe43awhgjq7ms2talxa0x";
	//$uri ="http://172.16.100.248:8088/OMS/eic/rest/addTrades.action";
	$uri ="http://180.166.221.226:8088/OMS/eic/rest/addTrades.action";
	$post_data['appKey']     =  '86497276';
	$post_data['partnerID']  =  'eic-sdk-java-20130701';
	$post_data['format']     =  'json';
	$post_data['signMethod'] =  'md5';
	$post_data['requestID']	 =  'LCd33vUUYuWC5z83Iwg8ay4FHnwn1OVV';
	$post_data['timestamp']	 =   date("Y-m-d H:i:s");
	$post_data['version']	 =  '2.0';
	$a=array(
			array(
			"tradeNumber"      =>$class.$data['sn'],//订单编号
			"ecplatShopName"   =>"海外超市事业部",
			"orderCreateDate"  =>$data['add_time'],//下单时间
			"orderPayDate"     =>$data['pay_time'],//付款时间
			"orderFinishDate"  =>date("Y-m-d H:i:s"),//完成时间--------------------
			"orderAmount"      =>ceil($data['total_price']),//订单金额
			"deliveryFee"      =>'0',//运费-----|||||
			"discountAmount"   =>'0',//折扣额-----------------------
			"payAmount"        =>ceil($data['deal_price']),//付款金额
			"orderStatus"      =>'WAIT_SELLER_SEND_GOODS',//交易状态 WAIT_SELLER_SEND_GOODS -待发货 WAIT_BUYER_CONFIRM_GOODS-待确认收货 TRADE_CLOSED-交易关闭 TRADE_FINISHED-交易成功   ???????????
			"buyerName"        =>$data['user_name'],//购买人账号---------------------------------!!!!!!!
			"receiverName"     =>$data['shr_name'],//收件人姓名
			"receiverCountry"  =>"中国",//收件人国家
			"receiverProvince" =>$data['shr_pro'],//收件人省份
			"receiverCity"     =>$data['shr_city'],//收件人城市
			"receiverDistrict" =>$data['shr_area'],//收件人地区
			"receiverAddress"  =>$data['shr_addr'],//收货人详细地址
			"receiverMobile"   =>$data['shr_phone'],//收货人手机号
			"deliveryCompany"  =>'无',//快递公司编号-----------------------
			"deliveryNumber"   =>'无',//运单号--------------------------N
			"isInvoice"        =>'0',//是否开票 1-是 0-否
			"invoiceType"      =>'无',//发票类型-------------------------------------------
			"invoiceTitle"     =>'无',//发票抬头
			"invoiceDetail"    =>$content,//发票内容---------------------------------------------
			"buyerRemark"      =>"",//买家备注
			"orderRemark"      =>"",//订单备注
			"sellerRemark"     =>"身份证：{$data['identity_card']}; 交易流水号：{$pay['trade_no']}",//卖家备注
			"platdisAmt"       =>'0',//平台优惠------------------------------
			"shopDisamt"       =>'0',//店铺优惠------------------------------------
			"isReachPay"       =>'0',//是否货到付款 1-是 0-否--------------------------
			"createTime"       =>date("Y-m-d H:i:s",time()),//创建时间
			"lastUpdateTime"   =>$data['add_time'],//最后修改时间----------------------------
				"soEntryJsons"     =>$arr,//销售订单物品清单
			),
		);
		$post_data['restStr']    =  json_encode($a);
		$result=sign($post_data,$secret);

		$post_data['authCode']   =   $result['str'];
		$post=$result['post'].'authCode='.$post_data['authCode'];
		// curl 方法
		$ch = curl_init();
		curl_setopt ( $ch, CURLOPT_URL, $uri );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		//curl_setopt ( $ch, CURLOPT_HEADER, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post );
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Expect:')); 
		$return = curl_exec ( $ch );
		curl_close ( $ch );
		$orders = json_decode($return);
		return $orders;
		// echo '<pre>';
		// var_dump($orders);exit;

}
function sign($data,$secret){
		ksort($data);
		$result['str'] =$secret;
		$result['post']='';
		if($data){
			foreach($data as $key =>$v){
				$v=urldecode($v);
				$result['str'].=  $key.$v;
				if($key=='restStr')
					$v=urlencode($v);
				$result['post'].=  $key.'='.$v.'&';
			}
		}
		$result['str'].=$secret;
		$result['str']=strtoupper(md5($result['str'],false));//md5加密 2进制转为16进制 转为大写字母
		return $result;
	}

///////////////////////////////////////////////////////////////////////////////
/**
 * 操作成功或失败跳转页面
 * 
 */
function jump($status,$message,$url){
	$heredoc = <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转页面</title>
</head>
<body>
	<div style="width:700px;margin:0 auto;margin-top:50px;">
		<div style="150px;float:left;">
			<img src="__PUBLIC__/Admin/images/$status.png" style="width:150px;"/>
		</div>
		<div style="width:530px;float:left;margin-left:20px;margin-top:10px;">
			<div style="width:550px;">
				<span style="font-size:22px;font-weight:bold;color:#2b2b2b;">操作提示</span>
			</div>
			<div style="width:550px;margin-top:5px;">
				<span style="line-height:40px;height:40px;font-size:15px;color:#717678;">$message</span>
			</div>
			<div style="background:#eb5409;border:1px solid #eb5409;border-radius:3px;width:100px;height:25px;line-height:25px;text-align:center;margin-top:30px;">
				<a href="$url" style="color:#fff;font-size:18px;display:bold;text-decoration:none;">返回</a>
			</div>
			<div style="clear:both;">
		</div>
		<div style="clear:both;">
		</div>
			</div>
	</div>
</body>
</html>
    		
	
END;
	return $heredoc;
}


/**
 * 危险 HTML代码过滤器
 *
 * @param   string  $html   需要过滤的html代码
 *
 * @return  string
 */
function html_filter($html)
{
	$filter = array(
			"/\s/",
			"/<(\/?)(script|i?frame|style|html|body|title|link|\?|\%)([^>]*?)>/isU",//object|meta|
			"/(<[^>]*)on[a-zA-Z]\s*=([^>]*>)/isU",
	);

	$replace = array(
			" ",
			"&lt;\\1\\2\\3&gt;",
			"\\1\\2",
	);

	$str = preg_replace($filter,$replace,$html);
	return $str;
}

/*打印*/
function vdump($data,$a='',$c='',$b='',$d='',$e='',$f=''){
	echo "<pre />"; 
	if(empty($a)){
		var_dump($data);exit;
	}else{
		var_dump($data,$a,$c,$b,$d,$e,$f);exit;
	}
}

/**
 *  导出excel表格
 *  $list['data']  要导出的数据，数据为索引数组
 *  例: $list['data']['0'] = array(a,b,c,d);
 *  $list['key']   列标题，数据为索引数组
 *  例: $list['key'] = array('优惠券号', '优惠券密码', '优惠券金额', '开始时间');
 *  $list['width'] 设置列宽，数据为关联数组
 *  例: $list['width'] = array('B'=>'15','C'=>'15','D'=>'15','E'=>'20');
 *  $list['mergeCells'] 合并单元格，数据为索引数组
 *  例: $list['mergeCells'] = array("A2:I2")
 *  $list['setRowHeight'] 设置行高，数据为关联数组
 *  例: $list['mergeCells'] = array('1'=>'15','2'=>'15','3'=>'15',)
 *  $name 导出的excel表格的名称
 **/
function exl($list,$name=null){
    $charactors = array('B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA');
    //导入PHPExcel类库
    //相当于引入了vendor目录下面PHPExcel\PHPExcel.php
    vendor('Excel.PHPExcel');
    $objPHPExcel = new \PHPExcel();
    
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $name)
        ->setCellValue('A2', 'ID');

    if (!empty($list['setRowHeight'])){
    	foreach ($list['setRowHeight'] as $k => $v) {
            $objPHPExcel->getActiveSheet()->getRowDimension($k)->setRowHeight($v);//设置行高
        }
    }    
    if (!empty($list['mergeCells'])) {
    	 foreach ($list['mergeCells'] as $k => $v) {
            $objPHPExcel->getActiveSheet()->mergeCells($v);//合并单元格
        }
    }

    if (!empty($list['key'])) {
        for ($i=0; $i < count($list['key']) ; $i++) { 
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($charactors[$i].'2', $list['key'][$i]);
        }
    }

    if (!empty($list['width'])) {
        foreach ($list['width'] as $k => $v) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth($v);//设置列宽
        }
    }

    foreach($list['data'] as $k=>$v){
        $k = $k+3;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".$k, $k-2);
        for ($i=0; $i <count($v) ; $i++) { 
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($charactors[$i].$k, $v[$i]);
        }
    }

    $objPHPExcel->getActiveSheet()->setTitle("$name"); 
    $objPHPExcel->setActiveSheetIndex(0);   
    ob_end_clean() ;
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename='.$name.'列表.xls');
    header('Cache-Control: max-age=0');
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}


function exls($list,$name=null,$str=null,$price=null){
    $charactors = array('B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA');
    //导入PHPExcel类库
    //相当于引入了vendor目录下面PHPExcel\PHPExcel.php
    vendor('Excel.PHPExcel');
    $objPHPExcel = new \PHPExcel();
    
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $name)
        ->setCellValue('A2', $str)
        ->setCellValue('A3', $price)
        ->setCellValue('A4', 'ID');

    if (!empty($list['setRowHeight'])){
    	foreach ($list['setRowHeight'] as $k => $v) {
            $objPHPExcel->getActiveSheet()->getRowDimension($k)->setRowHeight($v);//设置行高
        }
    }    
    if (!empty($list['mergeCells'])) {
    	 foreach ($list['mergeCells'] as $k => $v) {
            $objPHPExcel->getActiveSheet()->mergeCells($v);//合并单元格
        }
    }

    if (!empty($list['key'])) {
        for ($i=0; $i < count($list['key']) ; $i++) { 
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($charactors[$i].'4', $list['key'][$i]);
        }
    }

    if (!empty($list['width'])) {
        foreach ($list['width'] as $k => $v) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth($v);//设置列宽
        }
    }

    foreach($list['data'] as $k=>$v){
        $k = $k+5;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".$k, $k-4);
        for ($i=0; $i <count($v) ; $i++) { 
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($charactors[$i].$k, $v[$i]);
        }
    }

    $objPHPExcel->getActiveSheet()->setTitle("$name"); 
    $objPHPExcel->setActiveSheetIndex(0);   
    ob_end_clean() ;
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename='.$name.'列表.xls');
    header('Cache-Control: max-age=0');
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}

