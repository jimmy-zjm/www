<?php
namespace Home\Model;
use Think\Model;

/**
 * @author Administrator
 *
 */
class FurnishModel  extends Model {
	protected $autoCheckFields =false;
	
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

    //查询所有系统分类
    public function getCatName($c_id){
        $result=M('xgj_furnish_cat')->where(['cat_id' => $c_id])->find();
        return $result;
    }

    public function quoteList($catId){
        $result=M('xgj_furnish_quote')->field('quote_id,quote_name,is_putaway,cat_id')->where(['cat_id' => $catId,'is_putaway'=>'1'])->select();
		return $result;
	}

	//获取省市县中的省份
	function getPCD(){
        $result=M('xgj_area')->field("id,name")->where(['pid' => '100000'])->select();
		return $result;
	}

    //根据pid获取省市县名称
    function getPCDFind($pid){
        $result=M('xgj_area')->field("id,name")->where(['pid' => $pid])->select();
        return $result;
    }

    //根据名称获取省市县id
    function getPCDId($name,$lv=2){
        $where = "name = '{$name}'";
        if ($lv==1) $where .= " and pid = '100000'";
        else if ($lv==2) $where .= " and pid <> '100000'";
        $result = M('xgj_area')->where("$where")->getField('id');
        return $result;
    }

    public function getCustomerRow($name,$tel){
        $result=M('pad_customer')->where(['name' => $name,'tel'=>$tel])->find();
        if (!empty($result )&&$result['u_id']!=$_SESSION['dealerId']) {
            $re=M('pad_user')->where(['id' => $result['u_id']])->find();
            if ($re['pid']!=$_SESSION['dealerId']) {
                echo "<SCRIPT type='text/javascript'>alert('您无权限对此客户操作!!!');history.back();</SCRIPT>";exit;
            }
        }
        return $result;
    }

    //根据id获取省市县名称
    public function getPCDName($id){
        $area = M('xgj_area')->where(['id'=>$id])->getField('name');
        return $area;

    }

    public function getFurnishQuote($id){
        $result=M('xgj_furnish_quote')->where(['quote_id' => $id])->find();
        return $result;
    }

    public function addCustomerQuote($data){
        $result = M('pad_customer_quote')->add($data);
        return $result;
    }

    public function editCustomerQuote($data,$id){
        $result = M('pad_customer_quote')->where(['id'=>$id,'u_id'=>$_SESSION['dealerId']])->save($data);
        return $result;
    }

    public function updateCustomer($data,$id){
        $result = M('pad_customer')->where(['id'=>$id])->data($data)->save();
        return $result;
    }

    public function getCustomerQuoteData($id){
        $result=M('pad_customer_quote')->where(['id' => $id])->find();
        if(empty($result))
              return '';
        if($result['u_id']==$_SESSION['dealerId'])
                return $result;
        else{
                $data = M('pad_user')->where(['id' => $result['u_id']])->find();
                if($data['pid']==$_SESSION['dealerId'])
                    return $result;
                else{
                    $data = M('pad_user')->where(['id' => $data['pid']])->find();
                    if($data['pid']==$_SESSION['dealerId'])
                        return $result;
                    else
                        return '';
                }
        }
    }

    public function CustomerQuote($cid,$name){
        $result=M('pad_customer_quote')->where(['u_id' => $_SESSION['dealerId'],'c_id' => $cid,'name' => $name])->find();
        return $result;
    }

    public function getCustomer($id){
        $result=M('pad_customer')->where(['id' => $id])->find();
        return $result;
    }

    public function getPadOrder($id){
        $result=M('pad_order')->where(['q_id' =>$id , 'u_id' => $_SESSION['dealerId']])->find();
        return $result;
    }

    public function addCustomer($data){
        $result = M('pad_customer')->data($data)->add();
        return $result;
    }

    public function addPadOrder($data){
        $result = M('pad_order')->add($data);
        return $result;
    }
}

