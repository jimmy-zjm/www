<?php
namespace Home\Controller;
use Think\Controller;

class PayController extends BaseController {

    // 支付宝同步跳转处理
    public function returnurl(){
        layout(false);
        $alipay_config = C('alipay');
        vendor('Alipay.lib.Notify','','.class.php');
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        
        if ($verify_result) {//验证成功
            //接受数据
            $user_id = $_SESSION['user']['userId'];
            $out_trade_no = $_GET['out_trade_no'];//商户订单号
            $trade_no = $_GET['trade_no'];//支付宝交易号
            $trade_status = $_GET['trade_status'];//交易状态
            $total_fee = $_GET['total_fee'];//总交易金额
            $seller_id = $_GET['seller_id'];//卖家账户号
            $notify_id = $_GET['notify_id'];//通知校验ID
            $buyer_id = $_GET['buyer_id'];//买家支付宝账户号
            $buyer_email = $_GET['buyer_email'];//买家支付宝账号
            $extra_common_param = $_GET['extra_common_param'];//自定义参数

            $order_id = substr($out_trade_no, 0, 18);
            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                if ($this->checkOrder($order_id, $total_fee, $seller_id, $extra_common_param, $error)) {
                    if ($extra_common_param == '1') {
                        //修改订单状态
                        $info['is_pay'] = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time'] = date('Y-m-d H:i:s');//支付时间
                        $result = M('xgj_eu_order')->where(array('sn' => $order_id))->data($info)->save();
                        if ($result) {
                            $id = M('xgj_eu_order')->where(array('sn' => $order_id))->getField('id');
                            $result = M('xgj_eu_split_order')->where(array('order_id' => $id))->data(array('order_status' => 1))->save();
                        }
                    } elseif ($extra_common_param == '2') {
                        //修改订单状态
                        // $info['pay_status'] = 2;//修改为第一笔已经支付500
                        // $info['order_status'] = 7;//修改为等待发货
                        //临时
                        $info['pay_status'] = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        //临时
                        $info['pay_time'] = time();//支付时间
                        $result = M('xgj_furnish_order_info')->where(array('order_code' => $order_id))->data($info)->save();
                    } elseif ($extra_common_param == '3') {
                        //修改订单状态
                        $info['pay_status'] = 3;//修改为第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time'] = time();//支付时间
                        $result = M('xgj_furnish_order_info')->where(array('order_code' => $order_id))->data($info)->save();
                    } elseif ($extra_common_param == '4') {
                        //修改订单状态
                        $info['pay_status'] = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time'] = time();//支付时间
                        $result = M('xgj_furnish_order_info')->where(array('order_code' => $order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as intd,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('i.order_code' => $order_id))->find();
                            $int=$list['intd'] + $list['integral'];
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $int);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    } elseif ($extra_common_param == '5') {
                        //修改订单状态
                        $info['pay_status'] = 3;//修改为第一笔和第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time'] = time();//支付时间
                        $result = M('xgj_furnish_order_info')->where(array('order_code' => $order_id))->data($info)->save();  
                    } elseif ($extra_common_param == '6') {
                        //修改订单状态
                        $info['pay_status'] = 4;//修改为第二笔和第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time'] = time();//支付时间
                        $result = M('xgj_furnish_order_info')->where(array('order_code' => $order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as intd,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('i.order_code' => $order_id))->find();
                            $int=$list['intd'] + $list['integral'];
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $int);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    } elseif ($extra_common_param == '7') {
                        //修改订单状态
                        $info['pay_status'] = 1;//修改为三笔一起支付（全部支付）
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time'] = time();//支付时间
                        $result = M('xgj_furnish_order_info')->where(array('order_code' => $order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as intd,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('i.order_code' => $order_id))->find();
                            $int=$list['intd'] + $list['integral'];
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $int);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    } elseif ($extra_common_param == '8' || $extra_common_param == '9') {
                        //修改订单状态
                        $info['is_pay'] = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time'] = date('Y-m-d H:i:s');//支付时间
                        $result = M('xgj_s_order')->where(array('sn' => $order_id))->data($info)->save();
                    }
                    if ($result !== flase) {
                        //支付成功, 插入支付详情表
                        $data = array(
                            'user_id' => $user_id,
                            'order_id' => $out_trade_no,
                            'notify_id' => $notify_id,
                            'trade_no' => $trade_no,
                            'buyer_id' => $buyer_id,
                            'buyer_email' => $buyer_email,
                            'total_fee' => $total_fee,
                            'trade_status' => $trade_status,
                            'addtime' => date('Y-m-d H:i:s'),
                            'system_id' => $extra_common_param,
                        );
                        if (!M('xgj_eu_payment')->data($data)->add()) {
                            logResult('支付详情信息插入失败:' . $error);
                        }

                        //给用户提示支付成功的页面
                        $_SESSION['pay']['table'] = 'xgj_eu_payment';
                        $_SESSION['pay']['order_id'] = $out_trade_no;
                        $_SESSION['pay']['system_id'] = $extra_common_param;
                        $this->redirect("Order/paySuccess");
                        die;
                        // die('支付成功');

                    } else {
                        //给用户提示错误页面
                        logResult('更新订单状态失败:' . $error);
                        $this->redirect("Order/payError");
                    }
                } else {
                    if ($error === 'is_pay') {
                        //已经支付
                        //给用户提示支付成功的页面
                        $_SESSION['pay']['table'] = 'xgj_eu_payment';
                        $_SESSION['pay']['order_id'] = $out_trade_no;
                        $_SESSION['pay']['system_id'] = $extra_common_param;
                        $this->redirect("Order/paySuccess");
                        die;
                    }
                    //给用户提示错误页面
                    logResult('支付失败:' . $error);
                    $this->redirect("Order/payError");
                    die;
                }
            } else {
                echo "trade_status=" . $_GET['trade_status'];
            }

            echo "验证成功<br />";
        } else {
            //验证失败
            echo "验证失败";
        }
    }

    /*支付宝同步验证订单状态,*/
    public function checkOrder($order_id, $total_fee, $seller_id, $extra_common_param, &$error = ''){
        if ($extra_common_param == '1') {
            //欧团,母婴的订单查询代码....
            $order = M('xgj_eu_order')->field('is_pay,order_status,return_status,deal_price')->where(array('sn' => $order_id))->find();

            if (!$order) {
                $error = '订单不存在';
                return false;
            } elseif ($order['order_status'] == 6) {
                $error = '订单已经取消';
                return false;
            } elseif ($order['return_status'] != 0) {
                $error = '订单正在退货中';
                return false;
            } elseif ($order['is_pay'] == 1) {
                $error = 'is_pay';//已经支付,无需再次支付
                return false;
            } elseif ($order['is_pay'] == 0 && $order['deal_price'] == $total_fee && $seller_id == C('AP_PARTNER') && $order['order_status'] == 0) {
                return true;
            } else {
                $error = '系统错误';
                return false;
            }
        } elseif ($extra_common_param == '2') {
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status')->where(array('order_code' => $order_id))->find();
            if (!$order) {
                $error = '订单不存在';
                return false;
            } elseif ($order['order_status'] == 2) {
                $error = '订单已经取消';
                return false;
            } elseif ($order['shipping_status'] == 5) {
                $error = '订单正在退货中';
                return false;
            } elseif ($order['pay_status'] == 2) {
                $error = 'is_pay';//已经支付,无需再次支付
                return false;
            } elseif ($order['pay_status'] == 0 && 500 == $total_fee && $seller_id == C('AP_PARTNER')) {
                return true;
            } else {
                $error = '系统错误';
                return false;
            }
        } elseif ($extra_common_param == '3') {
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount')->where(array('order_code' => $order_id))->find();
            //系统信息
            $quote_info=M('xgj_furnish_order_detail d')->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
            //付款金额 
            $total_price = '';
            foreach ($quote_info as $k => $v) {
                $total_price += ($v['quote_price'] * $v['ecprice']) / 100;
            }
            $goods_amount = $total_price;
            //$goods_amount=0.01;
            if (!$order) {
                $error = '订单不存在';
                return false;
            } elseif ($order['order_status'] == 2) {
                $error = '订单已经取消';
                return false;
            } elseif ($order['shipping_status'] == 5) {
                $error = '订单正在退货中';
                return false;
            } elseif ($order['pay_status'] == 3) {
                $error = 'is_pay';//已经支付,无需再次支付
                return false;
            } elseif ($order['pay_status'] == 2 && $goods_amount == $total_fee && $seller_id == C('AP_PARTNER')) {
                return true;
            } else {
                $error = '系统错误';
                return false;
            }
        } elseif ($extra_common_param == '4') {
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount')->where(array('order_code' => $order_id))->find();
            //系统信息
            $quote_info=M('xgj_furnish_order_detail d')->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
            $total_price = '';
            foreach ($quote_info as $k => $v) {
                $total_price += $v['quote_price'] * (100 - $v['ecprice']) / 100;
            }
            //付款金额 
            $goods_amount = $total_price - 500;
            // $goods_amount=0.01;
            if (!$order) {
                $error = '订单不存在';
                return false;
            } elseif ($order['order_status'] == 2) {
                $error = '订单已经取消';
                return false;
            } elseif ($order['shipping_status'] == 5) {
                $error = '订单正在退货中';
                return false;
            } elseif ($order['pay_status'] == 4) {
                $error = 'is_pay';//已经支付,无需再次支付
                return false;
            } elseif ($order['pay_status'] == 3 && $goods_amount == $total_fee && $seller_id == C('AP_PARTNER')) {
                return true;
            } else {
                $error = '系统错误';
                return false;
            }
        } elseif ($extra_common_param == '5') {
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount')->where(array('order_code' => $order_id))->find();
            //系统信息
            $quote_info=M('xgj_furnish_order_detail d')->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
            $total_price = '';
            foreach ($quote_info as $k => $v) {
                $total_price += ($v['quote_price'] * $v['ecprice']) / 100;
            }
            //付款金额 
            $goods_amount = $total_price + 500;
            // $goods_amount=0.01;
            if (!$order) {
                $error = '订单不存在';
                return false;
            } elseif ($order['order_status'] == 2) {
                $error = '订单已经取消';
                return false;
            } elseif ($order['shipping_status'] == 5) {
                $error = '订单正在退货中';
                return false;
            } elseif ($order['pay_status'] == 3) {
                $error = 'is_pay';//已经支付,无需再次支付
                return false;
            } elseif ($order['pay_status'] == 0 && $goods_amount == $total_fee && $seller_id == C('AP_PARTNER')) {
                return true;
            } else {
                $error = '系统错误';
                return false;
            }
        } elseif ($extra_common_param == '6') {
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount')->where(array('order_code' => $order_id))->find();
            //系统信息
            $quote_info=M('xgj_furnish_order_detail d')->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
            $total_price = '';
            foreach ($quote_info as $k => $v) {
                $total_price += $v['quote_price'];
            }
            //付款金额 
            $goods_amount = $total_price - 500;
            // $goods_amount=0.01;
            if (!$order) {
                $error = '订单不存在';
                return false;
            } elseif ($order['order_status'] == 2) {
                $error = '订单已经取消';
                return false;
            } elseif ($order['shipping_status'] == 5) {
                $error = '订单正在退货中';
                return false;
            } elseif ($order['pay_status'] == 4) {
                $error = 'is_pay';//已经支付,无需再次支付
                return false;
            } elseif ($order['pay_status'] == 2 && $goods_amount == $total_fee && $seller_id == C('AP_PARTNER')) {
                return true;
            } else {
                $error = '系统错误';
                return false;
            }
        } elseif ($extra_common_param == '7') {
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount')->where(array('order_code' => $order_id))->find();
            //系统信息
            $quote_info=M('xgj_furnish_order_detail d')->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
            $total_price = '';
            foreach ($quote_info as $k => $v) {
                $total_price += $v['quote_price'];
            }
            $goods_amount = $total_price;
            // $goods_amount=0.01;
            if (!$order) {
                $error = '订单不存在';
                return false;
            } elseif ($order['order_status'] == 2) {
                $error = '订单已经取消';
                return false;
            } elseif ($order['shipping_status'] == 5) {
                $error = '订单正在退货中';
                return false;
            } elseif ($order['pay_status'] == 1) {
                $error = 'is_pay';//已经支付,无需再次支付
                return false;
            } elseif ($order['pay_status'] == 0 && $goods_amount == $total_fee && $seller_id == C('AP_PARTNER')) {
                return true;
            } else {
                $error = '系统错误';
                return false;
            }
        } elseif ($extra_common_param == '8' || $extra_common_param == '9') {
            //【海外超市】查询代码....
            $order = M('xgj_s_order')->field('is_pay,order_status,return_status,deal_price')->where(array('sn' => $order_id))->find();
            //$order['deal_price']=0.01;
            if (!$order) {
                $error = '订单不存在';
                return false;
            } elseif ($order['order_status'] == 6) {
                $error = '订单已经取消';
                return false;
            } elseif ($order['return_status'] != 0) {
                $error = '订单正在退货中';
                return false;
            } elseif ($order['is_pay'] == 1) {
                $error = 'is_pay';//已经支付,无需再次支付
                return false;
            } elseif ($order['is_pay'] == 0 && $order['deal_price'] == $total_fee && $seller_id == C('AP_PARTNER') && $order['order_status'] == 0) {
                return true;
            } else {
                $error = '系统错误';
                return false;
            }
        } else {
            $error = '自定义系统id错误, 应该为1或者2,现在为:' . $extra_common_param;
            return false;
        }
    }

    // 支付宝异步跳转处理
    public function notifyurl(){
        layout(false);
        $alipay_config = C('alipay');
        vendor('Alipay.lib.Notify','','.class.php');
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {//验证成功

            //接受数据
            $user_id            = $_SESSION['user']['userId'];
            $out_trade_no       = $_POST['out_trade_no'];//商户订单号
            $trade_no           = $_POST['trade_no'];//支付宝交易号
            $trade_status       = $_POST['trade_status'];//交易状态
            $total_fee          = $_POST['total_fee'];//总交易金额
            $seller_id          = $_POST['seller_id'];//卖家账户号
            $notify_id          = $_POST['notify_id'];//通知校验ID
            $buyer_id           = $_POST['buyer_id'];//买家支付宝账户号
            $buyer_email        = $_POST['buyer_email'];//买家支付宝账号
            $extra_common_param = $_POST['extra_common_param'];//自定义参数

            $order_id=substr($out_trade_no,0,18);
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
            );

            if($trade_status == 'TRADE_FINISHED') {
                if($trade_status_r = $this->checksOrder($order_id, $total_fee ,$seller_id, $trade_status, $trade_no, $extra_common_param, $error)){
                    if($trade_status_r == 'TRADE_FINISHED'){
                        //交易状态: 交易成功->交易完成
                        if(!M('xgj_eu_payment')->where(array('trade_no'=>$trade_no))->data(array('trade_status'=>'TRADE_FINISHED'))->save()){
                            logResult('TRADE_FINISHED更改交易状态: 交易成功->交易完成失败');
                        }
                    }else{
                        //不是高级版本的, 不支持退货功能
                        if($extra_common_param=='1'){
                            //修改订单状态
                            $info['is_pay']       = 1;//修改为已经支付
                            $info['order_status'] = 1;//修改为等待发货
                            $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                            $result               = M('xgj_eu_order')->where(array('sn'=>$order_id))->data($info)->save();
                            if($result){
                                $id=M('xgj_eu_order')->where(array('sn'=>$order_id))->getField('id');
                                $result              = M('xgj_eu_split_order')->where(array('order_id'=>$id))->data(array('order_status'=>1))->save();
                            }
                        }elseif($extra_common_param=='2'){
                            //修改订单状态
                            // $info['pay_status']   = 2;//修改为第一笔已经支付500
                            // $info['order_status'] = 7;//修改为等待发货
                            //临时
                            $info['pay_status'] = 4;//修改为第三笔已经支付
                            $info['order_status'] = 8;//修改为等待发货
                            //临时
                            $info['pay_time']     = time();//支付时间
                            $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        }elseif($extra_common_param=='3'){
                            //修改订单状态
                            $info['pay_status']   = 3;//修改为第二笔已经支付
                            $info['order_status'] = 7;//修改为等待发货
                            $info['pay_time']     = time();//支付时间
                            $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        }elseif($extra_common_param=='4'){
                            //修改订单状态
                            $info['pay_status']   = 4;//修改为第三笔已经支付
                            $info['order_status'] = 8;//修改为等待发货
                            $info['pay_time']     = time();//支付时间
                            $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                            if ($result) {
                                $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as intd,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('i.order_code' => $order_id))->find();
                                $int=$list['intd'] + $list['integral'];
                                $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $int);
                                if(!empty($list['intd']) && $res!==false){
                                    $infos['order']  = $order_id;
                                    $infos['user_id']   = $list['user_id'];
                                    $infos['user_name'] = $list['user_name'];
                                    $infos['integral']  = $list['intd'];
                                    $infos['class']  = 1;
                                    $infos['status'] = 2;
                                    $infos['time']  = time();
                                    if(M('xgj_user_integral')->data($infos)->add()==false){
                                        logResult('返还积分流水添加失败');
                                    }
                                }
                            }
                        }elseif($extra_common_param=='5'){
                            //修改订单状态
                            $info['pay_status']   = 3;//修改为第一笔和第二笔已经支付
                            $info['order_status'] = 7;//修改为等待发货
                            $info['pay_time']     = time();//支付时间
                            $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        }elseif($extra_common_param=='6'){
                            //修改订单状态
                            $info['pay_status']   = 4;//修改为第二笔和第三笔已经支付
                            $info['order_status'] = 8;//修改为等待发货
                            $info['pay_time']     = time();//支付时间
                            $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                            if ($result) {
                                $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as intd,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('i.order_code' => $order_id))->find();
                                $int=$list['intd'] + $list['integral'];
                                $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $int);
                                if(!empty($list['intd']) && $res!==false){
                                    $infos['order']  = $order_id;
                                    $infos['user_id']   = $list['user_id'];
                                    $infos['user_name'] = $list['user_name'];
                                    $infos['integral']  = $list['intd'];
                                    $infos['class']  = 1;
                                    $infos['status'] = 2;
                                    $infos['time']  = time();
                                    if(M('xgj_user_integral')->data($infos)->add()==false){
                                        logResult('返还积分流水添加失败');
                                    }
                                }
                            }
                        }elseif($extra_common_param=='7'){
                            //修改订单状态
                            $info['pay_status']   = 1;//修改为三笔一起支付
                            $info['order_status'] = 7;//修改为等待发货
                            $info['pay_time']     = time();//支付时间
                            $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                            if ($result) {
                                $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as intd,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('i.order_code' => $order_id))->find();
                                $int=$list['intd'] + $list['integral'];
                                $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $int);
                                if(!empty($list['intd']) && $res!==false){
                                    $infos['order']  = $order_id;
                                    $infos['user_id']   = $list['user_id'];
                                    $infos['user_name'] = $list['user_name'];
                                    $infos['integral']  = $list['intd'];
                                    $infos['class']  = 1;
                                    $infos['status'] = 2;
                                    $infos['time']  = time();
                                    if(M('xgj_user_integral')->data($infos)->add()==false){
                                        logResult('返还积分流水添加失败');
                                    }
                                }
                            }
                        }elseif($extra_common_param == '8' || $extra_common_param == '9'){
                            //修改订单状态【海外超市】
                            $info['is_pay']       = 1;//修改为已经支付
                            $info['order_status'] = 1;//修改为等待发货
                            $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                            $result              = M('xgj_s_order')->where(array('sn'=>$order_id))->data($info)->save();
                        }

                        if($result){
                            //支付成功, 插入支付详情表
                            if(!M('xgj_eu_payment')->data($data)->add()){
                                logResult('TRADE_FINISHED支付详情信息插入失败 data:'.createLinkstring($data));
                            }

                        }else{
                            logResult('TRADE_FINISHED更新订单状态失败 lastSql:'.M('xgj_eu_order')->_sql());
                        }
                    }
                }else{
                    logResult('TRADE_FINISHED'.$error);

                }
            }elseif ($trade_status == 'TRADE_SUCCESS') {
                if(checkOrder($order_id, $total_fee ,$seller_id, $trade_status, $trade_no, $extra_common_param, $error)){
                    if($extra_common_param=='1'){
                        //修改订单状态
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_eu_order')->where(array('sn'=>$order_id))->data($info)->save();
                        if($result){
                            $id=M('xgj_eu_order')->where(array('sn'=>$order_id))->getField('id');
                            $result              = M('xgj_eu_split_order')->where(array('order_id'=>$id))->data(array('order_status'=>1))->save();
                        }
                    }elseif($extra_common_param=='2'){
                        //修改订单状态
                        // $info['pay_status']   = 2;//修改为第一笔已经支付500
                        // $info['order_status'] = 7;//修改为等待发货
                        //临时
                        $info['pay_status'] = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        //临时
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }elseif($extra_common_param=='3'){
                        //修改订单状态
                        $info['pay_status']   = 3;//修改为第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }elseif($extra_common_param=='4'){
                        //修改订单状态
                        $info['pay_status']   = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }elseif($extra_common_param=='5'){
                        //修改订单状态
                        $info['pay_status']   = 3;//修改为第一笔和第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }elseif($extra_common_param=='6'){
                        //修改订单状态
                        $info['pay_status']   = 4;//修改为第二笔和第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }elseif($extra_common_param=='7'){
                        //修改订单状态
                        $info['pay_status']   = 1;//修改为三笔一起支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }elseif($extra_common_param == '8' || $extra_common_param == '9'){
                        //修改订单状态【海外超市】
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_s_order')->where(array('sn'=>$order_id))->data($info)->save();
                    }
                if($result!==flase){
                    //支付成功, 插入支付详情表
                    if(!M('xgj_eu_payment')->data($data)->add()){
                        logResult('TRADE_SUCCESS支付详情信息插入失败 data:'.createLinkstring($data));
                    }

                }else{
                    logResult('TRADE_SUCCESS更新订单状态失败 lastSql:'.M('xgj_eu_order')->_sql());
                }
            }else{
                logResult('TRADE_SUCCESS'.$error);

            }
        }
            echo "success";     //请不要修改或删除
        }else{
            echo "fail";
        }
    }

    /*支付宝异步验证订单状态,*/
    public function checksOrder($order_id, $total_fee ,$seller_id, $trade_status, $trade_no, $extra_common_param, &$error=''){
        $trade_status_d = M('xgj_eu_payment')->where(array('trade_no'=>$trade_no))->getField('trade_status');
        if($extra_common_param=='1'){
            //欧团,母婴的订单查询代码....
            $order = M('xgj_eu_order')->field('is_pay,order_status,return_status,deal_price')->where(array('sn'=>$order_id))->find();
            if(!$order){
                $error = '订单不存在';
                return false;
            }elseif($order['order_status']==6){
                $error = '订单已经取消';
                return false;
            }elseif($order['return_status']!=0){
                $error = '订单正在退货中';
                return false;
            }elseif($trade_status=='TRADE_FINISHED' && $trade_status_d == 'TRADE_SUCCESS'){
                return 'TRADE_FINISHED';//交易完成消息
            }elseif($order['is_pay']==1){
                $error = '已经支付,无需再次支付';
                return false;
            }elseif($order['is_pay']==0 && $order['deal_price']==$total_fee && $seller_id == C('AP_PARTNER') && $order['order_status']==0){
                return true;
            }else{
                $error = '系统错误';
                return false;
            }
        }elseif($extra_common_param=='2'){
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status')->where(array('order_code'=>$order_id))->find();
             $goods_amount=500;
            if(!$order){
                $error = '订单不存在';
                return false;
            }elseif($order['order_status']==2){
                $error = '订单已经取消';
                return false;
            }elseif($order['shipping_status']==5){
                $error = '订单正在退货中';
                return false;
            }elseif($order['pay_status']==2){
                $error = '第一笔已支付,无需再次支付';
                return false;
            }elseif($order['pay_status']==0 && $goods_amount==$total_fee && $seller_id == C('AP_PARTNER')){
                return true;
            }else{
                $error = '系统错误';
                return false;
            }
        }elseif($extra_common_param=='3'){
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount')->where(array('order_code'=>$order_id))->find();
            //系统信息
            $quote_info=M('xgj_furnish_order_detail d')->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
            //付款金额 
            $total_price='';
            foreach ($quote_info as $k=>$v){
                $total_price += ($v['quote_price']*$v['ecprice'])/100;
            }
            $goods_amount=$total_price;
            // $goods_amount=0.01;
            if(!$order){
                $error = '订单不存在';
                return false;
            }elseif($order['order_status']==2){
                $error = '订单已经取消';
                return false;
            }elseif($order['shipping_status']==5){
                $error = '订单正在退货中';
                return false;
            }elseif($order['pay_status']==3){
                $error = '第二笔已支付,无需再次支付';
                return false;
            }elseif($order['pay_status']==2 && $goods_amount==$total_fee && $seller_id == C('AP_PARTNER')){
                return true;
            }else{
                $error = '系统错误';
                return false;
            }
        }elseif($extra_common_param=='4'){
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount')->where(array('order_code'=>$order_id))->find();
            //系统信息
            $quote_info=M('xgj_furnish_order_detail d')->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
            $total_price='';
            foreach ($quote_info as $k=>$v){
                $total_price += $v['quote_price']*(100-$v['ecprice'])/100;
            }
            //付款金额 
            $goods_amount=$total_price-500;
            // $goods_amount=0.01;
            if(!$order){
                $error = '订单不存在';
                return false;
            }elseif($order['order_status']==2){
                $error = '订单已经取消';
                return false;
            }elseif($order['shipping_status']==5){
                $error = '订单正在退货中';
                return false;
            }elseif($order['pay_status']==4){
                $error = '第三笔已支付,无需再次支付';
                return false;
            }elseif($order['pay_status']==3 && $goods_amount==$total_fee && $seller_id == C('AP_PARTNER') ){
                return true;
            }else{
                $error = '系统错误';
                return false;
            }
        }elseif($extra_common_param=='5'){
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount')->where(array('order_code'=>$order_id))->find();
            //系统信息
            $quote_info=M('xgj_furnish_order_detail d')->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
            $total_price='';
            foreach ($quote_info as $k=>$v){
                $total_price += ($v['quote_price']*$v['ecprice'])/100;
            }
            //付款金额 
            $goods_amount=$total_price+500;
            // $goods_amount=0.01;
            if(!$order){
                $error = '订单不存在';
                return false;
            }elseif($order['order_status']==2){
                $error = '订单已经取消';
                return false;
            }elseif($order['shipping_status']==5){
                $error = '订单正在退货中';
                return false;
            }elseif($order['pay_status']==3){
                $error = '第一笔款或第二笔款已付,无需再次支付';
                return false;
            }elseif($order['pay_status']==0 && $goods_amount==$total_fee && $seller_id == C('AP_PARTNER')){
                return true;
            }else{
                $error = '系统错误';
                return false;
            }
        }elseif($extra_common_param=='6'){
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount')->where(array('order_code'=>$order_id))->find();
            //系统信息
            $quote_info=M('xgj_furnish_order_detail d')->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
            $total_price='';
            foreach ($quote_info as $k=>$v){
                $total_price += $v['quote_price'];
            }
            //付款金额 
            $goods_amount=$total_price-500;
            // $goods_amount=0.01;
            if(!$order){
                $error = '订单不存在';
                return false;
            }elseif($order['order_status']==2){
                $error = '订单已经取消';
                return false;
            }elseif($order['shipping_status']==5){
                $error = '订单正在退货中';
                return false;
            }elseif($order['pay_status']==4){
                $error = '第二笔款或第三笔款已付,无需再次支付';
                return false;
            }elseif($order['pay_status']==2 && $goods_amount==$total_fee && $seller_id == C('AP_PARTNER')){
                return true;
            }else{
                $error = '系统错误';
                return false;
            }
        }elseif($extra_common_param=='7'){
            //家居的订单查询代码....
            $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount')->where(array('order_code'=>$order_id))->find();
            //系统信息
            $quote_info=M('xgj_furnish_order_detail d')->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where("d.order_id=$order_id")->select();
            $total_price='';
            foreach ($quote_info as $k=>$v){
                $total_price += $v['quote_price'];
            }
            $goods_amount=$total_price;
            // $goods_amount=0.01;
            if(!$order){
                $error = '订单不存在';
                return false;
            }elseif($order['order_status']==2){
                $error = '订单已经取消';
                return false;
            }elseif($order['shipping_status']==5){
                $error = '订单正在退货中';
                return false;
            }elseif($order['pay_status']==1){
                $error = '该订单已全部支付,无需再次支付';
                return false;
            }elseif($order['pay_status']==0 && $goods_amount==$total_fee && $seller_id == C('AP_PARTNER')){
                return true;
            }else{
                $error = '系统错误';
                return false;
            }
        }elseif($extra_common_param == '8' || $extra_common_param == '9'){
            //海外超市的订单查询代码....
            $order = M('xgj_s_order')->field('is_pay,order_status,return_status,deal_price')->where(array('sn'=>$order_id))->find();
           // $order['deal_price']=0.01;
            if(!$order){
                $error = '订单不存在';
                return false;
            }elseif($order['order_status']==6){
                $error = '订单已经取消';
                return false;
            }elseif($order['return_status']!=0){
                $error = '订单正在退货中';
                return false;
            }elseif($trade_status=='TRADE_FINISHED' && $trade_status_d == 'TRADE_SUCCESS'){
                return 'TRADE_FINISHED';//交易完成消息
            }elseif($order['is_pay']==1){
                $error = '已经支付,无需再次支付';
                return false;
            }elseif($order['is_pay']==0 && $order['deal_price']==$total_fee && $seller_id == C('AP_PARTNER') && $order['order_status']==0){
                return true;
            }else{
                $error = '系统错误';
                return false;
            }
        }else{
            $error = '自定义系统id错误, 应该为1或者2,现在为:'.$extra_common_param;
            return false;
        }

    }

    //银联无卡快捷接口处理
    public function  nosign(){
        define('transResvered', "trans_");
        define('cardResvered', "card_");
        define('transResveredKey', "TranReserved");
        define('signatureField', "Signature");
        include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Nopay/util/common.php');
        if ($_POST) {
            $dispatchMap = array(
                // 配置个人网银交易转发地址
                "0001" => U('Pay/b2cNoPaySend'),
                "0004" => U('Pay/b2cNoPaySend'),
                // 配置退款交易转发地址
                "0401" => U('Refund/b2cNoRefundSend'),
                // 配置查询交易转发地址
                "0502" => U('Pay/b2cNoQuerySend'),
            );
            if (count($_POST) > 0) {
                if (isset($_POST['TranType']) && trim($_POST['TranType']) != "") {
                    $dispatchUrl = $dispatchMap[$_POST['TranType']];
                } else {
                    $dispatchUrl = $dispatchMap['0001'];
                }
                $transResvedJson = array();
                $cardInfoJson = array();
                $sendMap = array();
                foreach ($_POST as $key => $value) {
                    if (isEmpty($value)) {
                        continue;
                    }
                    if (startWith($key, transResvered)) {
                        // 组装交易扩展域
                        $key = substr($key, strlen(transResvered));
                        $transResvedJson[$key] = $value;
                    } else{
                        if (startWith($key, cardResvered)) {
                            // 组装有卡交易信息域
                            $key = substr($key, strlen(cardResvered));
                            $cardInfoJson[$key] = $value;
                        } else {
                            $sendMap[$key] = $value;
                        }
                    } 
                }
                $transResvedStr = null;
                $cardResvedStr = null;
                if (count($transResvedJson) > 0) {
                    $transResvedStr = json_encode($transResvedJson);
                }
                if (count($cardInfoJson) > 0) {
                    $cardResvedStr = json_encode($cardInfoJson);
                }
                include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Nopay/util/SecssUtil.class.php');
                $secssUtil = new \SecssUtil();
                if (! isEmpty($transResvedStr)) {
                    $transResvedStr = $secssUtil->decryptData($transResvedStr);
                    $sendMap[transResveredKey] = $transResvedStr;
                }
                if (! isEmpty($cardResvedStr)) {
                    $cardResvedStr = $secssUtil->decryptData($cardResvedStr);
                    $sendMap[cardResveredKey] = $cardResvedStr;
                }
                $securityPropFile = dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Nopay/security.properties";
                $secssUtil->init($securityPropFile);
                $secssUtil->sign($sendMap);
                $sendMap[signatureField] = $secssUtil->getSign();
                unset($_SESSION['chinanopay']);
                $_SESSION['chinanopay'] = $sendMap;
                header("Location:" . $dispatchUrl);
            }
        }
    }

    //银联无卡支付接口处理
    public function b2cNoPaySend(){
        header('Content-Type:text/html;charset=utf-8 ');
        include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Nopay/util/Settings_INI.php');
        $file=dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Nopay/path.properties";
        $settings = new \Settings_INI();
        $settings->load($file);
        $pay_url = $settings->get("pay_url");
        $html="<form name='payment' action='$pay_url' method='POST'>";
        $params = "TranReserved;MerId;MerOrderNo;OrderAmt;CurryNo;TranDate;SplitMethod;BusiType;MerPageUrl;MerBgUrl;SplitType;MerSplitMsg;PayTimeOut;MerResv;Version;BankInstNo;CommodityMsg;Signature;AccessType;AcqCode;OrderExpiryTime;TranType;RemoteAddr;Referred;TranTime;TimeStamp;CardTranData";
        foreach ($_SESSION['chinanopay'] as $k => $v) {
            if (strstr($params, $k)) {
                $html.="<input type='hidden' name = '" . $k . "' value ='" . $v . "'/>";
            }
        }
           $html.=' </form>
            <script language=JavaScript>
                document.payment.submit();
            </script>
        </body>
        </html>';
        echo $html;
        unset($_SESSION['chinanopay']);
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
                $order_id=date('Y',time()).substr($_POST['MerOrderNo'],0,14);
                
                $text = array();
                foreach($_POST as $key=>$value){
                    $text[$key] = urldecode($value);
                }
                if ($secssUtil->verify($text)) {
                    if($text['MerResv']==1){
                         //修改订单状态
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_eu_order')->where(array('sn'=>$order_id))->data($info)->save();
                        if($result){
                           $id=M('xgj_eu_order')->where(array('sn'=>$order_id))->getField('id');
                           $result=M('xgj_eu_split_order')->where(array('order_id'=>$id))->data(array('order_status'=>1))->save();
                        }
                    }else if($text['MerResv']==2){
                        //修改订单状态
                        // $info['pay_status']   = 2;//修改为第一笔已经支付500
                        // $info['order_status'] = 7;//修改为等待发货
                        //临时
                        $info['pay_status'] = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        //临时
                        $info['pay_time']     = time();//支付时间
                        $result       = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }else if($text['MerResv']==3){
                        //修改订单状态
                        $info['pay_status']   = 3;//修改为第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result        = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }else if($text['MerResv']==4){
                        //修改订单状态
                        $info['pay_status']   = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result        = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==5){
                        //修改订单状态
                        $info['pay_status']   = 3;//修改为第一笔和第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result        = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }else if($text['MerResv']==6){
                        //修改订单状态
                        $info['pay_status']   = 4;//修改为第二笔和第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==7){
                        //修改订单状态
                        $info['pay_status']   = 1;//修改为三笔一起支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==8 || $text['MerResv']==9){
                         //修改订单状态
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_s_order')->where(array('sn'=>$order_id))->data($info)->save();
                    }
                    if($result!==flase){
                        //支付成功, 插入支付详情表
                        $data_=array(
                            'order_id'      =>date('Y',time()).$text['MerOrderNo'],
                            'version'       =>$text['Version'],
                            'tran_date'     =>$text['TranDate'],
                            'tran_time'     =>$text['TranTime'],
                            'total_fee'     =>$text['OrderAmt']/100,
                            'add_time'      =>time(),
                            'tran_type'     =>$text['TranType'],
                            'busi_type'     =>$text['BusiType'],
                            'order_status'  =>$text['OrderStatus'],
                            'bank_inst_no'  =>$text['BankInstNo'],
                            'complete_date' =>$text['CompleteDate'],
                            'complete_time' =>$text['CompleteTime'],
                            'curry_no'      =>$text['CurryNo'],
                            'trade_no'      =>$text['AcqSeqId'],
                            'mer_id'        =>$text['MerId'],
                            'signature'     =>$text['Signature'],
                            'acq_date'      =>$text['AcqDate'],
                            'class_id'      =>$text['MerResv'],
                        );
                        if(!M('xgj_chinapay')->data($data_)->add()){
                            echo '支付详情信息插入失败';
                        }
                        //给用户提示支付成功的页面
                        $_SESSION['pay']['table']='xgj_chinapay';
                        $_SESSION['pay']['order_id'] = date('Y',time()).$text['MerOrderNo'];
                        $_SESSION['pay']['system_id'] = $text['MerResv'];
                        $this->redirect("Order/paySuccess");
                        die;
                        // die('支付成功');
                    }else{
                        //给用户提示错误页面
                        echo '更新订单状态失败111';die;
                        $this->redirect("Order/payError");die;
                    }
                    
                } else {
                    //给用户提示错误页面
                    echo '支付失败123';die;
                    $this->redirect("Order/payError");
                    die;
                }
            }
        }
    }

    /*银联无卡支付支付后台应答处理*/
    public function pgNoReturn(){
        if ($_POST) {
            if (count($_POST) > 0) {        
                include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Nopay/util/common.php');
                include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Nopay/util/SecssUtil.class.php');
                $secssUtil = new \SecssUtil();
                $securityPropFile = dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Nopay/security.properties";
                $secssUtil->init($securityPropFile);
                $order_id=date('Y',time()).substr($_POST['MerOrderNo'],0,14);
                $text = array();
                foreach($_POST as $key=>$value){
                    $text[$key] = urldecode($value);
                }
                if ($secssUtil->verify($_POST)) {
                    if($text['MerResv']==1){
                         //修改订单状态
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_eu_order')->where(array('sn'=>$order_id))->data($info)->save();
                        if($result){
                           $id=M('xgj_eu_order')->where(array('sn'=>$order_id))->getField('id');
                           $result=M('xgj_eu_split_order')->where(array('order_id'=>$id))->data(array('order_status'=>1))->save();
                        }
                    }else if($text['MerResv']==2){
                        //修改订单状态
                        // $info['pay_status']   = 2;//修改为第一笔已经支付500
                        // $info['order_status'] = 7;//修改为等待发货
                        //临时
                        $info['pay_status'] = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        //临时
                        $info['pay_time']     = time();//支付时间
                        $result       = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }else if($text['MerResv']==3){
                        //修改订单状态
                        $info['pay_status']   = 3;//修改为第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result        = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }else if($text['MerResv']==4){
                        //修改订单状态
                        $info['pay_status']   = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result        = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==5){
                        //修改订单状态
                        $info['pay_status']   = 3;//修改为第一笔和第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result        = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }else if($text['MerResv']==6){
                        //修改订单状态
                        $info['pay_status']   = 4;//修改为第二笔和第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==7){
                        //修改订单状态
                        $info['pay_status']   = 1;//修改为三笔一起支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==8 || $text['MerResv']==9){
                         //修改订单状态
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_s_order')->where(array('sn'=>$order_id))->data($info)->save();
                    }
                    if($result!==flase){
                        //支付成功, 插入支付详情表
                        if(M('xgj_chinapay')->where(array("order_id"=>date('Y',time()).$text['MerOrderNo']))->count('id')!=1){
                            $data_=array(
                                'order_id'      =>date('Y',time()).$text['MerOrderNo'],
                                'version'       =>$text['Version'],
                                'tran_date'     =>$text['TranDate'],
                                'tran_time'     =>$text['TranTime'],
                                'total_fee'     =>$text['OrderAmt']/100,
                                'add_time'      =>time(),
                                'tran_type'     =>$text['TranType'],
                                'busi_type'     =>$text['BusiType'],
                                'order_status'  =>$text['OrderStatus'],
                                'bank_inst_no'  =>$text['BankInstNo'],
                                'complete_date' =>$text['CompleteDate'],
                                'complete_time' =>$text['CompleteTime'],
                                'curry_no'      =>$text['CurryNo'],
                                'trade_no'      =>$text['AcqSeqId'],
                                'mer_id'        =>$text['MerId'],
                                'signature'     =>$text['Signature'],
                                'acq_date'      =>$text['AcqDate'],
                                'class_id'      =>$text['MerResv'],
                            );
                            if(!M('xgj_chinapay')->data($data_)->add()){
                                echo '支付详情信息插入失败';
                            }
                        }
                        //给用户提示支付成功的页面
                        $_SESSION['pay']['table']='xgj_chinapay';
                        $_SESSION['pay']['order_id'] = date('Y',time()).$text['MerOrderNo'];
                        $_SESSION['pay']['system_id'] = $text['MerResv'];
                        $this->redirect("Order/paySuccess");
                        die;
                    } else {
                        echo '支付失败111222';die;
                        $this->redirect("Order/payError");
                        die;
                    }
                }
            }
        }
    }

    //银联支付接口处理
    public function sign(){
        layout(false);
        define('transResvered', "trans_");
        define('cardResvered', "card_");
        define('transResveredKey', "TranReserved");
        define('signatureField', "Signature");
        include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/common.php');
        include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/SecssUtil.class.php');
        if ($_POST) {
            $dispatchMap = array(
                // 配置个人网银交易转发地址
                "0001" => U('Pay/b2cPaySend'),
                "0004" => U('Pay/b2cPaySend'),
                // 配置退款交易转发地址
                "0401" => U('Refund/b2cRefundSend'),
                // 配置查询交易转发地址
                "0502" => U('Pay/b2cQuerySend'),
            );
            if (count($_POST) > 0) {
                if (isset($_POST['TranType']) && trim($_POST['TranType']) != "") {
                    $dispatchUrl = $dispatchMap[$_POST['TranType']];
                } else {
                    $dispatchUrl = $dispatchMap['0001'];
                }
                $transResvedJson = array();
                $cardInfoJson = array();
                $sendMap = array();
                foreach ($_POST as $key => $value) {
                    if (isEmpty($value)) {
                        continue;
                    }
                    if (startWith($key, transResvered)) {
                        // 组装交易扩展域
                        $key = substr($key, strlen(transResvered));
                        $transResvedJson[$key] = $value;
                    } else {
                        if (startWith($key, cardResvered)) {
                            // 组装有卡交易信息域
                            $key = substr($key, strlen(cardResvered));
                            $cardInfoJson[$key] = $value;
                        } else {
                            $sendMap[$key] = $value;
                        }
                    }
                }
                $transResvedStr = null;
                $cardResvedStr = null;
                if (count($transResvedJson) > 0) {
                    $transResvedStr = json_encode($transResvedJson);
                }
                if (count($cardInfoJson) > 0) {
                    $cardResvedStr = json_encode($cardInfoJson);
                }
                $secssUtil = new \SecssUtil();
                if (! isEmpty($transResvedStr)) {
                    $transResvedStr = $secssUtil->decryptData($transResvedStr);
                    $sendMap[transResveredKey] = $transResvedStr;
                }
                if (! isEmpty($cardResvedStr)) {
                    $cardResvedStr = $secssUtil->decryptData($cardResvedStr);
                    $sendMap[cardResveredKey] = $cardResvedStr;
                }
                $securityPropFile = dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Chinapay/security.properties";
                $secssUtil->init($securityPropFile);
                $secssUtil->sign($sendMap);
                $sendMap[signatureField] = $secssUtil->getSign();
                unset($_SESSION['chinapay']);
                $_SESSION['chinapay'] = $sendMap;
                header("Location:" . $dispatchUrl);
            }
        }
    }

    //银联支付接口处理
    public function b2cPaySend(){
        header('Content-Type:text/html;charset=utf-8 ');
        include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/Settings_INI.php');
        $file=dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/path.properties';
        $settings = new \Settings_INI();
        $settings->load($file);
        $pay_url = $settings->get("pay_url");
        $html="<form name='payment' action='$pay_url' method='POST'>";
        $params = "TranReserved;MerId;MerOrderNo;OrderAmt;CurryNo;TranDate;SplitMethod;BusiType;MerPageUrl;MerBgUrl;SplitType;MerSplitMsg;PayTimeOut;MerResv;Version;BankInstNo;CommodityMsg;Signature;AccessType;AcqCode;OrderExpiryTime;TranType;RemoteAddr;Referred;TranTime;TimeStamp;CardTranData";
        foreach ($_SESSION['chinapay'] as $k => $v) {
            if (strstr($params, $k)) {
                $html.="<input type='hidden' name = '" . $k . "' value ='" . $v . "'/>";
            }
        }
           $html.=' </form>
            <script language=JavaScript>
                document.payment.submit();
            </script>
        </body>
        </html>';
        echo $html;   
        unset($_SESSION['chinapay']); 
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
                $order_id=date('Y',time()).substr($_POST['MerOrderNo'],0,14);
                $text = array();
                foreach($_POST as $key=>$value){
                    $text[$key] = urldecode($value);
                }

                if ($secssUtil->verify($text)) {
                    if($text['MerResv']==1){
                         //修改订单状态
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_eu_order')->where(array('sn'=>$order_id))->data($info)->save();
                        if($result){
                            $id=M('xgj_eu_order')->where(array('sn'=>$order_id))->getField('id');
                            $result              = M('xgj_eu_split_order')->where(array('order_id'=>$id))->data(array('order_status'=>1))->save();
                        }
                    }else if($text['MerResv']==2){
                        //修改订单状态
                        // $info['pay_status']   = 2;//修改为第一笔已经支付500
                        // $info['order_status'] = 7;//修改为等待发货
                        //临时
                        $info['pay_status'] = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        //临时
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();

                    }else if($text['MerResv']==3){
                        //修改订单状态
                        $info['pay_status']   = 3;//修改为第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }else if($text['MerResv']==4){
                        //修改订单状态
                        $info['pay_status']   = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==5){
                        //修改订单状态
                        $info['pay_status']   = 3;//修改为第一笔和第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }else if($text['MerResv']==6){
                        //修改订单状态
                        $info['pay_status']   = 4;//修改为第二笔和第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==7){
                        //修改订单状态
                        $info['pay_status']   = 1;//修改为三笔一起支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==8 || $text['MerResv']==9){
                         //修改订单状态
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_s_order')->where(array('sn'=>$order_id))->data($info)->save();
                    }
                    if($result!==flase){
                        //支付成功, 插入支付详情表
                        $data_=array(
                            'order_id'      =>date('Y',time()).$text['MerOrderNo'],
                            'version'       =>$text['Version'],
                            'tran_date'     =>$text['TranDate'],
                            'tran_time'     =>$text['TranTime'],
                            'total_fee'     =>$text['OrderAmt']/100,
                            'add_time'      =>time(),
                            'tran_type'     =>$text['TranType'],
                            'busi_type'     =>$text['BusiType'],
                            'order_status'  =>$text['OrderStatus'],
                            'bank_inst_no'  =>$text['BankInstNo'],
                            'complete_date' =>$text['CompleteDate'],
                            'complete_time' =>$text['CompleteTime'],
                            'curry_no'      =>$text['CurryNo'],
                            'trade_no'      =>$text['AcqSeqId'],
                            'mer_id'        =>$text['MerId'],
                            'signature'     =>$text['Signature'],
                            'acq_date'      =>$text['AcqDate'],
                            'class_id'      =>$text['MerResv'],
                            );
                        if(!M('xgj_chinapay')->data($data_)->add()){
                            echo '支付详情信息插入失败';
                        }
                        //给用户提示支付成功的页面
                        $_SESSION['pay']['table']='xgj_chinapay';
                        $_SESSION['pay']['order_id'] = date('Y',time()).$text['MerOrderNo'];
                        $_SESSION['pay']['system_id'] = $text['MerResv'];
                        $this->redirect("Order/paySuccess");
                        die;
                    }else{
                        //给用户提示错误页面
                        echo '更新订单状态失败';
                        $this->redirect("Order/payError");die;
                    }
                } else {
                    //给用户提示错误页面
                    echo '支付失败';
                    $this->redirect("Order/payError");
                    die;
                }
            }
        }
    }

    //银联支付前台应答处理
    public function pgReturn(){
        if ($_POST) {
            if (count($_POST) > 0) {
                include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/common.php');
                include(dirname($_SERVER['DOCUMENT_ROOT']).'/spss/Chinapay/util/SecssUtil.class.php');
                $secssUtil = new \SecssUtil();
                $securityPropFile =dirname($_SERVER['DOCUMENT_ROOT'])."/spss/Chinapay/security.properties";
                $secssUtil->init($securityPropFile);
                $order_id=date('Y',time()).substr($_POST['MerOrderNo'],0,14);
                $text = array();
                foreach($_POST as $key=>$value){
                    $text[$key] = $value;
                }

                if ($secssUtil->verify($text)) {
                    if($text['MerResv']==1){
                         //修改订单状态
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_eu_order')->where(array('sn'=>$order_id))->data($info)->save();
                        if($result){
                           $id=M('xgj_eu_order')->where(array('sn'=>$order_id))->getField('id');
                           $result=M('xgj_eu_split_order')->where(array('order_id'=>$id))->data(array('order_status'=>1))->save();
                        }
                    }else if($text['MerResv']==2){
                        //修改订单状态
                        // $info['pay_status']   = 2;//修改为第一笔已经支付500
                        // $info['order_status'] = 7;//修改为等待发货
                        //临时
                        $info['pay_status'] = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        //临时
                        $info['pay_time']     = time();//支付时间
                        $result       = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }else if($text['MerResv']==3){
                        //修改订单状态
                        $info['pay_status']   = 3;//修改为第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result        = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }else if($text['MerResv']==4){
                        //修改订单状态
                        $info['pay_status']   = 4;//修改为第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result        = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==5){
                        //修改订单状态
                        $info['pay_status']   = 3;//修改为第一笔和第二笔已经支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result        = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                    }else if($text['MerResv']==6){
                        //修改订单状态
                        $info['pay_status']   = 4;//修改为第二笔和第三笔已经支付
                        $info['order_status'] = 8;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==7){
                        //修改订单状态
                        $info['pay_status']   = 1;//修改为三笔一起支付
                        $info['order_status'] = 7;//修改为等待发货
                        $info['pay_time']     = time();//支付时间
                        $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
                        if ($result) {
                            $list = M('xgj_furnish_order_info i')->field("sum(d.integral) as int,i.user_id,u.integral,u.user_name")->join('xgj_furnish_order_detail d on i.order_id=d.order_id')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->select();
                            $res = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['intd'] + $list['integral']);
                            if(!empty($list['intd']) && $res!==false){
                                $infos['order']  = $order_id;
                                $infos['user_id']   = $list['user_id'];
                                $infos['user_name'] = $list['user_name'];
                                $infos['integral']  = $list['intd'];
                                $infos['class']  = 1;
                                $infos['status'] = 2;
                                $infos['time']  = time();
                                if(M('xgj_user_integral')->data($infos)->add()==false){
                                    logResult('返还积分流水添加失败');
                                }
                            }
                        }
                    }else if($text['MerResv']==8 || $text['MerResv']==9){
                         //修改订单状态
                        $info['is_pay']       = 1;//修改为已经支付
                        $info['order_status'] = 1;//修改为等待发货
                        $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                        $result               = M('xgj_s_order')->where(array('sn'=>$order_id))->data($info)->save();
                    }
                    if($result!==flase){
                        //支付成功, 插入支付详情表
                        if(M('xgj_chinapay')->where(array("order_id"=>date('Y',time()).$text['MerOrderNo']))->count('id')!=1){
                            $data_=array(
                                'order_id'      =>date('Y',time()).$text['MerOrderNo'],
                                'version'       =>$text['Version'],
                                'tran_date'     =>$text['TranDate'],
                                'tran_time'     =>$text['TranTime'],
                                'total_fee'     =>$text['OrderAmt']/100,
                                'add_time'      =>time(),
                                'tran_type'     =>$text['TranType'],
                                'busi_type'     =>$text['BusiType'],
                                'order_status'  =>$text['OrderStatus'],
                                'bank_inst_no'  =>$text['BankInstNo'],
                                'complete_date' =>$text['CompleteDate'],
                                'complete_time' =>$text['CompleteTime'],
                                'curry_no'      =>$text['CurryNo'],
                                'trade_no'      =>$text['AcqSeqId'],
                                'mer_id'        =>$text['MerId'],
                                'signature'     =>$text['Signature'],
                                'acq_date'      =>$text['AcqDate'],
                                'class_id'      =>$text['MerResv'],
                            );
                            if(!M('xgj_chinapay')->data($data_)->add()){
                                echo '支付详情信息插入失败';
                            }
                        }
                        //给用户提示支付成功的页面
                        $_SESSION['pay']['table']='xgj_chinapay';
                        $_SESSION['pay']['order_id'] = date('Y',time()).$text['MerOrderNo'];
                        $_SESSION['pay']['system_id'] = $text['MerResv'];
                        $this->redirect("Order/paySuccess");
                        die;
                    } else {
                        echo '支付失败';
                        $this->redirect("Order/payError");
                        die;
                    }
                } else {
                    echo '支付失败';
                    $this->redirect("Order/payError");
                    die;
                }
            }
        }
    }  
}

