<?php
namespace Home\Model;
use Think\Model;

class FurnishModel extends Model {
	Protected $autoCheckFields = false;

	public function getQuoteAll($where=null,$field=null){
		$re = $this->getAll('xgj_furnish_quote',$where,$field);
		return $re;
	}

	public function getQuoteRow($where=null,$field=null){
		$re = $this->getRow('xgj_furnish_quote',$where,$field);
		return $re;
	}

	public function getChildRow($where=null,$field=null){
		$re = $this->getRow('xgj_quote_child_list',$where,$field);
		return $re;
	}

	public function getAll($table=null,$where=null,$field=null){
		if (!empty($where) && !empty($field)) {
			$re = M($table)->field($field)->where($where)->select();
		}else if(!empty($where) && empty($field)){
			$re = M($table)->where($where)->select();
		}else if(empty($where) && !empty($field)){
			$re = M($table)->field($field)->select();
		}
	   	return $re;
	}

	public function getRow($table=null,$where=null,$field=null){
		if (!empty($where) && !empty($field)) {
			$re = M($table)->field($field)->where($where)->find();
		}else if(!empty($where) && empty($field)){
			$re = M($table)->where($where)->find();
		}else if(empty($where) && !empty($field)){
			$re = M($table)->field($field)->find();
		}
	   	return $re;
	}
}