<?php

require_once("lib/Submit.class.php");
class Alipay{

    public static function pay($alipay_config,$args){
        /**************************请求参数**************************/
        //支付类型
        $payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url = $args['notify_url'];
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        //页面跳转同步通知页面路径
        $return_url = $args['return_url'];
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        //商户订单号
        $out_trade_no = $args['out_trade_no'];
        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject = $args['name'];
        //必填
        //付款金额
        $total_fee = $args['total'];
        //必填
        //自定义参数
        $extra_common_param=$args['extra_common_param'];
        //订单描述
        $body = $args['content'];
        //商品展示地址
        $show_url = $args['show_url'];
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
            'extra_common_param' => $extra_common_param,//用户自定义参数, 回调时会传回来. 用于区分家居和欧团
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        echo '正在跳转至支付宝，请耐心等待...';
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }

    public static function refund($args){
        //var_dump($args);die;
        /**************************请求参数**************************/
        $alipay_config=C('alipay');
        // 卖家支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
        $seller_user_id=$alipay_config['partner'];
        // 服务器异步通知页面路径，需http://格式的完整路径，不能加?id=123这类自定义参数,必须外网可以正常访问
        $notify_url = $args['notify_url'];
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        //页面跳转同步通知页面路径
        $return_url = $args['return_url'];
        // 退款日期 时间格式 yyyy-MM-dd HH:mm:ss
        //date_default_timezone_set('PRC');//设置当前系统服务器时间为北京时间，PHP5.1以上可使用。
        $refund_date=date("Y-m-d H:i:s",time());;
        // 调用的接口名，无需修改
        $service='refund_fastpay_by_platform_nopwd';
        //批次号，必填，格式：当天日期[8位]+序列号[3至24位]，如：201603081000001
        $batch_no = $args['WIDbatch_no'];
        //退款笔数，必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）
        $batch_num = $args['WIDbatch_num'];
        //退款详细数据，必填，格式（支付宝交易号^退款金额^备注），多笔请用#隔开
        $detail_data = $args['WIDdetail_data'];
        //自定义参数
        //$extra_common_param=$args['extra_common_param'];
        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service"            => trim($service),
            "partner"            => trim($alipay_config['partner']),
            "notify_url"         => trim($notify_url),
            //"seller_user_id"     => trim($seller_user_id),
            "refund_date"        => trim($refund_date),
            "batch_no"           => $batch_no,
            "batch_num"          => $batch_num,
            "detail_data"        => $detail_data,
            "_input_charset"     => trim(strtolower($alipay_config['input_charset'])),
        );
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestHttp($parameter);

        $doc = new DOMDocument();
        $doc->loadXML($html_text);

        //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

        //解析XML
        if( ! empty($doc->getElementsByTagName( "alipay" )->item(0)->nodeValue) ) {
            $alipay = $doc->getElementsByTagName( "alipay" )->item(0)->nodeValue;
            if(trim($alipay)=='T'){
                if($args['class_id']==1){
                    //修改订单状态
                    $info['return_status'] = 1;//修改为退款中
                    $info['order_status']  = 6;//修改为取消
                    $info['refund_time']   = time();//退款时间
                    $result                = M('xgj_eu_order')->where(array('id'=>$args['order_id']))->data($info)->save();
                    if($result!==false){
                        $result = M('xgj_eu_split_order')->where(array('order_id'=>$args['order_id']))->data(array('order_status'=>6))->save();
                    }
                }elseif($args['class_id']==2){
                    //修改订单状态
                    $info['pay_status']   = 7;//修改为已退款
                    $info['order_status']    = 2;//修改为取消
                    $info['refund_time']     = time();//退款时间
                    $result          = M('xgj_furnish_order_info')->where(array('order_id'=>$args['order_id']))->data($info)->save();
                }elseif($args['class_id']==8 || $args['class_id']==9){
                    //修改订单状态
                    $info['is_pay']       = 1;//修改为已经支付
                    $info['order_status'] = 1;//修改为等待发货
                    $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                    $result               = M('xgj_s_order')->where(array('id'=>$args['order_id']))->data($info)->save();
                }
                if($result!==false){
                    $_SESSION['refund']['id']=$args['order_id'];
                    $_SESSION['refund']['class_id']=$args['class_id'];
                    $_SESSION['refund']['msg']='1';
                    redirect(U('Order/refundFile'));
                }
            }elseif($alipay=='F'){
                $_SESSION['refund']['id']=$args['order_id'];
                $_SESSION['refund']['class_id']=$args['class_id'];
                $_SESSION['refund']['msg']='2';
                redirect(U('Order/refundFile'));
            }
        }
    }
}
