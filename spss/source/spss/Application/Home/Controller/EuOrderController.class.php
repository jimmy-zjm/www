<?php
namespace Home\Controller;
use Think\Controller;

class EuOrderController extends BaseController {
	private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Home\Model\EuOrderModel;
    }

    //提交订单页面
    public function process(){
        layout(false);
        $return = $this->m->placeOrder();
        $order_id=$return['order_id'];
        if($return['deal_price']==0){
            $user_id=$_SESSION['user']['userId'];
            $res =M('xgj_eu_order')->field("id,sn,add_time,total_price")->where(array('id'=>$order_id,'user_id'=>$user_id))->find();
            $re = M("xgj_eu_split_order")->field('id')->where(array('id'=>$order_id,'user_id'=>$user_id))->select();
            if($res && $re){
                $pay = M('xgj_eu_order')->where('id='.$order_id.' and user_id='.$user_id)->data(array('pay_method'=>9,'order_status'=>'1','is_pay'=>1,'pay_time'=>date('Y-m-d H:i:s')))->save();
                $split = M('xgj_eu_split_order')->where('order_id='.$order_id.' and user_id='.$user_id)->data(array('order_status'=>'1'))->save();
                if($pay!==false && $split!==false){
                    $_SESSION['int']['order_id']=$order_id;
                    $_SESSION['int']['sn']=$res['sn'];
                    $_SESSION['int']['system_id']='1';
                    $_SESSION['int']['trade_no']='Int'.$res['sn'];
                    $_SESSION['int']['total_fee']=$res['total_price'];
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
                    $this->m->alipay($order_id,'1');
                }else if($_POST['pay_method']==2){
                    $this->m->chinaPayOrder($order_id,'1');
                }else if($_POST['pay_method']==3){
                    $this->m->noPayOrder($order_id,'1');
                }
            }else{
                $this->error(M('xgj_eu_order')->getError());
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
        $res = M("xgj_eu_order")->field("id,pay_method")->where(array("id"=>$order_id,"user_id"=>$user_id))->find() ;
        
        if($res['id']==$order_id){
            if($res['pay_method']==1){
                //下单成功, 跳到支付宝
                $this->m->alipay($order_id,'1');
            }else if($res['pay_method']==2){
                //下单成功, 跳到银联支付
                $this->m->chinaPayOrder($order_id,'1');
            }else if($res['pay_method']==3){
                //下单成功, 跳到银联支付
                $this->m->noPayOrder($order_id,'1');
            }
        }else{
            $this->error('参数错误');
        }
    }
}