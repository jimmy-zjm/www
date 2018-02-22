<?php
namespace Admin\Model\Pad;
use \Think\Model;
/*
优惠券模型
 */
class UserModel extends Model{
    protected $trueTableName = 'pad_user';

    protected $_validate=array(
        array('name','require','用户名不能为空',1,'',1),
        array('system','require','开通系统不能为空',1,'',1),
        array('name','','用户已经存在',2,'unique',1),
        //array('province','require','公司名称不能为空',1,'',1),
        array('tel',"/^[1][34578][0-9]{9}$/",'请输入正确的手机号码'),
        array('tel','','手机号码已存在',2,'unique',1),
        array('psd','require','密码不能为空',1,'',1),
        array('psd',"/^\w{6,15}$/",'密码为6-15英文或数字','','',1),
        array('rpassword','psd','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
        array('email','email','邮箱格式不正确',2)
    );
    
    /**
     * 执行添加品牌
     * @return [type] [description]
     */
    protected function _before_insert(&$data, $option){
        /*******处理基本信息*******/
        
        if($_POST['is_try']==1){
            $data['is_try']=1;
        }else{
            $data['is_try']=2;
        }
       
        $data['spsd']       = trim($data['psd']);
        $data['psd']        = md5(trim($data['psd']).C('MD5_PAD_PSD'));
        $data['add_time']   = time();
        $data['start_time'] = strtotime($_POST['start_time']);
        $data['end_time']   = strtotime($_POST['end_time']);
        //var_dump($data);die;
        return $data;
    }



	


    protected function _before_update(&$data, $option){
        if($_POST['is_try']==1){
            $data['is_try']=1;
        }else{
            $data['is_try']=2;
        }
        if(isset($data['name'])) unset($data['name']);//排除用户名, 不修改用户名
        /*$data['province']   = getPCDName($_POST['province']);
        $data['city']       = getPCDName($_POST['city']);
        $data['district']   = getPCDName($_POST['district']);*/
        //var_dump($_POST);die;
        $data['start_time'] = strtotime($_POST['start_time']);
        $data['end_time']   = strtotime($_POST['end_time']);
        //传了密码, 就修改密码
        if(!empty($data['psd'])){
            $data['spsd']             = trim($data['psd']);
            $data['psd'] = md5(trim($data['psd']).C('MD5_PAD_PSD'));
        }else{
            unset($data['psd']);
        }
    }

    //获取分类数据
    public function getCatTree($arr,$id = 0,$lev=0) {
        $tree = array();
    
        foreach($arr as $v) {
            if($v['pid'] == $id) {
                $v['lev'] = $lev;
                $tree[] = $v;
    
                $tree = array_merge($tree,$this->getCatTree($arr,$v['id'],$lev+1));
            }
        }
    
        return $tree;
    }
}   