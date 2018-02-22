<?php
namespace Home\Model;
use Think\Model;


class SOrderModel extends Model {
	protected $trueTableName = 'xgj_s_order';

    /**
    下订单
     */
    public function placeOrder(){
        $OrderModel              = new \Home\Model\OrderModel;
        $user_id                 = $_SESSION['user']['userId'];
        $cart_id                 = $_SESSION['user']['cart'];
        $cart_list               = $OrderModel->getCartByCartId($cart_id);
        if(empty($cart_list) || count($cart_list['list'])<=0){
            $this->error = '操作失败，请核对后再提交！';return false;
        }
        //接收值
        $cou                     = (float)I('coupon'); //优惠券
        $integral                =(float)I('integral'); //积分
        //组装数据
        $microtime               = explode('.',microtime(true));//转化时间戳 
        $addr_id                 = I('addr_id');//地址
        $data['pay_method']      = I('pay_method');               //支付方式, 默认支付宝
        $data['user_id']         = $user_id;                     //用户id
        $data['sn']              = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);//订单编号
        $shr_info                = M('xgj_address')->where(array('a_id'=>$addr_id))->find();     //收货人信息
        $data['shr_name']        = $shr_info['a_name'];                  //收货人姓名
        $data['shr_tel']         = $shr_info['a_phone'];                 //收货人电话号码
        $data['shr_phone']       = $shr_info['a_mobile_phone'];          //收货人手机号码
        $data['shr_email']       = $shr_info['a_zipcode'];                 //收货人邮箱
        $data['shr_addr']        = $shr_info['a_addr'];                  //收货人详细地址
        $data['shr_pro']         = $shr_info['a_pro'];                   //收货人省份
        $data['shr_city']        = $shr_info['a_city'];                  //收货人市区
        $data['shr_area']        = $shr_info['a_area'];                       //县城/区
        $data['is_pay']          = 0;                                         //是否已支付
        $data['order_status']    = 0;                                         //订单状态 0:等待付款
        $data['total_price']     = $cart_list['total'];                       //订单总价
        $data['total_goods_num'] = $cart_list['total_num'];        //订单商品总数量
        $data['add_time']        = date('Y-m-d H:i:s',time());                       //下单时间
        //$data['is_comment']    = 0;                                       //是否已评论
        //$data['express_sn']    = '';                                      //快递编号
        //$data['post_status']   = 0;                                       //发货状态 0:未发货
        $data['return_status']   = 0;                                       //发货状态 0:未申请
        $data['class_id']        = $cart_list['class'];
        //开启事务, 加锁 //防止高并发下单
        $fp = fopen(__ROOT__.'/order.lock','r');
        flock($fp, LOCK_EX);//排他锁
        M()->startTrans();

        //处理积分优惠卷
        $pri = M("xgj_users")->field("coupon,integral")->where(array("user_id"=>$user_id))->find();

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
            //$data['deal_price']      = round(bcsub($cart_list['total'],$pic,2),1);       //应付总价
            $data['deal_price']      = bcsub($cart_list['total'],$pic,2);
        }elseif($pri['coupon']>0 && $pri['coupon']<$cou){
            $coupon=0;//剩余优惠券
            $use_money=$pri['coupon'];//使用优惠券
            $pic=bcadd($use_money,$useint,2);
            $data['deal_price']      = bcsub($cart_list['total'],$pic,2);       //应付总价
        }else{
            $coupon=$pri['coupon'];
            $use_money=0;
            if($useint >= $cart_list['total']){
                $data['deal_price']      = 0;
            }else{
                $data['deal_price']     = bcsub($cart_list['total'],$useint,2);       //应付总价
             }
        }
        //插入订单数据
        $order_id = $this->data($data)->add();
        //插入订单商品数据
        if($order_id !== false){
            $info['order_id'] = $order_id;
            $info['user_id']  = $user_id;
            $info['class_id']  = $cart_list['class'];
            foreach ($cart_list['list'] as $key => $cart) {
                $info['goods_id']      = $cart['cat_id'];
                $info['goods_title']   = $cart['shop_name'];
                $info['goods_num']     = $cart['number'];
                $info['goods_price']   = $cart['price'];
                $info['goods_image']   = $cart['img'];
                if(M('xgj_s_order_goods')->data($info)->add()==false){
                    //下订单失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    M()->rollBack();
                    $this->error = '订单详情添加失败';
                    return false;
                } 
            }

            if(!empty($cou)){
                $infos['order_id']  = $order_id;
                $infos['user_id']   = $user_id;
                $infos['use_money'] = $use_money;
                $infos['use_time']  = time();
                $infos['class_id']  = 1;
                if(M('xgj_coupon_info')->data($infos)->add()==false){
                    //下订单失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    M()->rollBack();
                    $this->error = '优惠卷使用流水添加失败';
                    return false;
                }
            }

            if(!empty($integral)){
                $infos['order']  = $order_id;
                $infos['user_id']   = $user_id;
                $infos['user_name'] = $_SESSION['user']['userName'];
                $infos['integral']  = $useint;
                $infos['class']  = 2;
                $infos['status'] = 1;
                $infos['time']  = time();
                if(M('xgj_user_integral')->data($infos)->add()==false){
                    //下订单失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    M()->rollBack();
                    $this->error = '积分使用流水添加失败';
                    return false;
                }
            }
        }else{
            //订单插入失败, 需要回滚
            flock($fp,LOCK_UN);
            fclose($fp);
            M()->rollBack();
            $this->error = '订单添加失败';
            return false;
        }
        
        $p = M('xgj_users')->where(array("user_id"=>$user_id))->setField('integral',$int);
        $price = M('xgj_users')->where(array("user_id"=>$user_id))->setField('coupon',$coupon);
        //删除购物车中相应的商品
        $key = array_keys($cart_id);
        $map['cart_id']  = array('in',$key);
        M('xgj_furnish_cart')->where($map)->delete();
        flock($fp,LOCK_UN);
        fclose($fp);
        M()->commit();

        $return['order_id']=$order_id;
        $return['class_id']=$cart_list['class'];
        $return['deal_price']=$data['deal_price'];
        return $return;
    }

    /*
    支付订单,跳转到支付宝
     */
    public function alipay($order_id,$class_id){
        //查询出订单总价
        $order        = M('xgj_s_order')->where(array('id'=>$order_id))->find();
        $total_fee    = $order['deal_price'];
        $order_code   = $order['sn'];
        $out_trade_no = $order_code.'0'.$class_id;
        if($_POST['pay_method']==1){
            //$total_fee = 0.01;
            $baseurl = 'http://'.$_SERVER['HTTP_HOST'];
            $args=array(
                'out_trade_no'       =>$out_trade_no,// 商户订单号
                'notify_url'         =>$baseurl.U('Pay/notifyurl'),// 异步跳转处理
                'return_url'         =>$baseurl.U('Pay/returnurl'),// 同步跳转处理
                'name'               =>C('AP_ORDER_NAME'),// 订单名称
                'total'              =>$total_fee,// 订单金额
                'content'            =>C('AP_ORDER_DESC'),// 订单描述
                'show_url'           =>$baseurl,// 商品展示地址
                'extra_common_param' =>$class_id,
                );
            //下单成功, 跳到支付宝
            vendor('Alipay.Alipay');
            $obj= new \Alipay();
            $obj->pay(C('alipay'),$args);
        }
    }

    /*
    支付订单,跳转到银联支付
     */
    public function chinaPayOrder($order_id,$class_id){
        //银联配置
        header('Content-Type:text/html;charset=utf-8 ');
        ini_set('date.timezone','Asia/Shanghai');
        include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/common.php');
        //查询出订单信息
        $order       = M('xgj_s_order')->where(array('id'=>$order_id))->find();        
        //查询出订单号
        $order_code  = $order['sn'];
        //查询出订单生成时间
        $order_time    = strtotime($order['add_time']);
        /**************************请求参数**************************/
        //商户号 15位数字     
        $data['MerId'] = '531111608080002';
        //必填，不能修改
        //商户订单号
        $data['MerOrderNo'] = substr($order_code,4,16).'0'.$class_id;
        //商户网站订单系统中唯一订单号，必填，16位数字
        $total_fee = $order['deal_price'];
        //$total_fee=0.01;
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
        $data['MerBgUrl']="http://{$_SERVER['HTTP_HOST']}".U('Pay/bgReturn');
        //页面应答接收URL
        $data['MerPageUrl']="http://{$_SERVER['HTTP_HOST']}".U('Pay/pgReturn');
        //商户私有域,商户自定义，ChinaPay原样返回
        $data['MerResv']=$class_id;
        
        //表单
        $sHtml="<form name='createOrder' action=".U('Pay/sign')." method='POST'>";
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
        //银联配置
        header('Content-Type:text/html;charset=utf-8 ');
        ini_set('date.timezone','Asia/Shanghai');
        include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Nopay/util/common.php');
        //查询出订单信息
        $order       = M('xgj_s_order')->where(array('id'=>$order_id))->find();        
        //查询出订单号
        $order_code  = $order['sn'];
        //查询出订单生成时间
        $order_time    = strtotime($order['add_time']);

        /**************************请求参数**************************/
        //商户号 15位数字
        $data['MerId'] = '531111608080003';
        //必填，不能修改
        //商户订单号
        $data['MerOrderNo'] = substr($order_code,4,16).'0'.$class_id;
        //商户网站订单系统中唯一订单号，必填，16位数字
        $total_fee = $order['deal_price'];
        //$total_fee=0.01;
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
        $data['MerBgUrl']="http://{$_SERVER['HTTP_HOST']}".U('Pay/bgNoReturn');
        //页面应答接收URL
        $data['MerPageUrl']="http://{$_SERVER['HTTP_HOST']}".U('Pay/pgNoReturn');
        //商户私有域,商户自定义，ChinaPay原样返回
        $data['MerResv']=$class_id;
        //var_dump($order_time,$order,$data);die;
        //echo '正在跳转至银联支付，请耐心等待...';
        //表单.
        $sHtml="<form name='createOrder' action=".U('Pay/nosign')." method='POST'>";
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