<?php
namespace Home\Model;
use Think\Model;


class OrderModel extends Model {
	protected $trueTableName = 'xgj_furnish_order_info';

	//获取当前用户的购物车
    public function getCartByCartId($cart_id){
        $cartPrice=0;
    	foreach ($cart_id as $k => $v) {
            $data                       =M('xgj_furnish_cart c')->where("c.cart_id=$k")->order('c.class')->find();
            $a=count($data);
            if(!empty($a)){
                $info['list'][$k]           =$data;
                $info['list'][$k]['number'] =$v;
                $info['house_id']           =$data['house_id'];
                $info['class']              =$data['class'];
                if($data['class']==9){
                    if($v>1){
                        $cartPrice += ($data['price']+$data['price']*($v-1)*0.9);
                    }else{
                        $cartPrice += $data['price']*$v;
                    }
                }else{
                    $cartPrice += $data['price']*$v;
                }
                $info['total']              =$cartPrice;
                $info['total_num']          +=$v;
                
                $info['area']               =$data['area'];
                $info['total_area']         =$data['total_area'];
                $info['city']               =$data['city'];
                $info['house_layout']       =$data['house_layout'];
                $info['house_type']         =$data['house_type'];  
                $info['people']             =$data['people'];
                
                $info['coupon']             +=(($data['price']-$data['cost'])*0.1*$v);
                $info['zongshu']            +=$v;
            }
        }
        //var_dump($info);exit;
    	return $info;
    }
    
    //统计当前系统图片
    public function getFuImage($cat_id){
        $img=M('xgj_furnish_quote')->where("quote_id = '{$cat_id}'")->limit('1')->getField('img');
        return $img;
    }

    //统计当前商品图片
    public function getEuImage($cat_id){
        $img=M('xgj_eu_goods_new')->where("id = '{$cat_id}'")->limit('1')->getField('img');
        return $img;
    }

    /**
     * 从购物车中删除商品 ,支持未登陆和已登陆, 支持单个删除和批量删除
     * @param  mixed $id 购物车id
     * @return [type]     [description]
     */
    public function delHomeCartRow($cart_id){
        $user_id = $_SESSION['user']['userId'];
        $res=M('xgj_furnish_cart')->where("cart_id = '{$cart_id}'  and user_id = '{$user_id}'")->delete();
        return $res;
    }

    /*添加优惠券使用流水*/
    public function addCouponInfo($useMoney,$orderId){
        $data = array(
            'order_id'=>$orderId,
            'user_id'=>$_SESSION['user']['userId'],
            'use_money'=>$useMoney,
            'use_time'=>time(),
            'class_id'=>'1'
            );
        $re = M('xgj_coupon_info')->data($data)->add();
        return $re;
    }

    /**
    下订单
     */
    public function placeOrder(){
        $user_id                 = $_SESSION['user']['userId'];
        $cart_id                 = $_SESSION['user']['cart'];
        if(empty($cart_id)){
            $this->error = '购物车中没有该商品';
            return false;
        }
        $cart_list               = $this->getCartByCartId($cart_id);
        //var_dump($cart_list);exit;
        //组装数据
        $microtime               = explode('.',microtime(true));
        $addr_id                 = I('addr_id');
        $dikou                   = I('dikou');
        $house_id                = I('post.house_id');
        $total_price             = I('post.total_price');
        $coupon                  = I('post.coupon');
        $data['pay_method']      = I('pay_method');                  //支付方式, 默认支付宝
        //$data['is_invo']       = I('is_receipt',true,0);                  //是否开发票, 默认不开
        $data['user_id']         = $user_id;                                //用户id
        $data['d_id']            = 0;                                       //经销商id默认为0
        $data['house_id']        = $house_id;                               //房子id
        $data['order_code']      = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);//订单编号
        $shr_info                = M('xgj_address')->where(array('a_id'=>$addr_id))->find();     //收货人信息
        $data['consignee']       = $shr_info['a_name'];                     //收货人姓名
        $data['home_phone']      = $shr_info['a_phone'];                    //收货人电话号码
        $data['mobile_phone']    = $shr_info['a_mobile_phone'];             //收货人手机号码
        $data['address']         = $shr_info['a_addr'];                     //收货人详细地址
        $data['province']        = $shr_info['a_pro'];                      //收货人省份
        $data['city']            = $shr_info['a_city'];                     //收货人市区
        $data['district']        = $shr_info['a_area'];                     //县城/区
        $data['pay_status']      = 0;                                       //是否已支付
        $data['is_comment']      = 0;                                       //是否已评论
        $data['order_status']    = 0;                                       //订单状态 0:等待付款
        $data['shipping_status'] = 0;                                       //发货状态 0:未发货
        $data['return_status']   = 0;                                       //退货状态 0:未申请
        $data['goods_amount']    = $cart_list['total'];                     //订单总价
        $data['coupon']          = $coupon;                                 //使用的优惠券金额
        $data['add_order_time']  = time();                                  //下单时间
        // if($data['is_invo']   ==1){
        // $data['inv_payee']    = I('receipt_name');                       //发票抬头
        // }
        $data['schedule_status'] = 1;                                            //进度状态
        $data['allot_status']    = 0;                                            //分配状态
        $houseData               = M('xgj_users_houses')->where(array('house_id'=>$house_id))->find();
        //var_dump($houseData);exit;
        // $data['house_layout']    = $houseData['layout'];           //房子布局
        // $data['total_area']      = $houseData['total_area'];           //房子总面积
        // $data['type_area']       = $houseData['area'];           
        $data['area']            = $cart_list['area'];//每个房间面积
        $data['total_area']      = $cart_list['total_area'];
        $data['house_city']      = $cart_list['city'];
        $data['house_layout']    = $cart_list['house_layout'];
        $data['house_type']      = $cart_list['house_type'];              
        $data['people']          = $cart_list['people'];


        $userModel= new \Home\Model\UserModel;
        //开启事务, 加锁 //防止高并发下单
        $fp = fopen(__ROOT__.'/order.lock','r');
        flock($fp, LOCK_EX);//排他锁
        M()->startTrans();

        //插入订单数据
        $order_id = $this->data($data)->add();

        //插入订单商品数据
        if($order_id !== false){
            $info['order_id'] = $order_id;
            foreach ($cart_list['list'] as $key => $cart) {
                $int=M('xgj_furnish_quote')->where(array('quote_id'=>$cart['cat_id']))->getField('gift');
                $info['quote_id']     = $cart['cat_id'];
                $info['quote_name']   = $cart['shop_name'];
                $info['level']        = $cart['homebill_num'];
                $info['quote_status'] = 1;
                $info['stuff_goods']  = $cart['material'];
                $info['quote_price']  = $cart['price'];
                $info['cost']         = $cart['cost'];
                $info['plan_settle']  = 0;
                $info['status']       = 0; 
                $info['num']          = $cart['number']; 
                /*系统总金额-系统的优惠金额*返回积分的比例得到返还积分数值*/
                $info['integral']     = ($cart['price']*$cart['num']-($cart['price']-$cart['cost'])*0.1*$cart['num'])*$int/100;                 
                if(M('xgj_furnish_order_detail')->data($info)->add()==false){
                    //下订单失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    M()->rollBack();
                    $this->error = '订单详情添加失败';
                    return false;
                }
            }
            if(!empty($coupon)){
                //获取用户的优惠券
                $coupon_total=$userModel->getCouponByUserId($user_id);
                if ($coupon<=$coupon_total) {
                    $cou=$coupon_total-$coupon;//用户所剩余的优惠券
                    $useMoney = $coupon;//使用的优惠券
                }
                //添加优惠券使用详情
                if($this ->addCouponInfo($useMoney,$order_id)==false){
                    //添加优惠券使用详情失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    M()->rollBack();
                    $this->error = '添加优惠券使用详情失败';
                    return false;
                }

                if ($userModel->editconpon($cou,$user_id)==false) {
                    //修改优惠券金额失败, 需要回滚
                    flock($fp,LOCK_UN);
                    fclose($fp);
                    M()->rollBack();
                    $this->error = '修改用户优惠券金额失败';
                    return false;
                }
            }           
            /*跟单人*/
            $da['user_id']                            =$user_id;
            $da['order_id']                           =$order_id;
            $da['add_time']                           =time();
            if(M('xgj_documentary')->data($da)->add() ==false){
                $this->error                          = '添加跟单信息失败';
                return false;
            };
        }else{
            //订单插入失败, 需要回滚
            flock($fp,LOCK_UN);
            fclose($fp);
            M()->rollBack();
            $this->error = '订单添加失败';
            return false;
        }

        //删除购物车中相应的商品
        $str='';
        foreach ($cart_list['list'] as $key => $cart) {
            $str.=$cart['cart_id'].',';
        }
        $where=rtrim($str,',');
        M('xgj_furnish_cart')->where("cart_id in ($where)")->delete();
        flock($fp,LOCK_UN);
        fclose($fp);
        M()->commit();
        return $order_id;
    }

    /*
    支付订单,跳转到支付宝
     */
    public function alipay($order_id,$class_id){
        //查询出订单信息
        $order_info = M('xgj_furnish_order_info')->where(array('order_id'=>$order_id))->find();
        //查询出订单号
        $order_code = $order_info['order_code'];
        $out_trade_no = $order_code.'0'.$class_id;
        //订单详情系统信息
        $quote_info=M('xgj_furnish_order_detail d')->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
        //付款金额 (分)
        $total_price='';
        foreach ($quote_info as $k=>$v){
            if($class_id==3){
               $total_price += $v['quote_price']*$v['ecprice']/100;
            }elseif($class_id==4){
               $total_price += $v['quote_price']*(100-$v['ecprice'])/100;
            }elseif($class_id==5){
               $total_price += $v['quote_price']*$v['ecprice']/100;
            }elseif($class_id==6){
               $total_price += $v['quote_price'];
            }elseif($class_id==7){
               $total_price += $v['quote_price'];
            } 
        }

        if($class_id==2){
            $total_fee = 500;
        }elseif($class_id==3){
           $total_fee = ceil($total_price);
        }elseif($class_id==4){
           $total_fee = ceil($total_price-500);
        }elseif($class_id==5){
           $total_fee = ceil($total_price+500);
        }elseif($class_id==6){
           $total_fee = ceil($total_price-500);
        }elseif($class_id==7){
           $total_fee = ceil($total_price);
        } 
        if($_POST['pay_method']==1){
            $total_fee = 0.01;
            $baseurl = 'http://'.$_SERVER['HTTP_HOST'];
            $args=array(
                'out_trade_no'       =>$out_trade_no,// 商户订单号
                'notify_url'         =>$baseurl.U('Pay/notifyurl'),//'/spss/index.php/Pay/notifyurl.html',// 异步跳转处理
                'return_url'         =>$baseurl.U('Pay/returnurl'),//'/spss/index.php/Pay/returnurl.html',// 同步跳转处理
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
        include(dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Chinapay/util/common.php");//dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/common.php');
         //查询订单信息
        $order_info  = M('xgj_furnish_order_info')->where(array('order_id'=>$order_id))->find();
        //查询出订单号
        $order_code  = $order_info['order_code'];
        //查询出订单生成时间
        $order_time  = $order_info['add_order_time'];
        /**************************请求参数**************************/
        //商户号 15位数字     
        $data['MerId'] = '531111608080002';
        //必填，不能修改
        //商户订单号
        $data['MerOrderNo'] = substr($order_code,4,16).'0'.$class_id;
        //商户网站订单系统中唯一订单号，必填，16位数字
        //系统id
        $quote_info=M("xgj_furnish_order_detail d")->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
        //付款金额 (分)
        $total_price='';
        foreach ($quote_info as $k=>$v){
            if($class_id==3){
               $total_price += $v['quote_price']*$v['ecprice']/100;
            }elseif($class_id==4){
               $total_price += $v['quote_price']*(100-$v['ecprice'])/100;
            }elseif($class_id==5){
               $total_price += $v['quote_price']*$v['ecprice']/100;
            }elseif($class_id==6){
               $total_price += $v['quote_price'];
            }elseif($class_id==7){
               $total_price += $v['quote_price'];
            } 
        }

        if($class_id==2){
            $total_fee = 500;
        }elseif($class_id==3){
           $total_fee = ceil($total_price);
        }elseif($class_id==4){
           $total_fee = ceil($total_price-500);
        }elseif($class_id==5){
           $total_fee = ceil($total_price+500);
        }elseif($class_id==6){
           $total_fee = ceil($total_price-500);
        }elseif($class_id==7){
           $total_fee = ceil($total_price);
        } 
        //$total_fee=0.01;
        $data['OrderAmt'] = $total_fee*100;
       // $data['OrderAmt'] = 1;
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
        $data['MerBgUrl']="http://{$_SERVER['HTTP_HOST']}".U('Pay/bgReturn');///spss/index.php/Pay/bgReturn.html";
        //页面应答接收URL
        $data['MerPageUrl']="http://{$_SERVER['HTTP_HOST']}".U('Pay/pgReturn');///spss/index.php/Pay/pgReturn.html";
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
        include(dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Nopay/util/common.php");
        //查询订单信息
        $order_info  = M('xgj_furnish_order_info')->where(array('order_id'=>$order_id))->find();
        //查询出订单号
        $order_code  = $order_info['order_code'];
        //查询出订单生成时间
        $order_time  = $order_info['add_order_time'];
        /**************************请求参数**************************/
        //商户号 15位数字
        $data['MerId'] = '531111608080003';
        //必填，不能修改
        //商户订单号
        $data['MerOrderNo'] = substr($order_code,4,16).'0'.$class_id;
        //商户网站订单系统中唯一订单号，必填，16位数字
        //系统信息
        $quote_info=M("xgj_furnish_order_detail d")->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
        //付款金额 (分)  
        $total_price='';
        foreach ($quote_info as $k=>$v){
            if($class_id==3){
               $total_price += $v['quote_price']*$v['ecprice']/100;
            }elseif($class_id==4){
               $total_price += $v['quote_price']*(100-$v['ecprice'])/100;
            }elseif($class_id==5){
               $total_price += $v['quote_price']*$v['ecprice']/100;
            }elseif($class_id==6){
               $total_price += $v['quote_price'];
            }elseif($class_id==7){
               $total_price += $v['quote_price'];
            } 
        }

        if($class_id==2){
            $total_fee = 500;
        }elseif($class_id==3){
           $total_fee = ceil($total_price);
        }elseif($class_id==4){
           $total_fee = ceil($total_price-500);
        }elseif($class_id==5){
           $total_fee = ceil($total_price+500);
        }elseif($class_id==6){
           $total_fee = ceil($total_price-500);
        }elseif($class_id==7){
           $total_fee = ceil($total_price);
        } 
        // $total_fee=0.01;
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
        $data['MerBgUrl']="http://{$_SERVER['HTTP_HOST']}".U('Pay/bgNoReturn');//spss/index.php/Pay/bgNoReturn.html";
        //页面应答接收URL
        $data['MerPageUrl']="http://{$_SERVER['HTTP_HOST']}".U('Pay/pgNoReturn');//spss/index.php/Pay/pgNoReturn.html";
        //商户私有域,商户自定义，ChinaPay原样返回
        $data['MerResv']=$class_id;
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



    /*******************************************************退款接口********************************************************/
    public function alipayRefund($sn,$order_id){
        $info=M('xgj_eu_payment')->field('trade_no,order_id,system_id,total_fee')->where(array('order_id'=>array('in',$sn)))->select();
        
        if($info){
            $baseurl = 'http://'.$_SERVER['HTTP_HOST'];
            $microtime               = explode('.',microtime(true));
            $WIDdetail_data='';
            foreach ($info as $k=>$v){
                $WIDdetail_data.=$v['trade_no'].'^'.$v['total_fee'].'^'.'协商退款'.'#';
            }
            $WIDbatch_num=substr_count($WIDdetail_data,'#');
            $args=array(
                'notify_url'     =>$baseurl.U('Refund/notifyurl'),// 异步跳转处理
                'WIDbatch_no'    =>date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT),// 订单名称
                'WIDbatch_num'   =>$WIDbatch_num, 
                'WIDdetail_data' =>rtrim($WIDdetail_data,'#'), 
                'order_id'       =>$order_id,
                'class_id'       =>$info[0]['system_id'],
                );
            //下单成功, 跳到支付宝
            vendor('Alipay.Alipay');
            $obj= new \Alipay();
            $obj->refund($args);
        }
    }

    public function chinaPayRefund($sn,$order_id){
        //银联退款配置
        header('Content-Type:text/html;charset=utf-8 ');
        ini_set('date.timezone','Asia/Shanghai');
        include(dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Chinapay/util/common.php");
        //查询订单信息
        $order_info  = M('xgj_chinapay')->where(array('order_id'=>$sn))->find();
       // var_dump('sdfgsd',$order_info);exit;
        $microtime               = explode('.',microtime(true));
        //查询出订单号
        $order_code  = $order_info['order_id'];
        $class_id=$order_info['class_id'];
        /**************************请求参数**************************/
        //商户号 15位数字     
        $data['MerId'] = '531111608080002';
        //必填，不能修改
        //商户订单号
        $data['MerOrderNo'] = date('YmdHis').str_pad($microtime[1],2,0,STR_PAD_LEFT);
        //原交易订单号
        $data['OriOrderNo'] = substr($order_code,4,16);
        //商户网站订单系统中唯一订单号，必填，16位数字
        //退款日期   (8位数字，为订单提交日期)  必填
        $data['TranDate']=date('Ymd',time());
        //退款时间   (6位数字，为订单提交时间)  必填
        $data['TranTime']=date('His',time());
        //交易类型  (4位数字，固定值：0401)  必填
        $data['TranType']='0401';
        //业务类型  (4位数字，固定值：0001)  必填
        $data['BusiType']='0001';
        //版本号 必填 (8位数字，支付接口版本号) 
        $data['Version']='20140728';
        //后台应答接收URL
        $data['MerBgUrl']="http://{$_SERVER['HTTP_HOST']}".U('Refund/bgReturn');///spss/index.php/Pay/bgReturn.html";
        //自定义参数
        $data['MerResv'] = $order_id.','.$class_id;
        //退款金额
        $data['RefundAmt'] = $order_info['total_fee']*100;
        //必填
        //交易日期   (8位数字，为订单提交日期)  必填
        $data['OriTranDate']=$order_info['tran_date'];
        //表单
        $sHtml="<form name='createOrder' action=".U('Pay/sign')." method='POST'>";
        $params = "TranReserved;MerBgUrl;BusiType;CurryNo;MerSplitMsg;MerId;AccessType;AcqCode;SplitType;Signature;TranDate;TranTime;OriTranDate;TranType;Version;MerResv;SplitMethod;MerOrderNo;OriOrderNo;RefundAmt";
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

    public function noPayRefund($sn,$order_id){
        header('Content-Type:text/html;charset=utf-8 ');
        ini_set('date.timezone','Asia/Shanghai');
        include(dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Chinapay/util/common.php");
        //查询订单信息
        $order_info  = M('xgj_chinapay')->where(array('order_id'=>$sn))->find();
        //var_dump($order_info);exit;
        $microtime               = explode('.',microtime(true));
        /**************************请求参数**************************/
        //商户号 15位数字     
        $data['MerId'] = '531111608080003';
        //必填，不能修改
        //商户订单号
        $data['MerOrderNo'] = date('YmdHis').str_pad($microtime[1],2,0,STR_PAD_LEFT);
        //商户网站订单系统中唯一订单号，必填，16位数字
        //退款日期   (8位数字，为订单提交日期)  必填
        $data['TranDate']=date('Ymd',time());
        //退款时间   (6位数字，为订单提交时间)  必填
        $data['TranTime']=date('His',time());
        //交易类型  (4位数字，固定值：0401)  必填
        $data['TranType']='0401';   
        //业务类型  (4位数字，固定值：0001)  必填
        $data['BusiType']='0001';
        //版本号 必填 (8位数字，支付接口版本号) 
        $data['Version']='20140728';
        //后台应答接收URL
        $data['MerBgUrl']="http://{$_SERVER['HTTP_HOST']}".U('Refund/bgNoReturn');///spss/index.php/Pay/bgReturn.html";
        //退款金额
        $data['RefundAmt'] = $order_info['total_fee']*100;
        //原交易订单号    
        $data['OriOrderNo'] = substr($order_info['order_id'],4,16);
        //必填
        //交易日期   (8位数字，为订单提交日期)  必填
        $data['OriTranDate']=$order_info['tran_date'];
        //自定义属性
        $data['MerResv']=$order_id.','.$order_info['class_id'];
        //表单
        $sHtml="<form name='createOrder' action=".U('Pay/nosign')." method='POST' target='_blank'>";
        $params = "TranReserved;MerBgUrl;BusiType;CurryNo;MerSplitMsg;MerId;AccessType;AcqCode;SplitType;Signature;TranDate;TranTime;OriTranDate;TranType;Version;MerResv;SplitMethod;MerOrderNo;OriOrderNo;RefundAmt";
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