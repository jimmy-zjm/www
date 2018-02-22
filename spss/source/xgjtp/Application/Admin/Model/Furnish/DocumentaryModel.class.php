<?php
namespace Admin\Model\Furnish;
use \Think\Model;
/**
 * 系统商品model
 */
class DocumentaryModel extends Model{
	protected $trueTableName='xgj_documentary';
	/**
     * 执行添加前
     * @return [type] [description]
     */
    protected function _before_insert(&$data, $option){
        
    }
    
    /**
     * 执行添加前
     * @return [type] [description]
     */
    protected function _after_insert($data, $option){
    
    }

    /**
     * 执行修改前
     */
    protected function _before_update(&$data, $option){
		
        if(empty($data['kefu_id']))
             unset($data['kefu_id']);
		if(empty($data['gendan_id']))
             unset($data['gendan_id']);
		$data['update_time']=time();
        
    }
    
    /**
     * 执行添加前
     * @return [type] [description]
     */
    protected function _after_update($data, $option){
    
    }
}