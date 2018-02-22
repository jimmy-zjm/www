<?php
/**
 * 订单模型
 * @date 2016-3-11
 * @author grass <14712905@qq.com>
 */

class FurnishOrderModel extends Model{
    private $tableName = 'xgj_furnish_order_info';//该模型的表名
    public $error;//错误信息

    public function __construct(){
        parent::__construct($this->tableName);
    }

    /*
    获取地址信息
     */
    public function getAddress($default='0'){
        $user_id = $_SESSION['userId'];
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
          //获取欧元转人民币的汇率
        if(isset($_SESSION['currency'])){
            $currency=$_SESSION['currency'];
        }else{
            $_SESSION['currency']=switch_money();
            $currency=$_SESSION['currency']; 
        }
        $data = M('xgj_eu_carts')->where(array('id'=>array('in',$cart_id)))->select();
        //var_dump($data);exit;
        /************这里的算法和购物车列表的算法一样*******************/
        $total_price = $goods_number = 0;
        $goods_list  = array();
        foreach ($data as $key => $cart) {
             $value                    = $this->field('*')->table('xgj_eu_goods_new')->find($cart['goods_id']);
             $value['goods_num']       = $cart['goods_num'];
             $value['goods_id']        = $cart['goods_id'];
             $value['attr_id']         = $cart['goods_attr_id'];
             $value['id']              = $cart['id'];
             $value['image']           = getImage($value['face_image'],100,100);
             $purchase                 =$value['purchase']*$currency;//采购价
             $duties                   =$purchase*$value['duties'];//关税价
             $arr                      =explode('-',$value['luggage']);
             $luggage1                 =$arr[0];//海运运费
             $luggage2                 =$arr[1];//空运运费
             $vat1                     =($purchase+$duties+$luggage1)*$value['vat'];//增值税1
             $service_charge1          =($purchase+$duties+$luggage1+$vat1)*$value['service_charge'];//服务费1
             $vat2                     =($purchase+$duties+$luggage2)*$value['vat'];//增值税2
             $service_charge2          =($purchase+$duties+$luggage2+$vat2)*$value['service_charge'];//服务费2
             $value['luggage1']        = $luggage1;
             $value['luggage2']        = $luggage2;
             $value['vat1']            = $vat1;
             $value['vat2']            = $vat2;
             $value['service_charge1'] = $service_charge1;
             $value['service_charge2'] = $service_charge2;
             $value['money']           = $purchase;
             $value['duties1']         = $duties;
             $value['total1']          = ceil($purchase+$duties+$luggage1+$vat1+$service_charge1);
             $value['total2']          = ceil($purchase+$duties+$luggage2+$vat2+$service_charge2);
            //开启了团购就是用团购价, 没有开启就是本店价格
            if($value['is_groupbuy']==1){
                $value['price'] = $value['groupbuy_price'];
            }else{
                $value['price'] = $value['shop_price'];
            }

            //处理属性: 拼接属性字符创, 累加属性价格
            $value['attr_str'] = '';
            if(!empty($cart['goods_attr_id'])){
                $sql = "SELECT * FROM xgj_eu_goods_attr WHERE id IN ({$cart['goods_attr_id']})";
                $attr_list = $this->fetchAll($sql);
                $attr_str = '';
                foreach ($attr_list as $v) {

                    //属性字符串
                    $attr_str .= $v['attr_value'] . '<br/>';

                    //商品属性价格大于0 就累加
                    if($v['attr_price']>0){
                        $value['price'] += $v['attr_price'];
                    }
                }
                $value['attr_str'] = $attr_str;
            }
            if($cart['express']==1){
                //计算单个商品的总价
                $value['total_price'] = ceil($cart['goods_num']*$value['total1']);
            }else if($cart['express']==2){
                //计算单个商品的总价
                $value['total_price'] = ceil($cart['goods_num']*$value['total2']);
            }
            
            //统计商品总件数
            $goods_number += $value['goods_num'];

            //计算全部的总价
            $total_price += $value['total_price'];

            $goods_list[] = $value;
        }
        /************这里的算法和购物车列表的算法一样*******************/
        return array('cart_list'=>$goods_list,'total_price'=>$total_price,'goods_number'=>$goods_number);
    }

    /*
    下订单
     */
    public function placeOrder(){
        //组装数据
        //var_dump($_SESSION['furnish_order']);exit;
            $microtime               = explode('.',microtime(true));
            $addr_id                 = I('addr_id');
            $dikou                   = I('dikou');
            $house_id                = I('post.house_id');
            $total_price             = I('post.total_price');
            $data['pay_method']      = I('pay_method',true,1);               //支付方式, 默认支付宝
            $data['is_invo']         = I('is_receipt',true,0);               //是否开发票, 默认不开
            $data['user_id']         = $_SESSION['userId'];                     //用户id
            $data['d_id']            = 0;                                          //经销商id默认为0
            $data['house_id']        = $house_id;                             //房子id
            $data['order_code']      = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);//订单编号
            $shr_info                = M('xgj_address')->where(array('a_id'=>$addr_id))->find();     //收货人信息
            $data['consignee']       = $shr_info['a_name'];                  //收货人姓名
            $data['home_phone']      = $shr_info['a_phone'];                 //收货人电话号码
            $data['mobile_phone']    = $shr_info['a_mobile_phone'];          //收货人手机号码
            $data['address']         = $shr_info['a_addr'];                  //收货人详细地址
            $data['province']        = $shr_info['a_pro'];//收货人省份
            $data['city']            = $shr_info['a_city'];//收货人市区
            $data['district']        = $shr_info['a_area'];//县城/区
            $data['pay_status']      = 0;                                       //是否已支付
            $data['is_comment']      = 0;                                       //是否已评论
            $data['order_status']    = 0;                                       //订单状态 0:等待付款
            $data['shipping_status'] = 0;                                       //发货状态 0:未发货
            $data['return_status']   = 0;                                       //退货状态 0:未申请
            $data['goods_amount']    = $_SESSION['furnish_order']['total_price']+$_SESSION['furnish_order']['cj_money'];       //订单总价
            $data['zp_money']        = $_SESSION['furnish_order']['zp_money'];       //订单总价
            $data['cj_money']        = $_SESSION['furnish_order']['cj_money'];       //订单总价
            $data['add_order_time']  = time();                     //下单时间
            if($data['is_invo']      ==1){
            $data['inv_payee']       = I('receipt_name');                       //发票抬头
            }
            $data['schedule_status'] =1;                                              //进度状态
            $data['allot_status']    =0;                                            //分配状态
            $houseData               =$this->fetch("select * from xgj_users_house where house_id=$house_id");
            //var_dump($houseData);exit;
            $data['house_layout']    =$houseData['house_layout'];           //房子布局
            $data['total_area']      =$houseData['total_area'];           //房子总面积
            $data['type_area']       =$houseData['type_area'];           //每个房间面积



        //开启事务, 加锁 //防止高并发下单
        $fp = fopen(WWW_DIR.'/order.lock','r');
        flock($fp, LOCK_EX);//排他锁
        $this->pdo->beginTransaction();

        //插入订单数据
        $order_id = $this->data($data)->add();

        //插入订单商品数据
        if($order_id !== false){
            $cart_list = $_SESSION['furnish_order']['cart_list'];
            $info['order_id'] = $order_id;
            foreach ($cart_list as $key => $cart) {
                $info['quote_id']     = $cart['cat_id'];
                $info['quote_name']   = $cart['shop_name'];
                $info['level']        = $cart['homebill_num'];
                $info['quote_status'] = 1;
                $info['stuff_goods']  = $cart['material'];
                $info['quote_price']  = $cart['price'];
                $info['cost']         = $cart['cost'];
                $info['plan_settle']  = 0;
                $info['status']       =0;                
                if(M('xgj_furnish_order_detail')->data($info)->add()==false){
                    //下订单失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    $this->pdo->rollBack();
                    $this->error = '订单详情添加失败';
                    return false;
                }
            }

            $zp_list = $_SESSION['zp_order']['cart_list'];
            $infos['order_id'] = $order_id;
            $infos['user_id']  = session('userId');
            if(!empty($zp_list)){
                foreach ($zp_list as $key => $cart) {
                    $infos['goods_id']      = $cart['goods_id'];
                    $infos['goods_title']   = $cart['goods_title'];
                    $infos['goods_num']     = $cart['goods_num'];
                    $infos['goods_price']   = $cart['price'];
                    $infos['goods_attr']    = $cart['attr_str'];
                    $infos['goods_attr_id'] = $cart['attr_id'];
                    $infos['goods_image']   = $cart['face_image'];
                    if(M('xgj_eu_order_goods_zp')->data($infos)->add()==false){
                        //下订单失败, 需要回滚
                        flock($fp,LOCK_UN);
                        fclose($fp);
                        $this->pdo->rollBack();
                        $this->error = '赠品详情添加失败';
                        return false;
                    }
                }
            }
            if (!empty($dikou) && $dikou == 1) {
                $price = M()->fetch("SELECT coupon FROM xgj_users WHERE user_id ={$_SESSION['userId']}");
                $coupon = $price['coupon'];
                $coupons = round($_SESSION['furnish_order']['yhq']);
                if ($coupon<=$coupons) {
                    $cou = '0';
                    $useMoney = $coupon;
                }else{
                    $cou = $coupon - $coupons;
                    $useMoney = $coupons;
                }

                //添加优惠券使用详情
                if($this ->addCouponInfo($useMoney,$order_id)==false){
                    //添加优惠券使用详情失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    $this->pdo->rollBack();
                    $this->error = '添加优惠券使用详情失败';
                    return false;
                }

                $editconpon =$this ->editconpon($cou);
                if (!empty($editconpon) && $editconpon != 1) {
                    //修改优惠券金额失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    $this->pdo->rollBack();
                    $this->error = '修改优惠券金额失败';
                    return false;
                }
            }

            $da['user_id']=$_SESSION['userId'];
            $da['order_id']=$order_id;
            $da['add_time']=time();
            if(M('xgj_documentary')->data($da)->add()==false){
                $this->error = '添加跟单信息失败';
                return false;
            };


        }else{
            //订单插入失败, 需要回滚
            flock($fp,LOCK_UN);
            fclose($fp);
            $this->pdo->rollBack();
            $this->error = '订单添加失败';
            return false;
        }

        //删除购物车中相应的商品
        $str='';
        foreach ($cart_list as $key => $cart) {
            $str.=$cart['cart_id'].',';
        }
        $where=rtrim($str,',');
        M('xgj_furnish_cart')->where("cart_id in ($where)")->delete();
        if(!empty($_SESSION['zp_id'])){
            $cart_id_list = $_SESSION['zp_id'];
            M('xgj_eu_carts')->where(array('id'=>array('in',$cart_id_list)))->delete();
        }
        flock($fp,LOCK_UN);
        fclose($fp);
        $this->pdo->commit();
        return $order_id;
    }

    public function getError(){
        return $this->error;
    }

    public function category($class_id){
        $data = M('xgj_eu_category')->where("class_id=$class_id and pid=0")->select();
        //后台三级分类 前台读取后两级分类
        $pid='';
        foreach ($data as $k=>$v){
            $pid .= ','.$v['id'];
        }
        $pid =ltrim($pid,',');
        $data1 = M('xgj_eu_category')->where("class_id=$class_id and pid in ( {$pid} )")->select();
        foreach ($data1 as $key=>$v1){
            
            $data1[$key]['list']=M('xgj_eu_category')->where("class_id=$class_id and pid={$v1['id']}")->select();
        }
        return $data1;
    }

    public function editconpon($coupon){
        $price = M('xgj_users')->where('user_id='.$_SESSION['userId'])->setField('coupon',$coupon);
        return $price;
    }

    public function addCouponInfo($useMoney,$orderId){
        $data = array(
            'order_id'=>$orderId,
            'user_id'=>$_SESSION['userId'],
            'use_money'=>$useMoney,
            'use_time'=>time(),
            'class_id'=>'2'
            );
        $re = M('xgj_coupon_info')->data($data)->add();
        return $re;
    }

    /*
    支付订单,跳转到支付宝
     */
    public function payOrder($order_id,$class_id){
        //var_dump($order_id,$class_id);exit;
        //导入支付宝必须文件
        require WWW_DIR.'/alipay/config.php';
        require WWW_DIR.'/alipay/lib/core.php';
        require WWW_DIR.'/alipay/lib/md5.php';
        require WWW_DIR.'/alipay/lib/Submit.class.php';

        //查询出订单信息
        $order_info = M('xgj_furnish_order_info')->where(array('order_id'=>$order_id))->find();
        //var_dump($order_id,$order_info);exit;
        //查询出订单总价(系统价格*$v['sale']/100+赠品差价)
        //$total_price=$order_info['goods_amount'];
        //查询出订单号
        $order_code = $order_info['order_code'];
        //查询出差价
        $cj_money=$order_info['cj_money'];
        //查询出赠品价格
        $zp_money = $order_info['zp_money'];

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
        $out_trade_no = $order_code.'0'.$class_id;
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = C('AP_ORDER_NAME');
        //必填

        //系统id
        $quote_info=M()->fetchAll("select * from xgj_furnish_order_detail d join xgj_furnish_quote q on d.quote_id=q.quote_id where d.order_id=$order_id");
        //var_dump($quote_info,$notify_url,$return_url);exit;
        //付款金额 (分)
        $total_price='';
        foreach ($quote_info as $k=>$v){
            if($class_id==3){
               $total_price += (($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'])*$v['ecprice']/100;
            }elseif($class_id==4){
               $total_price += (($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'])*(100-$v['ecprice'])/100;
            }elseif($class_id==5){
               $total_price += (($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'])*$v['ecprice']/100;
            }elseif($class_id==6){
               $total_price += ($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'];
            }elseif($class_id==7){
               $total_price += ($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'];
            } 
        }

        //付款金额 (分)
        if($class_id==2){
            $total_fee = 500;
        }elseif($class_id==3){
           $total_fee = ceil($total_price+$cj_money);
        }elseif($class_id==4){
           $total_fee = ceil($total_price-500);
        }elseif($class_id==5){
           $total_fee = ceil($total_price+500+$cj_money);
        }elseif($class_id==6){
           $total_fee = ceil($total_price+$cj_money-500);
        }elseif($class_id==7){
           $total_fee = ceil($total_price+$cj_money);
        } 
        //$total_fee = 0.02;
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
                "service"            => "create_direct_pay_by_user",
                "partner"            => trim($alipay_config['partner']),
                "seller_email"       => trim($alipay_config['seller_email']),
                "payment_type"       => $payment_type,
                "notify_url"         => $notify_url,
                "return_url"         => $return_url,
                "out_trade_no"       => $out_trade_no,
                "subject"            => $subject,
                "total_fee"          => $total_fee,
                "body"               => $body,
                "show_url"           => $show_url,
                "anti_phishing_key"  => $anti_phishing_key,
                "exter_invoke_ip"    => $exter_invoke_ip,
                "_input_charset"     => trim(strtolower($alipay_config['input_charset'])),
                'extra_common_param' =>$class_id,//用户自定义参数, 回调时会传回来. 用于区分家居和欧团
        );
//var_dump($parameter);exit;
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        echo '正在跳转至支付宝，请耐心等待...';
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }

    /*
    支付订单,跳转到银联支付
     */
    public function chinaPayOrder($order_id,$class_id){
        //var_dump($order_id,$class_id,$_SESSION);exit;
        //银联配置
        header('Content-Type:text/html;charset=utf-8 ');
        ini_set('date.timezone','Asia/Shanghai');
        include WWW_DIR.'/chinapay/util/common.php';
        //查询订单信息
        $order_info  = M('xgj_furnish_order_info')->where(array('order_id'=>$order_id))->find();
        //查询出订单总价
        //$total_price = $order_info['goods_amount'];
        //查询出订单号
        $order_code  = $order_info['order_code'];
        //查询出订单生成时间
        $order_time  = $order_info['add_order_time'];
        //查询出差价
        $cj_money    =$order_info['cj_money'];
        //查询出赠品价格
        $zp_money    = $order_info['zp_money'];
        /**************************请求参数**************************/
        //商户号 15位数字
        $data['MerId'] = '531111608080003';
        //必填，不能修改

        //商户订单号
        $data['MerOrderNo'] = substr($order_code,4,16).'0'.$class_id;
        //商户网站订单系统中唯一订单号，必填，16位数字


        //系统信息
        $quote_info=M()->fetchAll("select * from xgj_furnish_order_detail d join xgj_furnish_quote q on d.quote_id=q.quote_id where d.order_id=$order_id");
        //付款金额 (分)
        $total_price='';
        foreach ($quote_info as $k=>$v){
            if($class_id==3){
               $total_price += (($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'])*$v['ecprice']/100;
            }elseif($class_id==4){
               $total_price += (($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'])*(100-$v['ecprice'])/100;
            }elseif($class_id==5){
               $total_price += (($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'])*$v['ecprice']/100;
            }elseif($class_id==6){
               $total_price += ($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'];
            }elseif($class_id==7){
               $total_price += ($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'];
            } 
        }

        //付款金额 (分)
        if($class_id==2){
            $total_fee = 500;
        }elseif($class_id==3){
           $total_fee = ceil($total_price+$cj_money);
        }elseif($class_id==4){
           $total_fee = ceil($total_price-500);
        }elseif($class_id==5){
           $total_fee = ceil($total_price+500+$cj_money);
        }elseif($class_id==6){
           $total_fee = ceil($total_price+$cj_money-500);
        }elseif($class_id==7){
           $total_fee = ceil($total_price+$cj_money);
        } 

        $data['OrderAmt'] = $total_fee*100;
        //$data['OrderAmt'] = 1;
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
        $data['MerResv']=$class_id;
        
        //echo '正在跳转至银联支付，请耐心等待...';
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
    支付订单,跳转到银联无卡支付
     */
    public function noPayOrder($order_id,$class_id){
        //var_dump($order_id,$class_id,$_SESSION);exit;
        //银联配置
        header('Content-Type:text/html;charset=utf-8 ');
        ini_set('date.timezone','Asia/Shanghai');
        include WWW_DIR.'/chinanopay/util/common.php';
         //查询订单信息
        $order_info  = M('xgj_furnish_order_info')->where(array('order_id'=>$order_id))->find();
        //查询出订单总价
        //$total_price = $order_info['goods_amount'];
        //查询出订单号
        $order_code  = $order_info['order_code'];
        //查询出订单生成时间
        $order_time  = $order_info['add_order_time'];
        //查询出差价
        $cj_money    =$order_info['cj_money'];
        //查询出赠品价格
        $zp_money    = $order_info['zp_money'];
        /**************************请求参数**************************/
        //商户号 15位数字     
        $data['MerId'] = '531111608080002';
        //必填，不能修改

        //商户订单号
        $data['MerOrderNo'] = substr($order_code,4,16).'0'.$class_id;
        //商户网站订单系统中唯一订单号，必填，16位数字

        //系统id
        $quote_info=M()->fetchAll("select * from xgj_furnish_order_detail d join xgj_furnish_quote q on d.quote_id=q.quote_id where d.order_id=$order_id");
        //付款金额 (分)
        $total_price='';
        foreach ($quote_info as $k=>$v){
            if($class_id==3){
               $total_price += (($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'])*$v['ecprice']/100;
            }elseif($class_id==4){
               $total_price += (($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'])*(100-$v['ecprice'])/100;
            }elseif($class_id==5){
               $total_price += (($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'])*$v['ecprice']/100;
            }elseif($class_id==6){
               $total_price += ($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'];
            }elseif($class_id==7){
               $total_price += ($v['quote_price']-$v['cost'])*$v['sale']/100+$v['cost'];
            } 
        }

        //付款金额 (分)
        if($class_id==2){
            $total_fee = 500;
        }elseif($class_id==3){
           $total_fee = ceil($total_price+$cj_money);
        }elseif($class_id==4){
           $total_fee = ceil($total_price-500);
        }elseif($class_id==5){
           $total_fee = ceil($total_price+500+$cj_money);
        }elseif($class_id==6){
           $total_fee = ceil($total_price+$cj_money-500);
        }elseif($class_id==7){
           $total_fee = ceil($total_price+$cj_money);
        } 
        $data['OrderAmt'] = $total_fee*100;
        //$data['OrderAmt'] = 1;
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
        $data['MerBgUrl']="http://{$_SERVER['HTTP_HOST']}/chinanopay/bgReturn.php";
        
        //页面应答接收URL
        $data['MerPageUrl']="http://{$_SERVER['HTTP_HOST']}/chinanopay/pgReturn.php";
        
        //商户私有域,商户自定义，ChinaPay原样返回
        $data['MerResv']=$class_id;
        
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
}