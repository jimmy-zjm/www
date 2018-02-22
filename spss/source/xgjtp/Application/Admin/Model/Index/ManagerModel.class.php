<?php
namespace Admin\Model\Index;
use \Think\Model;
/*
管理员模型
 */
class ManagerModel extends Model{
    protected $trueTableName = 'xgj_admin_user';

    /*
    添加或者修改管理员的时候的验证规则
     */
    protected $_validate=array(
        array('user_name','require','用户名不能为空',1,'',1),
        array('user_name','','用户已经存在',2,'unique',1),
        array('password','require','密码不能为空',1,'',1),
        array('email','email','邮箱格式不正确',2),
        array('truename','/^[\x{4E00}-\x{9FA5}]+$/u','真实姓名添加不正确',2),
        array('truename','0,10','真实姓名长度应该在10个字符内',2,'length'),
        array('telphone','/^((\d{3,4}-)|\d{3.4}-)?\d{7,8}$/','联系电话填写不正确',2),
    );

    /*
    后台登陆的时候的验证规则
     */
    public $login_validate=array(
        array('username','require','用户名不能为空',1),
        array('password','require','密码不能为空',1),
        array('verifycode','require','验证码不能为空',1),
        array('verifycode','checkVerify','验证码不正确',1,'callback'),
    );

    /*
    登陆的时候验证验证码
     */
    public function checkVerify($code){
        $verify = new \Think\Verify();
        return $verify->check($code);
    }


    /*
    数据插入之前
     */
    protected function _before_insert(&$data,$option){
        $data['password'] = md5($data['password'].C('MD5_KEY'));
        $data['linkman']  = I('post.truename');
        $data['tel']      = I('post.telphone');
        $data['is_lock']  = I('post.is_use')==1?0:1;
        $data['add_time'] = time();
    }

    /*
    数据修改之前
     */
    protected function _before_update(&$data, $option){
        if(isset($data['user_name'])) unset($data['user_name']);//排除用户名, 不修改用户名

        //传了密码, 就修改密码
        if(!empty($data['password'])){
            $data['password'] = md5($data['password'].C('MD5_KEY'));
        }else{
            unset($data['password']);
        }

        $data['linkman']  = I('post.truename');
        $data['tel']      = I('post.telphone');
        $data['is_lock']  = I('post.is_use')==1?0:1;
    }


}