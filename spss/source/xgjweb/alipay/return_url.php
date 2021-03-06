<?php
/*
支付宝同步消息
 */
require '../libs/common/initialize.php';
require WWW_DIR . '/alipay/config.php';
require WWW_DIR . '/alipay/lib/core.php';
require WWW_DIR . '/alipay/lib/md5.php';
require WWW_DIR . '/alipay/lib/Notify.class.php';

$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if ($verify_result) {//验证成功

    //接受数据
    $user_id = $_SESSION['userId'];
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
        if (checkOrder($order_id, $total_fee, $seller_id, $extra_common_param, $error)) {
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
                $info['pay_status'] = 2;//修改为第一笔已经支付500
                $info['order_status'] = 7;//修改为等待发货
                $info['pay_time'] = time();//支付时间
                $result = M('xgj_furnish_order_info')->where(array('order_code' => $order_id))->data($info)->save();
            } elseif ($extra_common_param == '3') {
                //修改订单状态
                $info['pay_status'] = 3;//修改为第二笔已经支付
                $info['order_status'] = 7;//修改为等待发货
                $info['pay_time'] = time();//支付时间
                $result = M('xgj_furnish_order_info')->where(array('order_code' => $order_id))->data($info)->save();
                if ($result) {
                    $list = M('xgj_furnish_order_info i')->field('i.zp_money,i.user_id,u.integral,u.user_name')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->find();
                    $result = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['zp_money'] + $list['integral']);
                    if(!empty($list['zp_money']) && $result!==false){
                        $infos['order']  = $order_id;
                        $infos['user_id']   = $list['user_id'];
                        $infos['user_name'] = $list['user_name'];
                        $infos['integral']  = $list['zp_money'];
                        $infos['class']  = 1;
                        $infos['status'] = 2;
                        $infos['time']  = time();
                        if(M('xgj_user_integral')->data($infos)->add()==false){
                            logResult('返还积分流水添加失败');
                        }
                    }
                }
            } elseif ($extra_common_param == '4') {
                //修改订单状态
                $info['pay_status'] = 4;//修改为第三笔已经支付
                $info['order_status'] = 8;//修改为等待发货
                $info['pay_time'] = time();//支付时间
                $result = M('xgj_furnish_order_info')->where(array('order_code' => $order_id))->data($info)->save();
            } elseif ($extra_common_param == '5') {
                //修改订单状态
                $info['pay_status'] = 3;//修改为第一笔和第二笔已经支付
                $info['order_status'] = 7;//修改为等待发货
                $info['pay_time'] = time();//支付时间
                $result = M('xgj_furnish_order_info')->where(array('order_code' => $order_id))->data($info)->save();
                if ($result) {
                    $list = M('xgj_furnish_order_info i')->field('i.zp_money,i.user_id,u.integral,u.user_name')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->find();
                    $result = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['zp_money'] + $list['integral']);
                    if(!empty($list['zp_money']) && $result!==false){
                        $infos['order']  = $order_id;
                        $infos['user_id']   = $list['user_id'];
                        $infos['user_name'] = $list['user_name'];
                        $infos['integral']  = $list['zp_money'];
                        $infos['class']  = 1;
                        $infos['status'] = 2;
                        $infos['time']  = time();
                        if(M('xgj_user_integral')->data($infos)->add()==false){
                            logResult('返还积分流水添加失败');
                        }
                    }
                }
            } elseif ($extra_common_param == '6') {
                //修改订单状态
                $info['pay_status'] = 4;//修改为第二笔和第三笔已经支付
                $info['order_status'] = 8;//修改为等待发货
                $info['pay_time'] = time();//支付时间
                $result = M('xgj_furnish_order_info')->where(array('order_code' => $order_id))->data($info)->save();
                if ($result) {
                    $list = M('xgj_furnish_order_info i')->field('i.zp_money,i.user_id,u.integral,u.user_name')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->find();
                    $result = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['zp_money'] + $list['integral']);
                    if(!empty($list['zp_money']) && $result!==false){
                        $infos['order']  = $order_id;
                        $infos['user_id']   = $list['user_id'];
                        $infos['user_name'] = $list['user_name'];
                        $infos['integral']  = $list['zp_money'];
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
                    $list = M('xgj_furnish_order_info i')->field('i.zp_money,i.user_id,u.integral,u.user_name')->join('xgj_users u on i.user_id=u.user_id')->where(array('order_code' => $order_id))->find();
                    $result = M('xgj_users')->where('user_id=' . $list['user_id'])->setField('integral', $list['zp_money'] + $list['integral']);
                    if(!empty($list['zp_money']) && $result!==false){
                        $infos['order']  = $order_id;
                        $infos['user_id']   = $list['user_id'];
                        $infos['user_name'] = $list['user_name'];
                        $infos['integral']  = $list['zp_money'];
                        $infos['class']  = 1;
                        $infos['status'] = 2;
                        $infos['time']  = time();
                        if(M('xgj_user_integral')->data($infos)->add()==false){
                            logResult('返还积分流水添加失败');
                        }
                    }
                }
            } elseif ($extra_common_param == '8') {
                //修改订单状态【海外超市】
                $info['is_pay'] = 1;//修改为已经支付
                $info['order_status'] = 1;//修改为等待发货
                $info['pay_time'] = date('Y-m-d H:i:s');//支付时间
                $result = M('xgj_ov_order')->where(array('sn' => $order_id))->data($info)->save();
                if ($result) {
                    $id = M('xgj_ov_order')->where(array('sn' => $order_id))->getField('id');
                    $result = M('xgj_ov_split_order')->where(array('order_id' => $id))->data(array('order_status' => 1))->save();
                }
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
                header('location:../order.php?paySuccess');
                die;
                // die('支付成功');

            } else {
                //给用户提示错误页面
                logResult('更新订单状态失败:' . $error);
                header('location:../order.php?payError');
            }
        } else {
            if ($error === 'is_pay') {
                //已经支付
                //给用户提示支付成功的页面
                $_SESSION['pay']['table'] = 'xgj_eu_payment';
                $_SESSION['pay']['order_id'] = $out_trade_no;
                $_SESSION['pay']['system_id'] = $extra_common_param;
                header('location:../order.php?paySuccess');
                die;
            }
            //给用户提示错误页面
            logResult('支付失败:' . $error);
            header('location:../order.php?payError');
            die;
        }
    } else {
        echo "trade_status=" . $_GET['trade_status'];
    }

    echo "验证成功<br />";
} else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    echo "验证失败";
}

/*
验证订单状态,
 */
function checkOrder($order_id, $total_fee, $seller_id, $extra_common_param, &$error = '')
{
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
        $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount,cj_money')->where(array('order_code' => $order_id))->find();
        //系统信息
        $quote_info = M()->fetchAll("select * from xgj_furnish_order_detail d join xgj_furnish_quote q on d.quote_id=q.quote_id where d.order_id=$order_id");
        //付款金额 
        $total_price = '';
        foreach ($quote_info as $k => $v) {
            $total_price += ($v['quote_price'] * $v['ecprice']) / 100;
        }
        $goods_amount = $total_price + $order['cj_money'];
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
        $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount,cj_money')->where(array('order_code' => $order_id))->find();
        //系统信息
        $quote_info = M()->fetchAll("select * from xgj_furnish_order_detail d join xgj_furnish_quote q on d.quote_id=q.quote_id where d.order_id=$order_id");
        $total_price = '';
        foreach ($quote_info as $k => $v) {
            $total_price += $v['quote_price'] * $v['sale'] / 100 * (100 - $v['ecprice']) / 100;
        }
        //付款金额 
        $goods_amount = $total_price - 500;
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
        $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount,cj_money')->where(array('order_code' => $order_id))->find();
        //系统信息
        $quote_info = M()->fetchAll("select * from xgj_furnish_order_detail d join xgj_furnish_quote q on d.quote_id=q.quote_id where d.order_id=$order_id");
        $total_price = '';
        foreach ($quote_info as $k => $v) {
            $total_price += ($v['quote_price'] * $v['sale'] / 100 * $v['ecprice']) / 100;
        }
        //付款金额 
        $goods_amount = $total_price + 500 + $order['cj_money'];
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
        } elseif ($order['pay_status'] == 0 && $goods_amount == $total_fee && $seller_id == C('AP_PARTNER')) {
            return true;
        } else {
            $error = '系统错误';
            return false;
        }
    } elseif ($extra_common_param == '6') {
        //家居的订单查询代码....
        $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount,cj_money')->where(array('order_code' => $order_id))->find();
        //系统信息
        $quote_info = M()->fetchAll("select * from xgj_furnish_order_detail d join xgj_furnish_quote q on d.quote_id=q.quote_id where d.order_id=$order_id");
        $total_price = '';
        foreach ($quote_info as $k => $v) {
            $total_price += $v['quote_price'] * $v['sale'] / 100;
        }
        //付款金额 
        $goods_amount = $total_price - 500 + $order['cj_money'];
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
        $order = M('xgj_furnish_order_info')->field('order_id,pay_status,order_status,shipping_status,goods_amount,cj_money')->where(array('order_code' => $order_id))->find();
        //系统信息
        $quote_info = M()->fetchAll("select * from xgj_furnish_order_detail d join xgj_furnish_quote q on d.quote_id=q.quote_id where d.order_id=$order_id");
        $total_price = '';
        foreach ($quote_info as $k => $v) {
            $total_price += $v['quote_price'] * $v['sale'] / 100;
        }
        $goods_amount = $total_price + $order['cj_money'];
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
        } elseif ($order['pay_status'] == 1) {
            $error = 'is_pay';//已经支付,无需再次支付
            return false;
        } elseif ($order['pay_status'] == 0 && $goods_amount == $total_fee && $seller_id == C('AP_PARTNER')) {
            return true;
        } else {
            $error = '系统错误';
            return false;
        }
    } elseif ($extra_common_param == '8') {
        //【海外超市】查询代码....
        $order = M('xgj_ov_order')->field('is_pay,order_status,return_status,deal_price')->where(array('sn' => $order_id))->find();
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