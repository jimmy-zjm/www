<?php
namespace Admin\Model\Overseas;
use \Think\Model;
/**
 * 订单模型
 */

class OrderModel extends Model{
    protected $trueTableName = 'xgj_ov_order';
    protected $fields= array('id', 'user_id', 'sn', 'shr_name', 'shr_pro', 'shr_city', 'shr_area', 'shr_addr', 'shr_phone', 'shr_tel', 'shr_email', 'is_pay', 'pay_method', 'is_comment', 'is_invo', 'invo_title', 'express_sn', 'order_status', 'post_status', 'return_status', 'total_price', 'deal_price', 'total_goods_num', 'add_time', 'pay_time', 'post_time', 'class_id', 'buy_status', 'order_id', 'shipping_status', 'goods_title', 'evaluate_time', 'e_content');

    /**
     * 获取所有的订单信息
     * @return [type] [description]
     */
    public function getAll(){
        //拼凑条件
        $map = array();
        if(isset($_GET['send'])){
            $sn             = I('sn');
            $shr_name       = I('shr_name');
            $order_status   = I('order_status')==-1?'':I('order_status/d');
            $post_status    = I('post_status')==-1?'':I('post_status/d');
            $return_status  = I('return_status')==-1?'':I('return_status/d');
            $is_comment     = I('is_comment')==-1?'':I('is_comment/d');

            if(!empty($shr_name)){
                $map['shr_name'] = array('eq',$shr_name);
            }
            if(!empty($sn)){
                $map['sn'] = array('eq',$sn);
            }
            if(!empty($order_status)){
                $status=$order_status-1;
                $map['order_status'] = array('eq',$status);
            }
            if(!empty($post_status)){
                $status=$post_status-1;
                $map['post_status'] = array('eq',$status);
            }
            if(!empty($return_status)){
                $status=$return_status-1;
                $map['return_status'] = array('eq',$status);
            }
            if(!empty($is_comment)){
                $status=$is_comment-1;
                $map['is_comment'] = array('eq',$status);
            }
        }
        //$map['class_id'] = 2;
        //var_dump($map);exit;
        //分页
        $total        = $this->where($map)->count();
        $page         = getPage($total, C('ORDER_PAGE_SIZE'));
        $data['page'] = $page['page'];

        // 订单数据
        $data['order_list'] = $this->where($map)->order('order_status DESC')->limit($page['limit'])->select();

        return $data;
    }

    /*
    获取订单数据, 用于回填
     */
    public function getOne($id){
        //$map['o.class_id'] = 2;
        $map['o.id'] = array('eq',$id);
        //订单数据
        $data['info']=$this->alias('o')->join('xgj_users u on o.user_id=u.user_id')->where($map)->order('o.order_status desc')->find();
        $data['detail']=M('xgj_ov_order_goods')->alias('g')->join('xgj_ov_goods o on o.id=g.goods_id')->where(" g.order_id=$id")->select();
        foreach ($data['detail'] as $k => &$v) {
            $v['goods_image']=getImage($v['goods_image'],54,54);
        }
        // $data = $this->alias('o')->join('xgj_ov_order_goods g on o.id=g.order_id')->where($map)->order('order_status desc')->find();
        // $data['goods_image']=getImage($data['goods_image'],54,54);
        //返回数据
        return $data;
    }

}