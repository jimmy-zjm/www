<?php
namespace Home\Controller;
use Think\Controller;

class RefundController extends BaseController {

    // 支付宝异步跳转处理
    public function notifyurl(){
        layout(false);
        $alipay_config = C('alipay');
        vendor('Alipay.lib.Notify','','.class.php');
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {//验证成功
            //接受数据
            $user_id        = $_SESSION['user']['userId'];
            $notify_time    = $_POST['notify_time'];//通知时间
            $notify_type    = $_POST['notify_type'];//通知类型
            $notify_id      = $_POST['notify_id'];//通知校验ID
            $sign_type      = "MD5";//签名方式
            $sign           = $_POST['sign'];//签名
            $batch_no       = $_POST['batch_no'];//退款批次号
            $success_num    = $_POST['success_num'];//退款成功总数·
            $result_details = $_POST['result_details'];//退款结果明细

            $data = array(
                'user_id'        => $user_id,
                'notify_time'    => $notify_time,
                'notify_type'    => $notify_type,
                'notify_id'      => $notify_id,
                'sign_type'      => $sign_type,
                'sign'           => $sign,
                'batch_no'       => $batch_no,
                'success_num'    => $success_num,
                'add_time'       => date('Y-m-d H:i:s'),
                'result_details' => $result_details,
            );
            if(!M('xgj_alipay_refund_stream')->data($data)->add()){
                logResult("添加退款流水信息失败");
            }
            echo "success";     //请不要修改或删除
        }else{
            echo "fail";
        }
    }
   

    // 

    //银联无卡支付接口处理
    public function b2cNoRefundSend(){
        header('Content-Type:text/html;charset=utf-8 ');
        include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Nopay/util/Settings_INI.php');
        $file=dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Nopay/path.properties";
        $settings = new \Settings_INI();
        $settings->load($file);
        $refund_url = $settings->get("refund_url");
        $html="<form name='payment' action='$refund_url' method='POST' target='_blank'>";
        $params = "TranReserved;MerBgUrl;BusiType;CurryNo;MerSplitMsg;MerId;AccessType;AcqCode;SplitType;Signature;TranDate;TranTime;OriTranDate;TranType;Version;MerResv;SplitMethod;MerOrderNo;OriOrderNo;RefundAmt";
        foreach ($_SESSION['chinanopay'] as $k => $v) {
            if (strstr($params, $k)) {
                $data['k']=$v;
            }
        }
        $uri=$refund_url;
        $post ='';
        // curl 方法
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $uri );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        //curl_setopt ( $ch, CURLOPT_HEADER, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query($data) );
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Expect:')); 
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        $urlarr=parse_url($return);
        parse_str($urlarr['path'],$parr);
        $arr=explode($data['MerResv'],',');
        if($parr['respCode']=='1003'){
            unset($_SESSION['chinanopay']);
            if($arr[1]==1){
                //修改订单状态
                $info['return_status'] = 1;//修改为退款中
                $info['order_status']  = 6;//修改为取消
                $info['refund_time']   = time();//退款时间
                $result                = M('xgj_eu_order')->where(array('id'=>$arr[0]))->data($info)->save();
                if($result){
                    $result = M('xgj_eu_split_order')->where(array('order_id'=>$arr[0]))->data(array('order_status'=>6))->save();
                }
            }elseif($arr[1]==2){
                //修改订单状态
                $info['pay_status']   = 7;//修改为已退款
                $info['order_status']    = 2;//修改为取消
                $info['refund_time']     = time();//退款时间
                $result          = M('xgj_furnish_order_info')->where(array('order_id'=>$arr[0]))->data($info)->save();
            }elseif($arr[1]==8 || $arr[1]==9){
                //修改订单状态
                $info['is_pay']       = 1;//修改为已经支付
                $info['order_status'] = 1;//修改为等待发货
                $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                $result               = M('xgj_s_order')->where(array('id'=>$arr[0]))->data($info)->save();
            }
            if($result!==false){
                $_SESSION['refund']['id']=$arr[0];
                $_SESSION['refund']['class_id']=$arr[1];
                $_SESSION['refund']['msg']='1';
                $this->redirect('Order/refundFile');
            }
        }else{
            unset($_SESSION['chinanopay']);
            $_SESSION['refund']['id']=$arr[0];
            $_SESSION['refund']['class_id']=$arr[1];
            $_SESSION['refund']['msg']='2';
            $this->redirect('Order/refundFile');
        }
    }

    /*银联无卡支付支付后台应答处理*/
    public function bgNoReturn(){
        if ($_POST) {
            if (count($_POST) > 0) {
                include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Nopay/util/SecssUtil.class.php');
                include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Nopay/util/common.php');
                $secssUtil = new \SecssUtil();
                $securityPropFile =dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Nopay/security.properties"; 
                $secssUtil->init($securityPropFile);
                $text = array();
                foreach($_POST as $key=>$value){
                    $text[$key] = urldecode($value);
                }
                $arr=explode($text['MerResv'],',');
                $order_id=$arr[0];
                if ($secssUtil->verify($text)) {
                    if($arr[1]==1){
                         //修改订单状态
                        $info['return_status'] = 1;//修改为退款中
                        $info['order_status']  = 6;//修改为取消
                        $info['refund_time']   = time();//退款时间
                        $result                = M('xgj_eu_order')->where(array('id'=>$order_id))->data($info)->save();
                        if($result){);
                            $result = M('xgj_eu_split_order')->where(array('order_id'=>$id))->data(array('order_status'=>6))->save();
                        }
                    }else if($arr[1]==2){
                        //修改订单状态
                        $info['pay_status']   = 7;//修改为退款中
                        $info['order_status'] = 2;//修改为取消
                        $info['refund_time']  = time();//退款时间
                        $result          = M('xgj_furnish_order_info')->where(array('order_id'=>$order_id))->data($info)->save();
                    }else if($arr[1]==8 || $arr[1]==9){
                         //修改订单状态
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_s_order')->where(array('id'=>$order_id))->data($info)->save();
                    }

                    if($result!==flase){
                        //支付成功, 插入支付详情表
                        $data_=array(
                            'order_sn'      =>$text['MerOrderNo'],//退货订单号
                            'ori_sn'        =>$text['OriOrderNo'],//原始订单号
                            'version'       =>$text['Version'],//版本号
                            'mer_id'        =>$text['MerId'],//商户号
                            'tran_date'     =>$text['TranDate'],//商户交易日期
                            'tran_time'     =>$text['TranTime'],//商户交易时间
                            'total_fee'     =>$text['OrderAmt']/100,//订单金额
                            'refund_fee'    =>$text['RefundAmt']/100,//退款金额
                            'tran_type'     =>$text['TranType'],//交易类型
                            'busi_type'     =>$text['BusiType'],//业务类型
                            'order_status'  =>$text['OrderStatus'],//订单状态
                            'signature'     =>$text['Signature'],//签名
                            'class_id'      =>$arr[1],//自定义参数
                            'order_id'      =>$arr[0],//原订单id
                            'add_time'      =>time(),
                        );
                        if(!M('xgj_chinapay_refund')->data($data_)->add()){
                            echo '退款详情信息插入失败';
                        }
                        //给用户提示支付成功的页面
                        $_SESSION['refund']['id']=$arr[0];
                        $_SESSION['refund']['class_id']=$arr[1];
                        $_SESSION['refund']['msg']='1';
                        $this->redirect('User/refundFile');
                        die;
                        // die('支付成功');
                    }else{
                        //给用户提示错误页面
                        $_SESSION['refund']['id']=$arr[0];
                        $_SESSION['refund']['class_id']=$arr[1];
                        $_SESSION['refund']['msg']='2';
                        $this->redirect('User/refundFile');
                        die;
                    }
                } else {
                    //给用户提示错误页面
                    $_SESSION['refund']['id']=$arr[0];
                    $_SESSION['refund']['class_id']=$arr[1];
                    $_SESSION['refund']['msg']='2';
                    $this->redirect('Order/refundFile');
                    die;
                }
            }
        }
    }


   

    //银联支付接口处理
    public function b2cRefundSend(){
        header('Content-Type:text/html;charset=utf-8 ');
        include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/Settings_INI.php');
        $file=dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/path.properties';
        $settings = new \Settings_INI();
        $settings->load($file);
        $refund_url = $settings->get("refund_url");
        $params = "TranReserved;MerBgUrl;BusiType;CurryNo;MerSplitMsg;MerId;AccessType;AcqCode;SplitType;Signature;TranDate;TranTime;OriTranDate;TranType;Version;MerResv;SplitMethod;MerOrderNo;OriOrderNo;RefundAmt";
        foreach ($_SESSION['chinapay'] as $k => $v) {
            if (strstr($params, $k)) {
                $data[$k]=$v;
            }
        }
        $uri=$refund_url;
        $post ='';
        // curl 方法
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $uri );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        //curl_setopt ( $ch, CURLOPT_HEADER, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query($data) );
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Expect:')); 
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        $urlarr=parse_url($return);
        parse_str($urlarr['path'],$parr);
        $arr=explode($data['MerResv'],',');
        if($parr['respCode']=='1003'){
            unset($_SESSION['chinanopay']);
            if($arr[1]==1){
                //修改订单状态
                $info['return_status'] = 1;//修改为退款中
                $info['order_status']  = 6;//修改为取消
                $info['refund_time']   = time();//退款时间
                $result                = M('xgj_eu_order')->where(array('id'=>$arr[0]))->data($info)->save();
                if($result){
                    $result = M('xgj_eu_split_order')->where(array('order_id'=>$arr[0]))->data(array('order_status'=>6))->save();
                }
            }elseif($arr[1]==2){
                //修改订单状态
                $info['pay_status']   = 7;//修改为已退款
                $info['order_status']    = 2;//修改为取消
                $info['refund_time']     = time();//退款时间
                $result          = M('xgj_furnish_order_info')->where(array('order_id'=>$arr[0]))->data($info)->save();
            }elseif($arr[1]==8 || $arr[1]==9){
                //修改订单状态
                $info['is_pay']       = 1;//修改为已经支付
                $info['order_status'] = 1;//修改为等待发货
                $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                $result               = M('xgj_s_order')->where(array('id'=>$arr[0]))->data($info)->save();
            }
            if($result!==false){
                $_SESSION['refund']['id']=$arr[0];
                $_SESSION['refund']['class_id']=$arr[1];
                $_SESSION['refund']['msg']='1';
                $this->redirect('Order/refundFile');
            }
        }else{
            unset($_SESSION['chinanopay']);
            $_SESSION['refund']['id']=$arr[0];
            $_SESSION['refund']['class_id']=$arr[1];
            $_SESSION['refund']['msg']='2';
            $this->redirect('Order/refundFile');
        }
    }

    //银联支付后台应答处理
    public function bgReturn(){
        if ($_POST) {
            if (count($_POST) > 0) {
                include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/common.php');
                include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/SecssUtil.class.php');
                $secssUtil = new \SecssUtil();
                $securityPropFile = dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Chinapay/security.properties";
                $secssUtil->init($securityPropFile);
                $text = array();
                foreach($_POST as $key=>$value){
                    $text[$key] = urldecode($value);
                }
                $arr=explode($text['MerResv'],',');
                $order_id=$arr[0];
                if ($secssUtil->verify($text)) {
                    if($arr[1]==1){
                         //修改订单状态
                        $info['return_status'] = 1;//修改为退款中
                        $info['order_status']  = 6;//修改为取消
                        $info['refund_time']   = time();//退款时间
                        $result                = M('xgj_eu_order')->where(array('id'=>$order_id))->data($info)->save();
                        if($result){);
                            $result = M('xgj_eu_split_order')->where(array('order_id'=>$id))->data(array('order_status'=>6))->save();
                        }
                    }else if($arr[1]==2){
                        //修改订单状态
                        $info['pay_status']   = 7;//修改为退款中
                        $info['order_status'] = 2;//修改为取消
                        $info['refund_time']  = time();//退款时间
                        $result          = M('xgj_furnish_order_info')->where(array('order_id'=>$order_id))->data($info)->save();
                    }else if($arr[1]==8 || $arr[1]==9){
                         //修改订单状态
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_s_order')->where(array('id'=>$order_id))->data($info)->save();
                    }

                    if($result!==flase){
                        //支付成功, 插入支付详情表
                        $data_=array(
                            'order_sn'      =>$text['MerOrderNo'],//退货订单号
                            'ori_sn'        =>$text['OriOrderNo'],//原始订单号
                            'version'       =>$text['Version'],//版本号
                            'mer_id'        =>$text['MerId'],//商户号
                            'tran_date'     =>$text['TranDate'],//商户交易日期
                            'tran_time'     =>$text['TranTime'],//商户交易时间
                            'total_fee'     =>$text['OrderAmt']/100,//订单金额
                            'refund_fee'    =>$text['RefundAmt']/100,//退款金额
                            'tran_type'     =>$text['TranType'],//交易类型
                            'busi_type'     =>$text['BusiType'],//业务类型
                            'order_status'  =>$text['OrderStatus'],//订单状态
                            'signature'     =>$text['Signature'],//签名
                            'class_id'      =>$arr[1],//自定义参数
                            'order_id'      =>$arr[0],//原订单id
                            'add_time'      =>time(),
                        );
                        if(!M('xgj_chinapay_refund')->data($data_)->add()){
                            echo '退款详情信息插入失败';
                        }
                        //给用户提示支付成功的页面
                        $_SESSION['refund']['id']=$arr[0];
                        $_SESSION['refund']['class_id']=$arr[1];
                        $_SESSION['refund']['msg']='1';
                        $this->redirect('Order/refundFile');
                        die;
                    }else{
                        //给用户提示错误页面
                        $_SESSION['refund']['id']=$arr[0];
                        $_SESSION['refund']['class_id']=$arr[1];
                        $_SESSION['refund']['msg']='2';
                        $this->redirect('Order/refundFile');die;
                    }
                } else {
                    //给用户提示错误页面
                    $_SESSION['refund']['id']=$arr[0];
                    $_SESSION['refund']['class_id']=$arr[1];
                    $_SESSION['refund']['msg']='2';
                    $this->redirect('Order/refundFile');die;
                }
            }
        }
    }

 
}

 // //银联支付接口处理
    // public function sign(){
    //     define('transResvered', "trans_");
    //     define('cardResvered', "card_");
    //     define('transResveredKey', "TranReserved");
    //     define('signatureField', "Signature");
    //     include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/common.php');
    //     include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/SecssUtil.class.php');
    //     if ($_POST) {
    //         $dispatchMap = array(
    //             // 配置个人网银交易转发地址
    //             "0001" => U('Refund/b2cPaySend'),
    //             "0004" => U('Refund/b2cPaySend'),
    //             // 配置退款交易转发地址
    //             "0401" => U('Refund/b2cRefundSend'),
    //             // 配置查询交易转发地址
    //             "0502" => U('Refund/b2cQuerySend'),
    //         );
    //         if (count($_POST) > 0) {
    //             if (isset($_POST['TranType']) && trim($_POST['TranType']) != "") {
    //                 $dispatchUrl = $dispatchMap[$_POST['TranType']];
    //             } else {
    //                 $dispatchUrl = $dispatchMap['0401'];
    //             }
    //             $transResvedJson = array();
    //             $cardInfoJson = array();
    //             $sendMap = array();
    //             foreach ($_POST as $key => $value) {
    //                 if (isEmpty($value)) {
    //                     continue;
    //                 }
    //                 if (startWith($key, transResvered)) {
    //                     // 组装交易扩展域
    //                     $key = substr($key, strlen(transResvered));
    //                     $transResvedJson[$key] = $value;
    //                 } else 
    //                     if (startWith($key, cardResvered)) {
    //                         // 组装有卡交易信息域
    //                         $key = substr($key, strlen(cardResvered));
    //                         $cardInfoJson[$key] = $value;
    //                     } else {
    //                         $sendMap[$key] = $value;
    //                     }
    //             }
    //             $transResvedStr = null;
    //             $cardResvedStr = null;
    //             if (count($transResvedJson) > 0) {
    //                 $transResvedStr = json_encode($transResvedJson);
    //             }
    //             if (count($cardInfoJson) > 0) {
    //                 $cardResvedStr = json_encode($cardInfoJson);
    //             }
    //             $secssUtil = new \SecssUtil();
    //             if (! isEmpty($transResvedStr)) {
    //                 $transResvedStr = $secssUtil->decryptData($transResvedStr);
    //                 $sendMap[transResveredKey] = $transResvedStr;
    //             }
    //             if (! isEmpty($cardResvedStr)) {
    //                 $cardResvedStr = $secssUtil->decryptData($cardResvedStr);
    //                 $sendMap[cardResveredKey] = $cardResvedStr;
    //             }
                
    //             $securityPropFile = dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Chinapay/security.properties";
    //             $secssUtil->init($securityPropFile);
    //             $secssUtil->sign($sendMap);
                
    //             $sendMap[signatureField] = $secssUtil->getSign();

    //             $_SESSION['chinapayrefund'] = $sendMap;

    //             header("Location:" . $dispatchUrl);
    //         }
    //     }
    // }
 


 //银联接口处理
    // public function nosign(){
    //     define('transResvered', "trans_");
    //     define('cardResvered', "card_");
    //     define('transResveredKey', "TranReserved");
    //     define('signatureField', "Signature");
    //     include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Nopay/util/common.php');
    //     //include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/common.php');
    //     if ($_POST) {
    //         $dispatchMap = array(
    //             // 配置个人网银交易转发地址
    //             "0001" => U('Refund/b2cNoPaySend'),
    //             "0004" => U('Refund/b2cNoPaySend'),
    //             // 配置退款交易转发地址
    //             "0401" => U('Refund/b2cNoRefundSend'),
    //             // 配置查询交易转发地址
    //             "0502" => U('Refund/b2cNoQuerySend'),
    //         );
    //         if (count($_POST) > 0) {
    //             if (isset($_POST['TranType']) && trim($_POST['TranType']) != "") {
    //                 $dispatchUrl = $dispatchMap[$_POST['TranType']];
    //             } else {
    //                 $dispatchUrl = $dispatchMap['0401'];
    //             }
    //             $transResvedJson = array();
    //             $cardInfoJson = array();
    //             $sendMap = array();
    //             foreach ($_POST as $key => $value) {
    //                 if (isEmpty($value)) {
    //                     continue;
    //                 }
    //                 if (startWith($key, transResvered)) {
    //                     // 组装交易扩展域
    //                     $key = substr($key, strlen(transResvered));
    //                     $transResvedJson[$key] = $value;
    //                 } else 
    //                     if (startWith($key, cardResvered)) {
    //                         // 组装有卡交易信息域
    //                         $key = substr($key, strlen(cardResvered));
    //                         $cardInfoJson[$key] = $value;
    //                     } else {
    //                         $sendMap[$key] = $value;
    //                     }
    //             }
    //             $transResvedStr = null;
    //             $cardResvedStr = null;
    //             if (count($transResvedJson) > 0) {
    //                 $transResvedStr = json_encode($transResvedJson);
    //             }
    //             if (count($cardInfoJson) > 0) {
    //                 $cardResvedStr = json_encode($cardInfoJson);
    //             }
    //             include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Nopay/util/SecssUtil.class.php');
    //             $secssUtil = new \SecssUtil();
    //             if (! isEmpty($transResvedStr)) {
    //                 $transResvedStr = $secssUtil->decryptData($transResvedStr);
    //                 $sendMap[transResveredKey] = $transResvedStr;
    //             }
    //             if (! isEmpty($cardResvedStr)) {
    //                 $cardResvedStr = $secssUtil->decryptData($cardResvedStr);
    //                 $sendMap[cardResveredKey] = $cardResvedStr;
    //             }
                
    //             $securityPropFile = dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Nopay/security.properties";
    //             $secssUtil->init($securityPropFile);
    //             $secssUtil->sign($sendMap);
    //             $sendMap[signatureField] = $secssUtil->getSign();
    //             $_SESSION['chinanopayrefund'] = $sendMap;
    //             header("Location:" . $dispatchUrl);
    //         }
    //     }
    // } 