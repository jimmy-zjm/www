<?php
namespace Home\Model;
use Think\Model;

/**
 * @author Administrator
 *
 */
class ManageModel  extends Model {
    protected $autoCheckFields =false;
	//获取省市县中的省份
    function getPCD(){
        $result=M('xgj_area')->field("id,name")->where(['pid' => '100000'])->select();
        return $result;
    }

    //根据id获取省市县名称
    function getPCDName($id){
        $area = M('xgj_area')->where(['id'=>$id])->getField('name');
        return $area;

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
        if (!empty($result )&&$result['u_id']!=$_SESSION['pad_id']) {
            $re=M('pad_user')->where(['id' => $result['u_id']])->find();
            if ($re['pid']!=$_SESSION['pad_id']) {
                echo "<SCRIPT type='text/javascript'>alert('您无权限对此客户操作!!!');history.back();</SCRIPT>";exit;
            }
        }
        return $result;
    }

    public function getFurnishQuote($id){
        $result=M('xgj_furnish_quote')->where(['quote_id' => $id])->find();
        return $result;
    }

    public function getCustomer($id){
        $result=M('pad_customer')->where(['id' => $id])->find();
        return $result;
    }

    public function addCustomer($data){
        $result = M('pad_customer')->data($data)->add();
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
            else
                return '';
        }
    }

    public function getPadOrder($id){
        $result=M('pad_order')->where(['q_id' =>$id , 'u_id' => $_SESSION['dealerId']])->find();
        return $result;
    }
}

