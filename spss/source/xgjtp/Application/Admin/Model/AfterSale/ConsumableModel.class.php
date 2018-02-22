<?php
namespace Admin\Model\AfterSale;
use \Think\Model;
/**
 * 机电耗材model
 */
class ConsumableModel extends Model{
	protected $trueTableName='xgj_s_consumable';


    //添加之前处理一下数据
    protected function _before_insert(&$data, $options){
    	/*******处理基本信息*******/
    	$data['add_time']             = time();//添加时间    	
    	//商品商品的封面图
    	if(isset($_FILES['c_img'])&&$_FILES['c_img']['error']==0){
    		$image = uploadOne('c_img','Consumable',C('IMG_THUMB_FACE'));
    		if($image['code']!=1){
    			//商品封面图片上传失败
    			$this->error = $image['error'];
    			return false;
    		}
    		$data['c_img'] = $image['images'];
    	}
    }
   

    protected function _before_update(&$data,$options){
    	//商品商品的封面图
    	if(isset($_FILES['c_img'])&&$_FILES['c_img']['error']==0){//有新的图片上传
    		$image = uploadOne('c_img','Consumable',C('IMG_THUMB_FACE'));
            //var_dump($image,'444');die;
    		if($image['code']!=1){
    			//商品封面图片上传失败
    			$this->error = $image['error'];
    			return false;
    		}
    		$data['c_img'] = $image['images'];
    	
    		//删除y原来的封面图片
    		$img_url = M('xgj_s_consumable')->where($options['where'])->getField('c_img');
    		deleteImage($img_url,C('IMG_THUMB_FACE'));
    	}
        //var_dump($data,'256');die;
    	return true;
    }

    public function getInfo($id){
    	$info=M('xgj_s_consumable')->find($id);
    	return $info;
    }
}