<?php
if ($_POST) {
    if (count($_POST) > 0) {
        require '../libs/common/initialize.php';
        include 'util/common.php';
        include 'util/SecssUtil.class.php';
        
        $secssUtil = new SecssUtil();
        $securityPropFile = $_SERVER['DOCUMENT_ROOT'] . "/chinapay/config/security.properties";
        $secssUtil->init($securityPropFile);
        $order_id=date('Y',time()).substr($_POST['MerOrderNo'],0,14);
        
		$text = array();
        foreach($_POST as $key=>$value){
            $text[$key] = urldecode($value);
        }
		
        if ($secssUtil->verify($text)) {
			//$_SESSION["VERIFY_KEY"] = "success";
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
                $info['pay_status']   = 2;//修改为第一笔已经支付500
                $info['order_status'] = 7;//修改为等待发货
                $info['pay_time']     = time();//支付时间
                $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
			}else if($text['MerResv']==3){
				//修改订单状态
                $info['pay_status']   = 3;//修改为第二笔已经支付
                $info['order_status'] = 7;//修改为等待发货
                $info['pay_time']     = time();//支付时间
                $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
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
			}else if($text['MerResv']==4){
				//修改订单状态
                $info['pay_status']   = 4;//修改为第三笔已经支付
                $info['order_status'] = 8;//修改为等待发货
                $info['pay_time']     = time();//支付时间
                $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
			}else if($text['MerResv']==5){
				//修改订单状态
                $info['pay_status']   = 3;//修改为第一笔和第二笔已经支付
                $info['order_status'] = 7;//修改为等待发货
                $info['pay_time']     = time();//支付时间
                $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
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
			}else if($text['MerResv']==6){
				//修改订单状态
                $info['pay_status']   = 4;//修改为第二笔和第三笔已经支付
                $info['order_status'] = 8;//修改为等待发货
                $info['pay_time']     = time();//支付时间
                $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
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
			}else if($text['MerResv']==7){
				//修改订单状态
                $info['pay_status']   = 1;//修改为三笔一起支付
                $info['order_status'] = 7;//修改为等待发货
                $info['pay_time']     = time();//支付时间
                $result               = M('xgj_furnish_order_info')->where(array('order_code'=>$order_id))->data($info)->save();
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
			}else if($text['MerResv']==8){
                 //修改订单状态
                $info['is_pay']       = 1;//修改为已经支付
                $info['order_status'] = 1;//修改为等待发货
                $info['pay_time']     = date('Y-m-d H:i:s');//支付时间
                $result               = M('xgj_ov_order')->where(array('sn'=>$order_id))->data($info)->save();
                if($result){
                    $id=M('xgj_ov_order')->where(array('sn'=>$order_id))->getField('id');
                    $result              = M('xgj_ov_split_order')->where(array('order_id'=>$id))->data(array('order_status'=>1))->save();
                }
            }
			if($result!==flase){
                //支付成功, 插入支付详情表
                $data_=array(
					'order_id'=>date('Y',time()).$text['MerOrderNo'],
					'version'=>$text['Version'],
					'tran_date'=>$text['TranDate'],
					'tran_time'=>$text['TranTime'],
					'total_fee'=>$text['OrderAmt']/100,
					'add_time'=>time(),
					'tran_type'=>$text['TranType'],
					'busi_type'=>$text['BusiType'],
					'order_status'=>$text['OrderStatus'],
					'bank_inst_no'=>$text['BankInstNo'],
					'complete_date'=>$text['CompleteDate'],
					'complete_time'=>$text['CompleteTime'],
					'curry_no'=>$text['CurryNo'],
					'trade_no'=>$text['AcqSeqId'],
					'mer_id'=>$text['MerId'],
					'signature'=>$text['Signature'],
					'acq_date'=>$text['AcqDate'],
					'class_id'=>$text['MerResv'],
				);
                if(!M('xgj_chinapay')->data($data_)->add()){
                    echo '支付详情信息插入失败';
                }
                //给用户提示支付成功的页面
				$_SESSION['pay']['table']='xgj_chinapay';
                $_SESSION['pay']['order_id'] = date('Y',time()).$text['MerOrderNo'];
                $_SESSION['pay']['system_id'] = $text['MerResv'];
                header('location:../order.php?paySuccess');
                die;
                // die('支付成功');
            }else{
                //给用户提示错误页面
                echo '更新订单状态失败';
                header("refresh:2;url='../order.php?payError'");die;
            }
            
        } else {
			//给用户提示错误页面
			echo '支付失败';
            header("refresh:3;url='../order.php?payError'");
            die;
            //$_SESSION["VERIFY_KEY"] = "fail";
        }
    }
}
