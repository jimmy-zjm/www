<?php
namespace Home\Controller;
use Think\Controller;

class OrderController extends BaseController {
	private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Home\Model\OrderModel;
    }
	//结算首页
    public function index(){

        
        if(!empty($_GET['goods_id']) && !empty($_GET['num'])){
            $goods_id=$_GET['goods_id'];
            $num=$_GET['num'];
            $arr[$goods_id]=$num;
            $_SESSION['user']['cart']=$arr;
        }
        $area = getPCD();
        $cart_id=$_SESSION['user']['cart'];
        $user_id=$_SESSION['user']['userId'];
        $info=$this->m->getCartByCartId($cart_id);
        $this->assign('info',$info);
        $userModel= new \Home\Model\UserModel;
        $addressInfo=$userModel->getAddressByUserId($user_id);
        $this->assign('addressInfo',$addressInfo);
        $this->assign('area',$area);
        //获取用户的优惠券
        $coupon=$userModel->getCouponByUserId($user_id);
        $this->assign('coupon',$coupon);
        $integral=$userModel->getuserintl(array('user_id'=>$user_id));
        $this->assign('integral',$integral);
    	$this->display();
    }

     //收货地址
    public function address(){
        //查询省级城市
        $area = getPCD();
        $a_id = I('id');
        $userModel= new \Home\Model\UserModel;
        $info=$userModel->getAddressById($a_id);
        foreach ($area as $k=>$v){
            if($v['name']==$info['a_pro']){
                $pro_id=$v['id'];
            }
        }
        $data['area']=$area;
        $data['info']=$info;
        $data['pro_id']=$pro_id;
        echo json_encode($data);
    }

    //编辑地址操作
    public function doUpdateAddress(){
        layout(false);
        $a_id = I('id');
        $user_id                =$_SESSION['user']['userId'];
        $data['a_name']         =I('a_name');
        $data['user_id']        =$user_id;
        $data['a_mobile_phone'] =I('mobile');
        $data['a_phone']        =I('quhao').'-'.I('number').'-'.I('fenji');
        $data['a_zipcode']      =empty(I('zipcode'))?'000000':I('zipcode');
        $data['a_pro']          =getPCDName(I('province'));
        $data['a_city']         =getPCDName(I('city'));
        $data['a_area']         =getPCDName(I('district'));
        $data['a_addr']         =I('addr');
        $data['default']        =empty(I('default'))?0:1;
        $userModel= new \Home\Model\UserModel;
        $re=$userModel->updateAddressInfo($data,array('a_id'=>$a_id));
        if($re!==false){
            $this->success('修改地址成功', 'index');
        }else{
            $this->error('修改地址失败');
        }
    }

    //提交订单页面
    public function process(){
        layout(false);
        $order_id =$this->m->placeOrder();
        if($order_id!==false){ 
            if($_POST['pay_method']==1){ 
                $this->m->alipay($order_id,'2');
            }else if($_POST['pay_method']==2){
                $this->m->chinaPayOrder($order_id,'2');
            }else if($_POST['pay_method']==3){
                $this->m->noPayOrder($order_id,'2');
            }
        }else{
            $this->error(M('xgj_furnish_order_info')->getError());
        }
    }

    //支付成功页面
    public function paySuccess(){
        $order=$_SESSION['int'];
        unset ($_SESSION['int']);
        if(empty($order)){
            $table=$_SESSION['pay']['table'];
            $order_id  = $_SESSION['pay']['order_id'];
            $system_id = (int) $_SESSION['pay']['system_id'];
            if(empty($order_id)) $this->error('非法请求','index.php');
            $payinfo= M("$table")->where(array("order_id"=>$order_id))->find();
            $order_code=substr($payinfo['order_id'],0,18);
            if ($system_id=='1'){
                $orderinfo=M("xgj_eu_order")->field("id as order_id")->where(array("sn"=>$order_code))->find();
            }else if ($system_id=='8' || $system_id=='9'){
                $orderinfo=M("xgj_s_order")->field("id as order_id")->where(array("sn"=>$order_code))->find();
            }else{
                $orderinfo=M("xgj_furnish_order_info")->where(array("order_code"=>$order_code))->find();
            } 

            $order['order_id']=$orderinfo['order_id'];
            $order['system_id']=$system_id;
            $order['sn']=$order_code;
            unset ($_SESSION['order']);
            if(empty($order)){
                trigger_error('支付成功: 通过订单id没有查询到数据'.$sql);
                die;
            }
        }
        if($order['system_id']=='1'){
            $orderGoodsInfo=M("xgj_eu_order_goods")->field("goods_title as quote_name")->where(array("order_id"=>$order['order_id']))->select();
        }else if($order['system_id']=='8' || $order['system_id']=='9') {
            $orderGoodsInfo=M("xgj_s_order_goods")->field("goods_title as quote_name")->where(array("order_id"=>$order['order_id'],'class_id'=>$order['system_id']))->select();
        }else{ 
            $orderGoodsInfo= M("xgj_furnish_order_detail")->where(array("order_id"=>$order['order_id']))->select();
        }
        $this->assign('order', $order);
        $this->assign('goods', $orderGoodsInfo);
        $this->display();
    }

    //支付失败页面
    public function payError(){
        $this->display();
    }

    //退款返回页面
    public function refundFile(){
        $id=$_SESSION['refund']['id'];
        $msg=$_SESSION['refund']['msg'];
        $class_id=$_SESSION['refund']['class_id'];
        $this->assign('id', $id);
        $this->assign('class_id', $class_id);
        $this->assign('msg', $msg);
        $this->display();
    }

    /*
    已下订单，再次支付
    */
    public function orderPay(){
        $order_id=intval($_POST['order_id']);
        $user_id=$_SESSION['user']['userId'];
        $res=M('xgj_furnish_order_info')->field('order_id,pay_method')->where(array('order_id'=>$order_id,'user_id'=>$user_id))->find();
        if($res['order_id']==$order_id){
            //$class_id=trim($_POST['option']);
			$class_id=2;
            if($res['pay_method']==1){
                $this->m->alipay($order_id,$class_id);
            }else if($res['pay_method']==2){
                $this->m->chinaPayOrder($order_id,$class_id);
            }else if($res['pay_method']==3){
                $this->m->noPayOrder($order_id,$class_id);
            }
        }else{
            $this->error('您所支付的订单有误',U('User/homeOrderShow',array('id'=>$order_id)));
        }
    }

    /*
    已下订单，一键支付
    */
    public function orderPayAll(){
        $order_id=intval($_POST['order_id']);
        $user_id=$_SESSION['user']['userId'];
        $res=M('xgj_furnish_order_info')->field('order_id,pay_method')->where(array('order_id'=>$order_id,'user_id'=>$user_id))->find();
        if($res['order_id']==$order_id){
            $class_id=trim($_POST['option']);
            //var_dump($_POST);exit;
            if($res['pay_method']==1){
                $this->m->alipay($order_id,$class_id);
            }else if($res['pay_method']==2){
                $this->m->chinaPayOrder($order_id,$class_id);
            }else if($res['pay_method']==3){
                $this->m->noPayOrder($order_id,$class_id);
            }
        }else{
            $this->error('您所支付的订单有误','User/homeOrderShow');
        }
    }
    /*
    取消订单（退款）
     */
    public function cancel(){
        layout(false);
        $order_id=intval($_POST['order_id']);
        $class_id=intval($_POST['class_id']);
        if($class_id==1){
            $res = M('xgj_eu_order')->field("id as order_id,pay_status,sn,pay_method")->where("id=$order_id and user_id ={$_SESSION['user']['userId']}")->find();
            $str='01';
        }else if($class_id==2){
            $res = M('xgj_furnish_order_info')->field("order_id,pay_status,order_code as sn,pay_method")->where("order_id=$order_id and user_id ={$_SESSION['user']['userId']}")->find();
            $str='02';
        }
        if($res['pay_status']!=7){
            if($res['pay_status']==0){
                if($class_id==1){
                    $re=M('xgj_eu_order')->delete($res['order_id']);  
                    $rs=M('xgj_eu_order_goods')->delete($res['order_id']);    
               }else{
                    $re=M('xgj_furnish_order_info')->delete($res['order_id']);  
                    $rs=M('xgj_furnish_order_detail')->delete($res['order_id']);
               }
                $this->success('取消订单成功',U('User/homeOrder'));
            }else{
                $sn=$res['sn'].$str;
                if($res['pay_method']==1){
                    $this->m->alipayRefund($sn,$order_id);
                }else if($res['pay_method']==2){
                    $this->m->chinaPayRefund($sn,$order_id);
                }else if($res['pay_method']==3){
                    $this->m->noPayRefund($sn,$order_id);
                }
            }
        }else{
            $this->error('您所取消的订单有误',U('User/homeOrderShow',array('id'=>$order_id)));
        }
    }

}