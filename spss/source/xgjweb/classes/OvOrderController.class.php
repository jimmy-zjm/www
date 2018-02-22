<?php
/**
 * 订单控制器
 * 适用于欧洲团购&德国母婴
 * @date 2016-3-11
 * @author grass <14712905@qq.com>
 */
class OvOrderController extends Controller{
    public function __construct(){
        parent::__construct();
    }

    /*
    显示 提交订单 页面
     */
    public function show(){
        $this->checkLogin('cart.php?cartList#tabs-2');
        if(isset($_GET['goods_id']) && !empty($_GET['goods_id'])){
            $goods_id=$_GET['goods_id'];
            $num=$_GET['num'];
            if(empty($goods_id)) $this->error('你选择的商品有误!!','baby.php?detail&id=$goods_id');
            $data    = D('OvOrder')->getCartGoods($goods_id,$num);
            $_SESSION['ovorder']['total_price']  = $data['total_price'];
            $_SESSION['ovorder']['goods_number'] = $data['goods_number'];
            $_SESSION['ovorder']['cart_list']    = $data['cart_list'];
            $_SESSION['ovorder']['youhuizong']   = $data['cart_list'][0]['youhuizong'];
        }else{
            //接收购物车提交过来的购物车id, 并写入session
            $cart = I('cart_id');
            $_SESSION['ovcart_id'] = $cart;
            $cart_id = empty($_SESSION['ovcart_id'])?'':$_SESSION['ovcart_id'];
            if(empty($cart_id)) $this->error('你没有选择任何商品!!','cart.php?cartList');
            $data    = D('OvOrder')->getCart($cart_id);
            if(empty($data['cart_list'])){
                $this->error('没有需要购买的商品!!','cart.php?cartList');
            }
            //var_dump($data['cart_list']);die;
            $_SESSION['ovorder']['total_price']  = $data['total_price'];
            $_SESSION['ovorder']['goods_number'] = $data['goods_number'];
            $_SESSION['ovorder']['cart_list']    = $data['cart_list'];
            $_SESSION['ovorder']['youhuizong']   = $data['cart_list'][0]['youhuizong'];
        }
        $user_id = session('userId');
        $addr = M()->fetch('SELECT * FROM xgj_address WHERE user_id='.$user_id);//默认地址
        $userInfo = M()->fetch('SELECT real_name,identity_card,coupon,integral FROM xgj_users WHERE user_id='.$user_id);//会员信息
        $price = M()->fetch("SELECT coupon FROM xgj_users WHERE user_id ={$_SESSION['userId']}");
        $map['is_show']    = 1;
        $map['class_id']   = 2;
        $map['pid']        = 0;
        $cate_list = M('xgj_ov_category')->where($map)->order('`order` ASC')->select();
        foreach($cate_list as $k=>$v){
            $cate_list[$k]['list']=M('xgj_ov_category')->where("pid={$v['id']}")->order('`order` ASC')->select();
        }
        $data1=D('Eugroup')->category(1);
        $this->assign('eu_tree',$data1);
        $this->assign('ov_tree',$cate_list);
        $this->assign('coupon', $price['coupon']);

        $this->assign('addr', $addr);
        $this->assign('userInfo', $userInfo);
        $this->assign('cart_list', $data['cart_list']);
        $this->assign('total_price', $data['total_price']);
        $this->assign('goods_number', $data['goods_number']);
        $this->display('ovorder/order_info.tpl.html');
    }


    /*
    下订单, 处理订单
     */
    public function process(){
        $this->checkLogin();
		if(empty($_SESSION['ovorder']))
				 $this->error('页面刷新失败，请重新操作','index.php');
        $return = D('OvOrder')->placeOrder();
        $order_id=$return['order_id'];
        if($return['deal_price']==0){
            $res = M()->fetch("SELECT id,sn,add_time,total_price FROM xgj_ov_order WHERE id=$order_id and user_id ={$_SESSION['userId']}");
            $re = M()->fetchAll("SELECT id FROM xgj_ov_split_order WHERE order_id=$order_id and user_id ={$_SESSION['userId']}");
            if($res && $re){
                $pay = M('xgj_ov_order')->where('id='.$order_id.' and user_id='.$_SESSION['userId'])->data(array('pay_method'=>9,'order_status'=>'1','is_pay'=>1,'pay_time'=>date('Y-m-d H:i:s')))->save();
                $split = M('xgj_ov_split_order')->where('order_id='.$order_id.' and user_id='.$_SESSION['userId'])->data(array('order_status'=>'1'))->save();
                if($pay!==false && $split!==false){
                    $order['order_id']=$order_id;
                    $order['sn']=$res['sn'];
                    $order['system_id']='8';
                    $order['trade_no']='Ovs'.$res['sn'];
                    $order['total_fee']=$res['total_price'];
                    $this->paySuccess($order);
					
                }else{
                    $this->error('更新订单状态失败','index.php');
                }
            }else{
                $this->error('没有找到所查信息','index.php');
            }
        }else{

            if($order_id!==false){
                if($_POST['pay_method']==1){
                    //下单成功, 跳到支付宝
                    D('OvOrder')->payOrder($order_id);
                }else if($_POST['pay_method']==2){
                    //下单成功, 跳到银联支付
                    D('OvOrder')->chinaPayOrder($order_id);
                }else if($_POST['pay_method']==3){
                    //下单成功, 跳到银联支付
                    D('OvOrder')->noPayOrder($order_id);
                }else {
						 $this->error('页面刷新失败，请勿重复提交','index.php');
				}
            }else{
                $this->error(D('OvOrder')->getError(),'index.php');
            }
        }
        
    }

    /*
    从个人中心过来支付的链接
     */
    public function payOrder(){
        $this->checkLogin();
        $order_id = I('order_id');
        $order_id = (int) $order_id;
        $res = M()->fetch("SELECT id,pay_method FROM xgj_ov_order WHERE id=$order_id and user_id ={$_SESSION['userId']}");
        
        if($res['id']==$order_id){
            if($res['pay_method']==1){
                //下单成功, 跳到支付宝
                D('OvOrder')->payOrder($order_id);
            }else if($res['pay_method']==2){
                //下单成功, 跳到银联支付
                D('OvOrder')->chinaPayOrder($order_id);
            }else if($res['pay_method']==3){
                //下单成功, 跳到银联支付
                D('OvOrder')->noPayOrder($order_id);
            }
        }else{
            $this->error('参数错误','index.php');
        }
    }

    /*
    支付成功
     */
    public function paySuccess($order=''){
        $this->checkLogin();
       if(empty($order)){
            $table=$_SESSION['pay']['table'];
            $order_id  = $_SESSION['pay']['order_id'];
            $system_id = (int) $_SESSION['pay']['system_id'];
            if(empty($order_id)) $this->error('非法请求','index.php');
            $sql = "SELECT p.* FROM $table p WHERE p.order_id='{$order_id}'";
            $order= M()->fetch($sql);
            $order['order_id']=$order_id;
			$order['system_id']=$system_id;
            $order['sn']=substr($order['order_id'],0,18);
			unset ($_SESSION['ovorder']);
            if(empty($order)){
                trigger_error('支付成功: 通过订单id没有查询到数据'.$sql);
                die;
            }
        }

        if ($order['system_id']=='1') $tables = 'xgj_eu_order_goods';
        else if ($order['system_id']=='8') $tables = 'xgj_ov_order_goods';
        else $tables = 'xgj_furnish_order_detail';

        $sql = "SELECT * FROM $tables WHERE order_id = '{$order['order_id']}'";
        $orderGoodsInfo= M()->fetchAll($sql);

        $this->assign('order', $order);
        $this->assign('goods', $orderGoodsInfo);
        $this->display('order/success.tpl.html');
    }


    /*
    支付失败
     */
    public function payError(){
        $this->checkLogin();
        $this->display('ovorder/error.tpl.html');
    }

}