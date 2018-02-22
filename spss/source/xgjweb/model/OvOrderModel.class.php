<?php
require_once (WWW_DIR . "/libs/db.php");
/**
 * 订单模型
 * @date 2016-3-11
 * @author grass <14712905@qq.com>
 */

class OvOrderModel extends Model{
    private $tableName = 'xgj_ov_order';//该模型的表名
    public $error;//错误信息

    public function __construct(){
        parent::__construct($this->tableName);
    }

    /*
    获取地址信息
     */
    public function getAddress($default='0'){
        $user_id = session('userId');
        //按照default字段降序排列, 默认地址为第一个
        $sql = "SELECT * FROM xgj_address WHERE user_id={$user_id} AND `default`='{$default}' ORDER BY `default` DESC";
        $data = M()->fetchAll($sql);
        foreach ($data as $key => &$value) {
            $value['a_mobile_phone'] = processPhone($value['a_mobile_phone']);
        }
        return $data;
    }


    /*
    获取购物车数据
     */
    public function getCart($cart_id){
        $data = M('xgj_ov_cart')->where(array('id'=>array('in',$cart_id)))->select();

        /************这里的算法和购物车列表的算法一样*******************/
        $total_price = $goods_number = 0;
        $goods_list  = array();
        $da['youhuizong']=0;
        foreach ($data as $key => $cart) {
            $value                    = $this->field('*')->table('xgj_ov_goods')->find($cart['goods_id']);
            $da['youhuizong']+=($value['discount_amount'])*$cart['goods_num'];
            $value['goods_num']       = $cart['goods_num'];
            $value['goods_id']        = $cart['goods_id'];
            $value['attr_id']         = $cart['goods_attr_id'];
            $value['id']              = $cart['id'];
            $value['image']           = getImage($value['face_image'],100,100);
            //计算单个商品的总价
            $value['total_price'] = $cart['goods_num']*round($value['purchase'],1);
            $value['price']           = $value['purchase'];
            //统计商品总件数
            $goods_number += $value['goods_num'];
            //计算全部的总价
            $total_price += $value['total_price'];

            $goods_list[] = $value;
        }
        $goods_list[0]['youhuizong']=$da['youhuizong'];
        /************这里的算法和购物车列表的算法一样*******************/
        return array('cart_list'=>$goods_list,'total_price'=>$total_price,'goods_number'=>$goods_number);
    }

    /*立即购买*/
    public function getCartGoods($goods_id,$num=1){
            $value                    = $this->field('*')->table('xgj_ov_goods')->find($goods_id);
            $youhuizong=($value['discount_amount'])*$num;
            $value['goods_num']       = $num;
            $value['goods_id']        = $goods_id;
            $value['attr_id']         = '';
            $value['id']              = '';
            $value['image']           = getImage($value['face_image'],100,100);
            //计算单个商品的总价
            $value['total_price'] = round($value['purchase'],1)*$num;
            $value['price']           = $value['purchase'];
            //统计商品总件数
            $goods_number = $num;
            //计算全部的总价
            $total_price = $value['total_price'];

            $goods_list[] = $value;
            $goods_list[0]['youhuizong']=$youhuizong;
            /************这里的算法和购物车列表的算法一样*******************/
        return array('cart_list'=>$goods_list,'total_price'=>$total_price,'goods_number'=>$goods_number);
    }
    /**
     * 验证库存
     * @param  array $cart_id           [购物车id列表]
     * @param  array  &$return_cart_list [返回没有库存的购物车id列表]
     * @return boolean
     */
    public function checkStock($cart_id, &$return_cart_list=array()){
        $map['id'] = array('in',$cart_id);
        $cart_list = M('xgj_ov_cart')->where($map)->select();
        foreach ($cart_list as $key => $value) {
            if(!$this->_checkStock($value['goods_id'], $value['goods_attr_id'], $value['goods_num'])){
                $return_cart_list[] = $value;
            }
        }

        if(empty($return_cart_list)){
            return true;
        }else{
            return false;
        }

    }

    /*
    有库存返回true,否则false
     */
    public function _checkStock($goods_id, $goods_attr, $goods_number){
        $map['goods_id'] = $goods_id;
        $map['goods_attr_id'] = $goods_attr;
        $map['number'] = array('egt',$goods_number);
        if(M('xgj_ov_stock')->where($map)->select()){
            return true;
        }else{
            return false;
        }
    }


    /*
    下订单
     */
    public function placeOrder(){
        //接收值
        $cou= (float)I('coupon'); //优惠券
        $integral=(float)I('integral'); //积分
        //组装数据
        $microtime               = explode('.',microtime(true));
        $addr_id                 = I('addr_id');
        $data['pay_method']      = I('pay_method',true,1);               //支付方式, 默认支付宝
        $data['is_invo']         = I('is_receipt',true,0);               //是否开发票, 默认不开
        $data['user_id']         = session('userId');                     //用户id
        $data['sn']              = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);//订单编号
        $shr_info                = M('xgj_address')->where(array('a_id'=>$addr_id))->find();     //收货人信息
        $data['shr_name']        = $shr_info['a_name'];                  //收货人姓名
        $data['shr_tel']         = $shr_info['a_phone'];                 //收货人电话号码
        $data['shr_phone']       = $shr_info['a_mobile_phone'];          //收货人手机号码
        $data['shr_email']       = $shr_info['a_email'];                 //收货人邮箱
        $data['shr_addr']        = $shr_info['a_addr'];                  //收货人详细地址
        $data['shr_pro']         = $shr_info['a_pro'];                   //收货人省份
        $data['shr_city']        = $shr_info['a_city'];                  //收货人市区
        $data['shr_area']        = $shr_info['a_area'];                     //县城/区
        $data['is_pay']          = 0;                                       //是否已支付
        $data['order_status']    = 0;                                       //订单状态 0:等待付款
        $data['total_price']     = $_SESSION['ovorder']['total_price'];       //订单总价
        $data['total_goods_num'] = $_SESSION['ovorder']['goods_number'];      //订单商品总数量
        $data['add_time']        = date('Y-m-d H:i:s');                     //下单时间


        //开启事务, 加锁 //防止高并发下单
        $fp = fopen(WWW_DIR.'/ovorder.lock','r');
        flock($fp, LOCK_EX);//排他锁
        $this->pdo->beginTransaction();

        //处理积分优惠卷
        $pri = M()->fetch("SELECT coupon,integral FROM xgj_users WHERE user_id ={$_SESSION['userId']}");
        if(!empty($integral) && $integral<=$pri['integral']){
            $useint=$integral;    //使用积分金额
            $int=$pri['integral']-$integral;//剩余积分
        }else if($integral>$pri['integral']){
            $useint=$pri['integral'];  //积分金额
            $int=0;
        }else{
            $useint=0;
            $int=$pri['integral'];
        }

        if(!empty($cou) && $pri['coupon']>0 && $pri['coupon']>=$cou){
            $coupon=$pri['coupon']-$cou;//剩余优惠券
            $use_money=$cou;//使用优惠券
            $pic=bcadd($use_money,$useint,2);
            $data['deal_price']      = round(bcsub($_SESSION['ovorder']['total_price'],$pic,2),1);       //应付总价
        }elseif($pri['coupon']>0 && $pri['coupon']<$cou){
            $coupon=0;//剩余优惠券
            $use_money=$pri['coupon'];//使用优惠券
            $pic=bcadd($use_money,$useint,2);
            $data['deal_price']      = round(bcsub($_SESSION['ovorder']['total_price'],$pic,2),1);       //应付总价
        }else{
            $coupon=$pri['coupon'];
            $use_money=0;
            if($useint >= $_SESSION['ovorder']['total_price']){
                $data['deal_price']      = 0;
            }else
			  $data['deal_price']      = round(bcsub($_SESSION['ovorder']['total_price'],$useint,2),1);       //应付总价
        }
        //插入订单数据
        $order_id = $this->data($data)->add();

        //插入订单商品数据
        if($order_id !== false){
            $cart_list = $_SESSION['ovorder']['cart_list'];
            $info['order_id'] = $order_id;
            $info['user_id']  = session('userId');
            foreach ($cart_list as $key => $cart) {
                $info['goods_id']      = $cart['goods_id'];
                $info['goods_title']   = $cart['goods_title'];
                $info['goods_num']     = $cart['goods_num'];
                $info['goods_price']   = $cart['total_price'];
                $info['goods_attr']    = '';
                $info['goods_attr_id'] = $cart['attr_id'];
                $info['goods_image']   = $cart['face_image'];
                if(M('xgj_ov_order_goods')->data($info)->add()==false){
                    //下订单失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    $this->pdo->rollBack();
                    $this->error = '订单详情添加失败';
                    return false;
                } 
            }

            //分单
            $orderInfo = M()->fetchAll("SELECT id FROM xgj_ov_order_goods WHERE order_id=$order_id and user_id ={$_SESSION['userId']}");
            $info_['order_id'] = $order_id;
            $info_['user_id']  = session('userId');
            //var_dump($cart_list);exit;
            foreach ($orderInfo as $key => $cart) {
                $info_['split_sn']     = $data['sn'].'-'.$cart['id'];
                $info_['detail_id']     = $cart['id'];
                $info_['order_status'] = 0;

                if(M('xgj_ov_split_order')->data($info_)->add()==false){
                    //下订单失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    $this->pdo->rollBack();
                    $this->error = '分单信息添加失败';
                    return false;
                } 
            }
            //插入优惠券使用流水
            if(!empty($coupon)){
                $infos['order_id']  = $order_id;
                $infos['user_id']   = session('userId');
                $infos['use_money'] = $use_money;
                $infos['use_time']  = time();
                $infos['class_id']  = 3;
                if(M('xgj_coupon_info')->data($infos)->add()==false){
                    //下订单失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    $this->pdo->rollBack();
                    $this->error = '优惠券使用流水添加失败';
                    return false;
                }
            }
            if(!empty($integral)){
                $infos['order']  = $order_id;
                $infos['user_id']   = session('userId');
                $infos['user_name'] = session('userName');
                $infos['integral']  = $useint;
                $infos['class']  = 3;
                $infos['status'] = 1;
                $infos['time']  = time();
                if(M('xgj_user_integral')->data($infos)->add()==false){
                    //下订单失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    $this->pdo->rollBack();
                    $this->error = '积分使用流水添加失败';
                    return false;
                }
            }
        }else{
            //订单插入失败, 需要回滚
            flock($fp,LOCK_UN);
            fclose($fp);
            $this->pdo->rollBack();
            $this->error = '订单添加失败';
            return false;
        }

        $p = M('xgj_users')->where('user_id='.$_SESSION['userId'])->setField('integral',$int);
        $price = M('xgj_users')->where('user_id='.$_SESSION['userId'])->setField('coupon',$coupon);
        //删除购物车中相应的商品
        if(!empty($_SESSION['ovcart_id'])){
            $cart_id_list = $_SESSION['ovcart_id'];
            M('xgj_ov_cart')->where(array('id'=>array('in',$cart_id_list)))->delete();
        }
        flock($fp,LOCK_UN);
        fclose($fp);
        $this->pdo->commit();
        
        $return['order_id']=$order_id;
        $return['deal_price']=$data['deal_price'];
        return $return;
    }

    public function getError(){
        return $this->error;
    }

    /*
    支付订单,跳转到支付宝
     */
    public function payOrder($order_id){

        //导入支付宝必须文件
        
        require WWW_DIR.'/alipay/config.php';
        require WWW_DIR.'/alipay/lib/core.php';
        require WWW_DIR.'/alipay/lib/md5.php';
        require WWW_DIR.'/alipay/lib/Submit.class.php';

        //查询出订单总价
        $order       = M('xgj_ov_order')->find($order_id);
        $total_price = $order['deal_price'];
        $order_sn    = $order['sn'];

        /**************************请求参数**************************/

        //支付类型
        $payment_type = "1";
        //必填，不能修改

        //服务器异步通知页面路径
        $notify_url = C('WWW_URL')."alipay/notify_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = C('WWW_URL')."alipay/return_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //商户订单号
        $out_trade_no = $order_sn.'08';
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = C('AP_ORDER_NAME');
        //必填

        //付款金额
        $total_fee = $total_price;
        //必填

        //订单描述

        $body = C('AP_ORDER_DESC');
        //商品展示地址
        $show_url = '';
        //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1


        /************************************************************/

        //构造要请求的参数数组，无需改动
        $parameter = array(
                "service" => "create_direct_pay_by_user",
                "partner" => trim($alipay_config['partner']),
                "seller_email" => trim($alipay_config['seller_email']),
                "payment_type"  => $payment_type,
                "notify_url"    => $notify_url,
                "return_url"    => $return_url,
                "out_trade_no"  => $out_trade_no,
                "subject"   => $subject,
                "total_fee" => $total_fee,
                "body"  => $body,
                "show_url"  => $show_url,
                "anti_phishing_key" => $anti_phishing_key,
                "exter_invoke_ip"   => $exter_invoke_ip,
                "_input_charset"    => trim(strtolower($alipay_config['input_charset'])),
                'extra_common_param'=>'8',//用户自定义参数, 回调时会传回来. 用于区分家居和欧团
        );
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        echo '正在跳转到支付宝进行支付。。。';
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "立即跳转");
        echo $html_text;
    }

    /*
    支付订单,跳转到银联支付
     */
    public function chinaPayOrder($order_id){
        //var_dump($order_id,$class_id,$_SESSION);exit;
        //银联配置
        header('Content-Type:text/html;charset=utf-8 ');
        ini_set('date.timezone','Asia/Shanghai');
        include WWW_DIR.'/chinapay/util/common.php';
        // //查询出订单信息
        $order       = M('xgj_ov_order')->find($order_id);
        $total_price = $order['deal_price'];
        $order_code  = $order['sn'];
        $order_time    = strtotime($order['add_time']);
        /**************************请求参数**************************/
        //商户号 15位数字
        $data['MerId'] = '531111608080003';
        //必填，不能修改
		
        //商户订单号
        $data['MerOrderNo'] = substr($order_code,4,16).'08';
        //商户网站订单系统中唯一订单号，必填，16位数字

        //付款金额 (分)
        $data['OrderAmt'] = $total_price*100;
      //  $data['OrderAmt'] = 1;
        //必填
		
        //交易日期   (8位数字，为订单提交日期)  必填
        $data['TranDate']=date("Ymd",$order_time);
        //交易时间   (6位数字，为订单提交时间)  必填
        $data['TranTime']=date("His",$order_time);
        //业务类型  (4位数字，固定值：0001)  必填
        $data['BusiType']='0001';
        //版本号 必填 (8位数字，支付接口版本号) 
        $data['Version']='20140728';
        //后台应答接收URL
        $data['MerBgUrl']="http://{$_SERVER['HTTP_HOST']}/chinapay/bgReturn.php";
        //页面应答接收URL
        $data['MerPageUrl']="http://{$_SERVER['HTTP_HOST']}/chinapay/pgReturn.php";
        //商户私有域,商户自定义，ChinaPay原样返回
        $data['MerResv']=8;
        
        //表单
        $sHtml="<form name='createOrder' action='/chinapay/sign.php' method='POST'>";
        $params = "TranReserved;MerId;MerOrderNo;OrderAmt;CurryNo;TranDate;SplitMethod;BusiType;MerPageUrl;MerBgUrl;SplitType;MerSplitMsg;PayTimeOut;MerResv;Version;BankInstNo;CommodityMsg;Signature;AccessType;AcqCode;OrderExpiryTime;TranType;RemoteAddr;Referred;TranTime;TimeStamp;CardTranData";
        foreach ($data as $k => $v) {
            if (strstr($params, $k)) {
                $sHtml.= "<input type='hidden' name='".$k."' value='".$v."'/>";
            }
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='确认'></form>";

        $sHtml = $sHtml."<script>document.forms['createOrder'].submit();</script>";
        echo $sHtml;
    }
	
	/*
    支付订单,跳转到无卡支付
     */
    public function noPayOrder($order_id){
        //银联配置
        header('Content-Type:text/html;charset=utf-8 ');
        ini_set('date.timezone','Asia/Shanghai');
        include WWW_DIR.'/chinanopay/util/common.php';
        $order       = M('xgj_ov_order')->find($order_id);
        $total_price = $order['deal_price'];
        $order_code  = $order['sn'];
        $order_time    = strtotime($order['add_time']);
        /**************************请求参数**************************/
        //商户号 15位数字
        $data['MerId'] = '531111608080002';
        //必填，不能修改
		
        //商户订单号
        $data['MerOrderNo'] = substr($order_code,4,16).'08';
        //商户网站订单系统中唯一订单号，必填，16位数字

        //付款金额 (分)
        $data['OrderAmt'] = $total_price*100;
      //  $data['OrderAmt'] = 1;
        //必填
		$data['TranType']='0001';
        //交易日期   (8位数字，为订单提交日期)  必填
        $data['TranDate']=date("Ymd",$order_time);
        //交易时间   (6位数字，为订单提交时间)  必填
        $data['TranTime']=date("His",$order_time);
        //业务类型  (4位数字，固定值：0001)  必填
        $data['BusiType']='0001';
        //版本号 必填 (8位数字，支付接口版本号) 
        $data['Version']='20140728';
        //后台应答接收URL
        $data['MerBgUrl']="http://{$_SERVER['HTTP_HOST']}/chinanopay/bgReturn.php";
        //页面应答接收URL
        $data['MerPageUrl']="http://{$_SERVER['HTTP_HOST']}/chinanopay/pgReturn.php";
        //商户私有域,商户自定义，ChinaPay原样返回
        $data['MerResv']=8;
        
        //表单
        $sHtml="<form name='createOrder' action='/chinanopay/sign.php' method='POST'>";
        $params = "TranReserved;MerId;MerOrderNo;OrderAmt;CurryNo;TranDate;SplitMethod;BusiType;MerPageUrl;MerBgUrl;SplitType;MerSplitMsg;PayTimeOut;MerResv;Version;BankInstNo;CommodityMsg;Signature;AccessType;AcqCode;OrderExpiryTime;TranType;RemoteAddr;Referred;TranTime;TimeStamp;CardTranData";
        foreach ($data as $k => $v) {
            if (strstr($params, $k)) {
                $sHtml.= "<input type='hidden' name='".$k."' value='".$v."'/>";
            }
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='确认'></form>";

        $sHtml = $sHtml."<script>document.forms['createOrder'].submit();</script>";
        echo $sHtml;
    }

    function add_dealer($table,$data){
        $db = new db();
        $result = $db->add($table, $data);
        return $result;
    }
    
}