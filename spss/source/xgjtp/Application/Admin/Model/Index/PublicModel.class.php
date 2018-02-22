<?php
namespace Admin\Model\Index;
use \Think\Model;
/*
管理员模型
 */
class PublicModel extends Model{
    protected $trueTableName = 'xgj_admin_user';
    protected $_validate=array(
        array('user_name','require','用户名不能为空',1,'',1),
        array('user_name','','用户已经存在',2,'unique',1),
        array('password','require','密码不能为空',1,'',1),
        array('email','email','邮箱格式不正确',2)
    );
}