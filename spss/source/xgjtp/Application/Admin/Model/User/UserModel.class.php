<?php
namespace Admin\Model\User;
use \Think\Model;
/*
优惠券模型
 */
class UserModel extends Model{
    protected $trueTableName = 'xgj_users';

    protected $_validate=array(
        array('user_name','require','用户名不能为空',1,'',1),
        array('user_name','','用户已经存在',2,'unique',1),
        array('mobile_phone',"/^[1][34578][0-9]{9}$/",'请输入正确的手机号码'),
        array('mobile_phone','','手机号码已存在',2,'unique',1),
        array('password','require','密码不能为空',1,'',1),
        array('password',"/^\w{6,15}$/",'密码为6-15英文或数字','','',1),
        array('rpassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
        array('email','email','邮箱格式不正确',2)
    );
    
    /**
     * 执行添加品牌
     * @return [type] [description]
     */
    protected function _before_insert(&$data, $option){
        /*******处理基本信息*******/
        $data['password']             = md5($data['password'].C('MD5_PASSWORD'));
        return $data;
    }

    protected function _before_update(&$data, $option){
        if(isset($data['user_name'])) unset($data['user_name']);//排除用户名, 不修改用户名

        //传了密码, 就修改密码
        if(!empty($data['password'])){
            $data['password'] = md5($data['password'].C('MD5_PASSWORD'));
        }else{
            unset($data['password']);
        }

    }
}   