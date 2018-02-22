<?php
namespace Home\Model;
use Think\Model;

/**
 * @author Administrator
 *
 */
class IndexModel  extends Model {
	protected $autoCheckFields =false;
	
	//查询所有系统分类
	public function quoteCat(){
        $result=M('xgj_furnish_cat')->where(['is_show' => 1])->select();
		return $result;
	}

	public function user(){
        $result=M('pad_user')->where(['id' => $_SESSION['dealerId']])->find();
        return $result;
    }

    public function selectSetUp(){
        $result=M('pad_user')->where(['id' => $_SESSION['dealerId']])->find();
        return $result;
    }

    public function userCity(){
        $result=M('pad_user')->where(['id' => $_SESSION['dealerId']])->find();
        $service = explode('|', $result['p_service_city_all']);
        foreach ($service as $k => $v) {
            $city = explode('-', $v);
            $result['prov'][] = $city['0'];
            $result['service'][$city['0']][] = $city['1'];
        }
        return $result;
    }

	 public function setup($data){
        $result = M('pad_user')->where(['id'=>$_SESSION['dealerId']])->data($data)->save();
        return $result;
    }
}

