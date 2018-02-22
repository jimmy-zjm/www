<?php
/**
 * 购物车类
 * @date 2016-3-9
 * @author grass <14712905@qq.com>
 */

class MoorModel extends Model{
    private $tableName = 'xgj_users';//该模型的表名

    public function __construct(){
        parent::__construct($this->tableName);
    }
	
	public function userinfo($mobile) {
		$result = M('xgj_users')->where(array('mobile_phone'=>$mobile))->find();
		if(!empty($result)){
			if($result['sex']==0)
				$result['sex']="男";
			elseif($result['sex']==1)
				$result['sex']="女";
			elseif($result['sex']==2)
				$result['sex']="保密";
			else 
				$result['sex']="未填";
		}
		$data = M('xgj_address')->where(array('user_id'=>$result['user_id'],'default'=>'1'))->find();
		//var_dump($data);die();
		if(!empty($data)){
			$result['pro']=$data['a_pro'];
			$result['city']=$data['a_city'];
			$result['area']=$data['a_area'];
			$result['addr']=$data['a_addr'];
		}
		return $result;
	}
	public function userinfo1($userid) {
		$result = M('xgj_users')->find($userid);
		if(!empty($result)){
			if($result['sex']==0)
				$result['sex']="男";
			elseif($result['sex']==1)
				$result['sex']="女";
			elseif($result['sex']==2)
				$result['sex']="保密";
			else 
				$result['sex']="未填";
		}
		$data = M('xgj_address')->where(array('user_id'=>$result['user_id'],'default'=>'1'))->find();
		//var_dump($data);die();
		if(!empty($data)){
			$result['pro']=$data['a_pro'];
			$result['city']=$data['a_city'];
			$result['area']=$data['a_area'];
			$result['addr']=$data['a_addr'];
		}
		return $result;
	}
   
}