<?php
/**
 * 订单控制器
 * 适用于健康舒适家居
 */
class FurnishOrderController extends Controller{
    public function __construct(){
        parent::__construct();
        $this->checkLogin('cart.php?cartList');//订单控制用户必须登陆
    }

    /*
    显示 提交订单 页面
     */
    public function show(){
        //接收购物车提交过来的购物车id, 并写入session
        $cart_id                           = $_POST['cart_id'];
        // var_dump($cart_id);exit;
        $str                               ='';
        foreach ($cart_id as $key          => $value) {
            $str                           .=$value.',';
        }
        $where                             =rtrim($str,',');
        $user_id                           = $_SESSION['userId'];
        $addr                              = M()->fetch('SELECT * FROM xgj_address WHERE user_id='.$user_id);//默认地址
        $data                              = M()->fetchAll("SELECT *,q.coupon cou FROM xgj_furnish_cart c join xgj_furnish_quote q on c.cat_id=q.quote_id WHERE c.cart_id in ($where)");
        foreach($data as $k=>$v){
			$data[$k]['image']=getImage(M('xgj_furnish_quote')->where(array('quote_id'=>$v['cat_id']))->getField('img'));
        }	
        $price                             =0;
        $coupon                            =0;
        $gift                              =0;
        foreach ($data as $key             => $value) {
            $price                         +=ceil(($value['price']-$value['cost'])*$value['sale']/100+$value['cost']);//订单总价
            $coupon                        +=ceil((($value['price']-$value['cost'])*$value['sale']/100)*$value['cou']/100);//打折之后优惠券的总金额
            $gift                          +=ceil((($value['price']-$value['cost'])*$value['sale']/100)*$value['gift']/100);//打折之后赠品可使用的总金额
        }
        $_SESSION['furnish_order']['cart_list']         =$data;

        if(!empty($_POST['zp_id'])){
            //接收购物车提交过来的购物车id, 并写入session
            $cart                                     = I('zp_id');
            $_SESSION['zp_id']                        = $cart;
            $zp_id                                    = empty($_SESSION['zp_id'])?'':$_SESSION['zp_id'];
            $data_                                    = D('FurnishOrder')->getCart($zp_id);
            $_SESSION['zp_order']                     =$data_;
            $_SESSION['zp_order']['total_price']      = $data_['total_price'];//赠品总价
            $_SESSION['zp_order']['goods_number']     = $data_['goods_number'];
            $_SESSION['zp_order']['cart_list']        = $data_['cart_list'];
            
            $total_price                              =$price+ceil($data_['total_price']-$gift-$gift*0.05);
            $_SESSION['furnish_order']['total_price'] =$total_price;
            $_SESSION['furnish_order']['zp_money']    =$data_['total_price'];//赠品价格
            $_SESSION['furnish_order']['cj_money']    =ceil($data_['total_price']-$gift-$gift*0.05);
            $_SESSION['furnish_order']['yhq']         =$coupon;//优惠券总金额
            $this->assign('zp_list', $data_['cart_list']);
            $this->assign('goods_number', $data_['goods_number']);
        }else{
            $_SESSION['zp_order']                     = '';
            $_SESSION['zp_order']['total_price']      = 0;
            $_SESSION['zp_order']['goods_number']     = 0;
            $_SESSION['zp_order']['cart_list']        = '';
            
            $total_price                              =$price;
            $_SESSION['furnish_order']['total_price'] =$total_price;
            $_SESSION['furnish_order']['yhq']         =$coupon;//优惠券总金额
            $_SESSION['furnish_order']['zp_money']    =0;//赠品价格
            $_SESSION['furnish_order']['cj_money']    =0;
            $this->assign('zp_list','');
            $this->assign('goods_number', '');
        }

        $data1=D('Eugroup')->category(1);
        $this->assign('eu_tree',$data1);

        $coupon = $this->coupon(1);
        $this->assign('coupon', $coupon);
        
        $this->assign('addr', $addr);
        $this->assign('cart_list', $data);
        $this->assign('house_id', $data[0]['house_id']);
        $this->assign('total_price', $total_price);
        $this->display('furnish/furnish_order_info.tpl.html');
    }


    /*
    下订单, 处理订单
     */
    public function process(){
        $furnish_order_ob=new FurnishOrderModel();
        $order_id =$furnish_order_ob ->placeOrder();
        if($order_id!==false){  
            if($_POST['pay_method']==1){
                //下单成功, 跳到支付宝
                $furnish_order_ob->payOrder($order_id,'2');
            }else if($_POST['pay_method']==2){
                $furnish_order_ob->chinaPayOrder($order_id,'2');
            }else if($_POST['pay_method']==3){
                $furnish_order_ob->noPayOrder($order_id,'2');
            }
        }else{
            $this->error(M('xgj_furnish_order_info')->getError(),'index.php');
        }
    }
    /*
    已下订单，再次支付
    */
    public function orderPay(){
        $order_id=intval($_POST['order_id']);
        //var_dump($order_id);exit;
        $res = M()->fetch("SELECT * FROM xgj_furnish_order_info WHERE order_id=$order_id and user_id ={$_SESSION['userId']}");
        //var_dump($res,$order_id);exit;
        if($res['order_id']==$order_id){
            $furnish_order_ob=new FurnishOrderModel();
            $class_id=trim($_POST['option']);
            //var_dump($_POST);exit;
            if($res['pay_method']==1){
                $furnish_order_ob->payOrder($order_id,$class_id);
            }else if($res['pay_method']==2){
                $furnish_order_ob->chinaPayOrder($order_id,$class_id);
            }else if($res['pay_method']==3){
                $furnish_order_ob->noPayOrder($order_id,$class_id);
            }
        }else{
            $this->error('您所支付的订单有误','user.php?homeOrderShow');
        }
    }

    /*
    已下订单，一键支付
    */
    public function orderPayAll(){
        $order_id=intval($_POST['order_id']);
        $res = M()->fetch("SELECT * FROM xgj_furnish_order_info WHERE order_id=$order_id and user_id ={$_SESSION['userId']}");
        if($res['order_id']==$order_id){
            $furnish_order_ob=new FurnishOrderModel();
            $class_id=trim($_POST['option']);
            //var_dump($_POST);exit;
            if($res['pay_method']==1){
                $furnish_order_ob->payOrder($order_id,$class_id);
            }else if($res['pay_method']==2){
                $furnish_order_ob->chinaPayOrder($order_id,$class_id);
            }else if($res['pay_method']==3){
                $furnish_order_ob->noPayOrder($order_id,$class_id);
            }
        }else{
            $this->error('您所支付的订单有误','user.php?homeOrderShow');
        }
    }

    /**
     * 取消订单
     */
    public function cancel(){
        $order_id=intval($_POST['order_id']);
        $res = M()->fetch("SELECT * FROM xgj_furnish_order_info WHERE order_id=$order_id and user_id ={$_SESSION['userId']}");
        if($res){
            $re=M('xgj_furnish_order_info')->delete($res['order_id']);  
            $rs=M('xgj_furnish_order_detail')->delete($res['order_id']); 
            $this->success('取消订单成功','user.php?homeOrderShow');
        }else{
            $this->error('您所取消的订单有误','user.php?homeOrderShow');
        }
    }

    /**
     * 查询优惠券
     */
    public function coupon($o=null){

        $price = M()->fetch("SELECT coupon FROM xgj_users WHERE user_id ={$_SESSION['userId']}");
        if ($o==null) {
            echo $price['coupon'];
        }else{
            return $price['coupon'];
        }
        
    }
}