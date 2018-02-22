<?php
namespace Home\Model;
use Think\Model;

class CustomerModel extends Model {
	protected $autoCheckFields =false;


	//获取用户id中所有系统名称
    public function getQuoteName($id){
    	$data=M('xgj_furnish_order_info o')->field("d.quote_name,d.order_id,q.alias,d.quote_id")->join("xgj_furnish_order_detail d on o.order_id=d.order_id")->join("xgj_furnish_quote q on d.quote_id=q.quote_id")->where(array("o.user_id"=>$id))->order("d.order_id desc")->select();
    	return $data;
    }

    //获取订单id查找联系人地址
    public function getHouseAddr($id){
    	$data=M("xgj_furnish_order_info")->where(array("order_id"=>$id))->find();
    	return $data;
    }

    public function getUpkeepAll($limit=null){
        $where['is_use'] = 1;
        if ($limit) {
            $data=M("xgj_s_upkeep")->where($where)->limit($limit)->select();
        }else{
            $data=M("xgj_s_upkeep")->where($where)->select();
        }
        return $data;
    }

    //获取所有耗材
    public function getConsumable($map=''){
        $where['is_put']=1;
        if(!empty($map)){
            $where['quote_name']=$map;
        }
        $data=M('xgj_s_consumable')->where($where)->select();
        return $data;
    }
}