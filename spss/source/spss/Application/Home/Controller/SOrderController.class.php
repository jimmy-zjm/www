<?php
namespace Home\Controller;
use Think\Controller;

class SOrderController extends BaseController {
	private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Home\Model\SOrderModel;
    }

    //提交订单页面
    public function process(){
        layout(false);
        $return = $this->m->placeOrder();
        $order_id=$return['order_id'];
        $class_id=$return['class_id'];
        if($return['deal_price']==0){
            $user_id=$_SESSION['user']['userId'];
            $res =M('xgj_s_order')->field("id,sn,add_time,total_price")->where(array('id'=>$order_id,'user_id'=>$user_id))->find();
            if($res){
                $pay = M('xgj_s_order')->where('id='.$order_id.' and user_id='.$user_id)->data(array('pay_method'=>9,'order_status'=>'1','is_pay'=>1,'pay_time'=>date('Y-m-d H:i:s')))->save();
                if($pay!==false ){
                    $_SESSION['int']['order_id']=$order_id;
                    $_SESSION['int']['sn']=$res['sn'];
                    $_SESSION['int']['system_id']=$class_id;
                    $_SESSION['int']['trade_no']='Int'.$res['sn'];
                    $_SESSION['int']['total_fee']=$res['total_price'];
                    //var_dump($_SESSION['int']);die;
                    $this->redirect("Order/paySuccess");
                }else{
                    $this->error('更新订单状态失败');
                }
            }else{
                $this->error('没有找到所查信息');
            }
        }else{
            if($order_id!==false){ 
                if($_POST['pay_method']==1){ 
                    $this->m->alipay($order_id,$class_id);
                }else if($_POST['pay_method']==2){
                    $this->m->chinaPayOrder($order_id,$class_id);
                }else if($_POST['pay_method']==3){
                    $this->m->noPayOrder($order_id,$class_id);
                }
            }else{
                $this->error(M('xgj_s_order')->getError());
            }
        }
    }

    /*
    从个人中心过来支付的链接
     */
    public function payOrder(){
        $order_id = I('order_id');
        $order_id = (int) $order_id;
        $user_id=$_SESSION['user']['userId'];
        $res = M("xgj_s_order")->field("id,pay_method,class_id")->where(array("id"=>$order_id,"user_id"=>$user_id))->find() ;
        if($res['id']==$order_id){
            if($res['pay_method']==1){
                //下单成功, 跳到支付宝
                $this->m->alipay($order_id,$res['class_id']);
            }else if($res['pay_method']==2){
                //下单成功, 跳到银联支付
                $this->m->chinaPayOrder($order_id,$res['class_id']);
            }else if($res['pay_method']==3){
                //下单成功, 跳到银联支付
                $this->m->noPayOrder($order_id,$res['class_id']);
            }
        }else{
            $this->error('参数错误');
        }
    }
}