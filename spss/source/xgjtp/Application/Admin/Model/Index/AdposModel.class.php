<?php
namespace Admin\Model\Index;
use \Think\Model;
/*
广告位置模型
 */
class AdposModel extends Model{
    protected $trueTableName = 'xgj_ad_pos';

    protected $_validate=array(
        array('name','require','广告不能为空',1,''),
        // array('width','1,1024','广告位的宽度应该为1-1024之间',1,'between'),
        array('width','number','广告位的宽度必须为数字',1),
        // array('height','1,1024','广告位的高度应该为1-1024之间',1,'between'),
        array('height','number','广告位的宽度必须为数字',1),
        array('pos_desc','0,255','广告位描述应该在0-255之内',2,'length'),
    );

}