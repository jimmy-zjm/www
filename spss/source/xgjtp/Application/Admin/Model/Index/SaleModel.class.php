<?php
namespace Admin\Model\Index;
use \Think\Model;
/*
优惠活动模型
 */
class SaleModel extends Model{
    protected $trueTableName = 'xgj_sale';

    protected $_validate=array(
        array('dt_id','require','地推人员不能为空',1,''),
        array('start','require','开始区间不能为空',1,''),
        array('end','require','结束区间不能为空',1,''),
    );



	//插入优惠活动之前
    protected function _before_insert(&$data, $options){
        /*******处理基本信息*******/
        $data['addtime']   = time();//添加时间
        $data['edittime']   = '';
        return true;
    }

	/*
    修改优惠活动之前
     */
    protected function _before_update(&$data, $option){
        //处理基本数据
        $data['edittime']   = time();
        return true;

    }
}