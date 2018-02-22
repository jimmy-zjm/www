<?php
namespace Home\Model;
use Think\Model;
/*
管理员模型
 */
class PublicModel extends Model{
    protected $trueTableName = 'xgj_users';
    protected $_validate=array(
        array('userName','require','手机号码不能为空',1,'',1),
        array('userName','/^[1][34578][0-9]{9}$/','手机格式错误',),
        array('passWord','require','密码不能为空',1,'',1),
    );
}