<?php
namespace Mobile\Controller;

class OrderController extends BaseController {

    public function index(){
        $userId = $_SESSION['user']['userId'];

        $map = [
            'a.user_id'    =>$userId,
            'a.pay_status' =>2,
            // 'a.is_mobile'  =>2,  打开后只查询手机下单
        ];

        $data = M('xgj_furnish_order_info a')
              ->join('xgj_furnish_order_detail b on a.order_id = b.order_id')
              ->join('xgj_furnish_quote c on b.quote_id = c.quote_id')
              ->field('c.img,b.quote_name,b.num')->where($map)->select();

        $numAll = 0;
        foreach ($data as $v) {
            $numAll += $v['num'];
        }

        $this->assign('num',$numAll);
        $this->assign('list',$data);
    	$this->assign('header','个人订单');
    	$this->assign('name',$re);
        $this->display();
    }

    public function payaa(){
        $userId                  = $_SESSION['user']['userId'];
        $cid                     = I('get.cid');
        $house_id                = I('get.hid');
        $re                      = M('xgj_furnish_quote')->where(['quote_id'=>$cid])->find();
        $houseData               = M('xgj_users_houses')->where(['user_id'=>$userId,'house_id'=>$house_id,'is_mobile'=>2])->find();
        $UserData                = M('xgj_users')->where(['user_id'=>$userId])->find();
        //组装数据
        $microtime               = explode('.',microtime(true));
        $data['pay_method']      = 0;                                //支付方式, 默认支付宝
        $data['user_id']         = $userId;                                //用户id
        $data['d_id']            = 0;                                       //经销商id默认为0
        $data['house_id']        = $house_id;                               //房子id
        $data['order_code']      = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);//订单编号
        $data['consignee']       = $UserData['user_name'];                     //收货人姓名
        $data['home_phone']      = $UserData['home_phone'];                    //收货人电话号码
        $data['mobile_phone']    = $UserData['mobile_phone'];             //收货人手机号码
        $data['address']         = $houseData['address'];                     //收货人详细地址
        $data['province']        = $houseData['province'];                      //收货人省份
        $data['city']            = $houseData['city'];                     //收货人市区
        $data['district']        = $houseData['district'];                     //县城/区
        $data['pay_status']      = 0;                                       //是否已支付
        $data['is_comment']      = 0;                                       //是否已评论
        $data['order_status']    = 0;                                       //订单状态 0:等待付款
        $data['shipping_status'] = 0;                                       //发货状态 0:未发货
        $data['return_status']   = 0;                                       //退货状态 0:未申请
        $data['goods_amount']    = $_SESSION['mobile']['info']['money'];    //订单总价
        $data['coupon']          = 0;                                       //使用的优惠券金额
        $data['add_order_time']  = time();                                  //下单时间
        $data['schedule_status'] = 1;                                       //进度状态
        $data['allot_status']    = 0;                                       //分配状态
        $data['house_layout']    = $houseData['layout'];                    //房子布局
        $data['total_area']      = $houseData['total_area'];                //房子总面积
        $data['area']            = $houseData['area'];                      //每个房间面积
        $data['house_city']      = $houseData['province'].'-'.$houseData['city'].'-'.$houseData['district'].'-'.$houseData['address'];                      //报价地址
        $data['house_type']      = $houseData['type'];                      //房屋类型
        $data['people']          = $houseData['people'];                    //常住人口
        //插入订单数据
        $order_id = M('xgj_furnish_order_info')->data($data)->add();
        $error=['status'=>1,'msg'=>$order_id];
        //插入订单商品数据
        if($order_id !== false){
            $info['order_id'] = $order_id;              
            $info['quote_id']     = $cid;
            $info['quote_name']   = $re['quote_name'];
            $info['level']        = 1;
            $info['quote_status'] = 1;
            $info['stuff_goods']  = $_SESSION['mobile']['info']['sn'];
            $info['quote_price']  = $_SESSION['mobile']['info']['money'];
            $info['cost']         = $_SESSION['mobile']['info']['cost'];
            $info['plan_settle']  = 0;
            $info['status']       = 0; 
            $info['num']          = 1; 
            /*系统总金额-系统的优惠金额*返回积分的比例得到返还积分数值*/
            $info['integral']     = $_SESSION['mobile']['info']['gift'];                 
            if(M('xgj_furnish_order_detail')->data($info)->add()==false){
                //下订单失败, 需要回滚
                $error=['status'=>2,'msg'=>'订单详情添加失败'];
            }
            /*跟单人*/
            $da['user_id']                            =$userId;
            $da['order_id']                           =$order_id;
            $da['add_time']                           =time();
            if(M('xgj_documentary')->data($da)->add() ==false){
                $error=['status'=>2,'msg'=>'添加跟单信息失败'];
            }
        }else{
            //订单插入失败, 需要回滚
            $error=['status'=>2,'msg'=>'订单添加失败'];
        }
        echo json_encode($error);
    }

    public function pay(){
        $order_id = I('get.oid');
        $data = M('xgj_furnish_order_info a')
            ->field('a.order_code,b.quote_name')
            ->join('xgj_furnish_order_detail b on a.order_id = b.order_id')
            ->join('xgj_furnish_quote c on b.quote_id = c.quote_id')
            ->where(['a.order_id'=>$order_id])->find();
        $this->assign('data',$data);  
        $this->assign('order_id',$order_id);  
        $this->assign('header','在线支付');
        $this->display();
    }

    public function place(){
        $name    = I('post.name');
        $oid     = I('post.oid');
        $pay_way = I('post.pay_way');
        $re      = M('xgj_furnish_order_info')->data(['pay_method'=>$pay_way])->where(['order_id'=>$oid])->save();
        $sn      = M('xgj_furnish_order_info')->where(['order_id'=>$oid])->getField('order_code');
        if($re!==false && $pay_way==4){
            $config = C('webpay');
            vendor('Webpay.wappay.service.AlipayTradeService','','.php');
            vendor('Webpay.wappay.buildermodel.AlipayTradeWapPayContentBuilder','','.php');
            if (!empty($sn)&& trim($sn)!=""){
                //商户订单号，商户网站订单系统中唯一订单号，必填
                $out_trade_no = $sn;
                //订单名称，必填
                $subject = $name;
                //付款金额，必填
                $total_amount = '0.01';
                //商品描述，可空
                $body = '';
                //超时时间
                $timeout_express="1m";
                $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
                $payRequestBuilder->setBody($body);
                $payRequestBuilder->setSubject($subject);
                $payRequestBuilder->setOutTradeNo($out_trade_no);
                $payRequestBuilder->setTotalAmount($total_amount);
                $payRequestBuilder->setTimeExpress($timeout_express);
                $payResponse = new \AlipayTradeService($config);
                $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
                return ;
            }
        }elseif($re!==false && $pay_way==5){
            import('Vendor.wxpay.wxpay');
            $config = C("wxpay");
            if (empty($config)) {
                $this->show('请设置微信支付配置');
            }
            $wxpay = new \wxpay($config);
            $order = array(
                "body" => '微信支付测试111',
                "product_id" => 12,
                "order_sn" => $sn,
                "total_fee" => 1,
                "url" => ""
            );
            $order['out_trade_no'] = $sn . "_" . ($order['total_fee']);
            /*扫码支付 start*/
            // $pay_url = $wxpay->native($order);
            // if ($pay_url === false) {
            //     $this->show('获取支付链接失败：' . $wxpay->getError());
            // }else{
            //     $this->assign("html", "<img src='" .U("Index/qrcode", array('text' => urlencode($pay_url))). "' />");
            // }
            // $this->assign("out_trade_no", $order['out_trade_no']);
            // $this->assign("type", "native");
            /*扫码支付 end*/
            /*JSAPI start*/
            $html = $wxpay->jsApiPay($order);
            if ($html === false) {
                $this->show($wxpay->getError());
            }else{
                $this->assign("html", $html);
            }
            $this->assign("type", "jsapipay");
            /*JSAPI end*/
            $this->display();
        }else{
            $this->error('修改支付方式失败');
        }
    }

    public function notifyurl(){
        /* *
         * 功能：支付宝服务器异步通知页面
         * 版本：2.0
         * 修改日期：2016-11-01
         * 说明：
         * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

         *************************页面功能说明*************************
         * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
         * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
         * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
         */
        $config = C('webpay');
        //vendor('Webpay.wappay.service.AlipayTradeService','','.php');
        // include(dirname($_SERVER['DOCUMENT_ROOT']).'/mobile/Webpay/wapPay/config.php');
        // include(dirname($_SERVER['DOCUMENT_ROOT']).'/mobile/Webpay/wapPay/service/AlipayTradeService.php');
        //vendor('Webpay.config');
        vendor('Webpay.wappay.service.AlipayTradeService');
        $arr=$_POST;
        $alipaySevice = new \AlipayTradeService($config); 
        $alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代

            
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            //接受数据
            $user_id            = $_SESSION['user']['userId'];
            $out_trade_no       = $_POST['out_trade_no'];//商户订单号
            $trade_no           = $_POST['trade_no'];//支付宝交易号
            $trade_status       = $_POST['trade_status'];//交易状态
            $total_fee          = $_POST['total_amount'];//总交易金额
            $seller_id          = $_POST['seller_id'];//卖家账户号
            $notify_id          = $_POST['notify_id'];//通知校验ID
            $buyer_id           = $_POST['buyer_id'];//买家支付宝账户号
            $order_id=M('xgj_furnish_order_info')->where(['order_code'=>$out_trade_no])->getField("order_id"); 

            if($_POST['trade_status'] == 'TRADE_FINISHED') {

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //临时
                $info['pay_status'] = 4;//修改为第三笔已经支付
                $info['order_status'] = 8;//修改为等待发货
                //临时
                $info['pay_time'] = time();//支付时间
                $result = M('xgj_furnish_order_info')->where(['order_code' => $out_trade_no])->data($info)->save();
                    
                if ($result !== flase) {
                    //支付成功, 插入支付详情表
                    $data = array(
                        'user_id'      => $user_id,
                        'order_id'     => $out_trade_no,
                        'notify_id'    => $notify_id,
                        'trade_no'     => $trade_no,
                        'buyer_id'     => $buyer_id,
                        'total_fee'    => $total_fee,
                        'trade_status' => $trade_status,
                        'addtime'      => date('Y-m-d H:i:s'),
                        'is_mobile'    => 2,
                    );
                    if (!M('xgj_eu_payment')->data($data)->add()) {
                        //给用户提示支付成功的页面
                        $this->redirect("Order/success",['status'=>1,'oid'=>$order_id]);die;
                    }
                } else {
                    //给用户提示错误页面
                    $this->redirect("Order/success",['status'=>2,'msg'=>'修改支付状态失败']);die;
                }
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序   
                //临时
                $info['pay_status'] = 4;//修改为第三笔已经支付
                $info['order_status'] = 8;//修改为等待发货
                //临时
                $info['pay_time'] = time();//支付时间
                $result = M('xgj_furnish_order_info')->where(array('order_code' => $out_trade_no))->data($info)->save();
                    
                if ($result !== flase) {
                    //支付成功, 插入支付详情表
                    $data = array(
                        'user_id'      => $user_id,
                        'order_id'     => $out_trade_no,
                        'notify_id'    => $notify_id,
                        'trade_no'     => $trade_no,
                        'buyer_id'     => $buyer_id,
                        'buyer_email'  => $buyer_email,
                        'total_fee'    => $total_fee,
                        'trade_status' => $trade_status,
                        'addtime'      => date('Y-m-d H:i:s'),
                        'system_id'    => $extra_common_param,
                        'is_mobile'    => 2,
                    );
                    if (M('xgj_eu_payment')->data($data)->add()) {
                        //给用户提示支付成功的页面
                        $this->redirect("Order/success",['status'=>1,'oid'=>$order_id]);die;
                    }
                } else {
                    //给用户提示错误页面
                    $this->redirect("Order/success",['status'=>2,'msg'=>'修改支付状态失败']);die;
                }         
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
                
            echo "success";     //请不要修改或删除
                
        }else {
            //验证失败
            echo "fail";    //请不要修改或删除

        }
    }

    public function returnurl(){
        /* *
         * 功能：支付宝页面跳转同步通知页面
         * 版本：2.0
         * 修改日期：2016-11-01
         * 说明：
         * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

         *************************页面功能说明*************************
         * 该页面可在本机电脑测试
         * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
         */
         $config = C('webpay');
        // vendor('Webpay.wapPay.service.AlipayTradeService','','.php');
        // include(dirname($_SERVER['DOCUMENT_ROOT']).'/mobile/Webpay/wapPay/config.php');
        // include(dirname($_SERVER['DOCUMENT_ROOT']).'/mobile/Webpay/wapPay/service/AlipayTradeService.php');
        //vendor('Webpay.config');
        vendor('Webpay.wappay.service.AlipayTradeService');
        $arr=$_GET;
        $alipaySevice = new \AlipayTradeService($config); 
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码
            
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //接受数据
            $user_id            = $_SESSION['user']['userId'];
            $out_trade_no       = htmlspecialchars($_GET['out_trade_no']);//商户订单号
            $trade_no           = htmlspecialchars($_GET['trade_no']);//支付宝交易号
            $total_fee          = $_GET['total_amount'];//总交易金额
            $seller_id          = $_GET['seller_id'];//卖家账户号

            $info['pay_status'] = 4;//修改为第三笔已经支付
            $info['order_status'] = 8;//修改为等待发货
            //临时
            $info['pay_time'] = time();//支付时间
            $result = M('xgj_furnish_order_info')->where(array('order_code' => $out_trade_no))->data($info)->save();
            $order_id=M('xgj_furnish_order_info')->where(['order_code'=>$out_trade_no])->getField("order_id");    
            if ($result !== flase) {
                //支付成功, 插入支付详情表
                $data = array(
                    'user_id'      => $user_id,
                    'order_id'     => $out_trade_no,
                    'trade_no'     => $trade_no,
                    'total_fee'    => $total_fee,
                    'addtime'      => date('Y-m-d H:i:s'),
                    'is_mobile'    => 2,
                );
                if (M('xgj_eu_payment')->data($data)->add()) {
                    //给用户提示支付成功的页面
                    $this->redirect("Order/success",['status'=>1,'oid'=>$order_id]);die;
                }
            } else {
                //给用户提示错误页面
                $this->redirect("Order/success",['status'=>2,'msg'=>'修改支付状态失败']);die;
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }else {
            //验证失败
             $this->redirect("Order/success",['status'=>2,'msg'=>'验证失败']);die;
        }
    }

    public function success(){
        $status=empty(I('status'))?'':I('status');
        $msg=empty(I('msg'))?'':I('msg');
        $oid=empty(I('oid'))?'':I('oid');

        if($status==1){
            $this->assign('status',$status);
            $this->assign('oid',$oid);
        }else{
            $this->assign('status',$status);
            $this->assign('oid',$oid);
            $this->assign('msg',$msg);
        }
        $this->display();
    }
}