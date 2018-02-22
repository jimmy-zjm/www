<?php
namespace Admin\Model\Index;
use \Think\Model;
/*
优惠券模型
 */
class CouponModel extends Model{
    protected $trueTableName = 'xgj_coupon';

    protected $_validate=array(
        array('coupon_number','require','优惠券号不能为空',1,''),
        array('coupon_password','require','优惠券密码不能为空',1,''),
        array('discount_amount','require','优惠券额度不能为空',1,''),
		array('start_time','require','请输入优惠券开始使用时间',1,''),
		array('end_time','require','请输入优惠券截止使用时间',1,''),
    );



	//插入优惠券之前
    protected function _before_insert(&$data, $options){
        /*******处理基本信息*******/
        $data['add_time']   = time();//添加时间
		$data['start_time'] = strtotime($_POST['start_time']);
		$data['end_time']   = strtotime($_POST['end_time']);
        return true;
    }

	//批量增加 优惠券
	 public function checknumber($start,$end){
	 if($start<$end){
		$data  = M('xgj_coupon')->order('id desc')->limit(1)->select();
		
		if($start<=$data[0]['coupon_number'])
			return false;
		else
			return true;
	 }else 
		 return false;
      
    }

	//生成优惠券数据
	public function generate($start,$end,$amount,$start_time,$end_time){
		$start_time=strtotime($start_time);
		$end_time=strtotime($end_time);
		for($i=$start;$i<=$end;$i++){
			$pass=substr(md5($i.'Xgm1sfYDcd234'),1,8);
			$dataList[] = array('coupon_number'=>$i,'coupon_password'=>$pass,'discount_amount'=>$amount,'start_time'=>$start_time,'end_time'=>$end_time,'add_time'=>time());
		}
		return $dataList;
		//echo "<pre>";var_dump($dataList);die();
	}




}