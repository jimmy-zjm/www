<?php
namespace Admin\Model\Dealer;
use \Think\Model;
/**
 * 品牌model
 */
class DealerModel extends Model{
    protected $trueTableName='xgj_furnish_dealer';
    protected $fields= array('d_id', 'd_name', 'd_pwd', 'd_email', 'd_district', 'd_province', 'd_city', 'd_service_city_all', 'd_service_city','d_area','d_company', 'd_company_desc', 'd_legalperson', 'd_legalperson_phone', 'd_reg_address', 'd_rel_address', 'd_pickup_address', 'd_hall_address', 'd_store_figure', 'd_map', 'd_linkman', 'd_link_phone', 'd_runstatus', 'd_rank', 'add_time', 'finance_message', 'message_status', 'd_price');
	protected $pk='d_id';
    protected $_validate = array(
    		array('d_name','require','服务商名称不能为空!',1),
    		array('d_name','','不能有相同的服务商名称!',1,'unique',1),
    		array('d_company','','不能有相同的服务商公司!',1,'unique',1),
    		array('d_company','require','服务商公司不能为空!',1),
    		array('d_email','email','请输入正确的邮箱!'),
    		array('d_legalperson','require','公司法人为必填',1),
    		array('d_legalperson_phone','require','公司法人电话为必填',1),
        );

    /**
     * 执行添加
     * @return [type] [description]
     */
    protected function _before_insert(&$data, $option){
    	$data['add_time']             = time();//添加时间
    	if(isset($_FILES['d_store_figure'])&&$_FILES['d_store_figure']['error']==0){
    		$image = uploadOne('d_store_figure','Dealer',C('IMG_THUMB_FACE'));
    		if($image['code']!=1){
    			//商品封面图片上传失败
    			$this->error = $image['error'];
    			return false;
    		}
    		$data['d_store_figure'] = $image['images'];
    	}
    	
    }

    /**
     * 执行修改
     */
    protected function _after_insert($data, $option){
    	$d_id = $data['d_id'];
    	/***上传商品相册图片***/
    	if(!empty($_FILES['img_url'])){
    		$pics = array();
    		$img_model = M('xgj_furnish_dealer_img');
    		 
    		for ($i=0; $i < count($_FILES['img_url']['name']); ++$i) {
    			$pics[] = array(
    					'name' => $_FILES['img_url']['name'][$i],
    					'type' => $_FILES['img_url']['type'][$i],
    					'tmp_name' => $_FILES['img_url']['tmp_name'][$i],
    					'error' => $_FILES['img_url']['error'][$i],
    					'size' => $_FILES['img_url']['size'][$i],
    			);
    		}
    		 
    		foreach ($pics as $key=>$value) {
    			$_FILES[$key] = $value;
    			if($_FILES[$key]['error']==0){
    				//将上传成功的商品相册图片地址保存起来
    				$image_info = uploadOne($key,'Dealer',C('IMG_THUMB'));
    				if($image_info['code']==1){
    					$data = array(
    							'd_id' => $d_id,
    							'url'      => $image_info['images'],
    							'is_show'  => 1
    					);
    					if(!$img_model->add($data)){
    						$this->error = '展厅展示图数据插入失败';
    						return false;
    					}
    				}else{
    					$this->error = '展厅展示图上传失败:'.$image_info['error'];
    					return false;
    				}
    			}
    		}
    	}
    }
    
    //修改商品基本信息
    protected function _before_update(&$data,$options){
    	if(isset($_FILES['d_store_figure'])&&$_FILES['d_store_figure']['error']==0){
    		$image = uploadOne('d_store_figure','Dealer',C('IMG_THUMB_FACE'));
    		if($image['code']!=1){
    			//商品封面图片上传失败
    			$this->error = $image['error'];
    			return false;
    		}
    		$data['d_store_figure'] = $image['images'];
    		 
    		//删除y原来的封面图片
    		$img_url = M('xgj_furnish_dealer')->where($options['where'])->getField('d_store_figure');
    		deleteImage($img_url,C('IMG_THUMB_FACE'));
    	}
    	return true;
    }
    
    //商品信息修改成功后,
    protected function _after_update($data, $options){
    	$d_id = $options['where']['d_id'];
    	 
    	if(!empty($_FILES['img_url'])){
    		$pics = array();
    		$img_model = M('xgj_furnish_dealer_img');
    		 
    		for ($i=0; $i < count($_FILES['img_url']['name']); ++$i) {
    			$pics[] = array(
    					'name' => $_FILES['img_url']['name'][$i],
    					'type' => $_FILES['img_url']['type'][$i],
    					'tmp_name' => $_FILES['img_url']['tmp_name'][$i],
    					'error' => $_FILES['img_url']['error'][$i],
    					'size' => $_FILES['img_url']['size'][$i],
    			);
    		}
    		 
    		foreach ($pics as $key=>$value) {
    			$_FILES[$key] = $value;
    			if($_FILES[$key]['error']==0){
    				//将上传成功的商品相册图片地址保存起来
    				$image_info = uploadOne($key,'Dealer',C('IMG_THUMB'));
    				if($image_info['code']==1){
    					$data = array(
    							'd_id' => $d_id,
    							'url'      => $image_info['images'],
    							'is_show'  => 1
    					);
    					if(!$img_model->add($data)){
    						$this->error = '展厅展示图数据插入失败';
    						return false;
    					}
    				}else{
    					$this->error = '展厅展示图上传失败:'.$image_info['error'];
    					return false;
    				}
    			}
    		}
    	}
    }

    public function getAll(){
    	//拼凑条件
    	$map = array();
    	if(isset($_GET['send']) || isset($_GET['district'])){
    		$keyword = I('keyword');
    		
    		if(!empty($keyword)){
    			$map['d_company'] = array('like',"%{$keyword}%");
    		}
    		   	
    		$district = I('district');   	
	    	if(!empty($district)){
	    		switch ($district){
	    			case 1:
	    				$map['d_district'] = array('eq',"华中地区");
	    				break;
    				case 2:
    					$map['d_district'] = array('eq',"华东地区");
    					break;
    				case 3:
    					$map['d_district'] = array('eq',"西南/西北地区");
    					break;
	    			case 4:
	    				$map['d_district'] = array('eq',"华北/东北地区");
	    				break;
	    			case 5:
	    				$map['d_district'] = array('eq',"华南地区");
	    				break;
	    		}
	    		
	    	}
    	}
    	
    	//分页
    	$total        = $this->where($map)->count();
    	$page         = getPage($total, C('DEALER_PAGE_SIZE'));
    	$data['page'] = $page['page'];
    	 
    	//服务商数据
    	$data['dealer_list'] =$this->field('*')->where($map)->order('d_id DESC')->limit($page['limit'])->select();
    	
    	//处理数据
        array_walk($data['dealer_list'], function(&$v){
            $v['d_store_figure'] = getImage($v['d_store_figure'],54,54);
            $v['d_map'] = getImage($v['d_map'],54,54);
            $v['add_time']  = date('Y-m-d H:i', $v['add_time']);
            $v['d_runstatus']  = $v['d_runstatus']==0?'不正常':'正常';
        });
    	// foreach ($data['dealer_list'] as &$v) {
    	// 	$v['d_store_figure'] = getImage($v['d_store_figure'],54,54);
    	// 	$v['d_map'] = getImage($v['d_map'],54,54);
    	// 	$v['add_time']  = date('Y-m-d H:i', $v['add_time']);
    	// 	$v['d_runstatus']  = $v['d_runstatus']==0?'不正常':'正常';
    	// }
    	//var_dump($data);exit;
    	return $data;
    }
    
    public function getOne($id){
    	$data['dealer'] = $this->where(array('d_id'=>$id))->find();
    	$data['dealer']['d_store_figure'] = getImage($data['dealer']['d_store_figure'],350,350);
    	$data['image'] = M('xgj_furnish_dealer_img')->where(array('d_id'=>$id))->select();
    	array_walk($data['image'], function(&$v,$k){
    		$v['url'] = getImage($v['url'],350,350);
    	});
    	return $data;
    }
    
    public function delete($id){
    	$dealer = $this->where(array('d_id'=>$id))->find();
    	//var_dump($id);exit;
    	if(!$dealer) return false;
    	if(M('xgj_furnish_dealer')->where(array('d_id'=>$id))->delete()){
    		
    		// 取出图片地址,删除图和缩略图片
    		$image_path = $dealer['d_store_figure'];
    		deleteImage($image_path, C('IMG_THUMB_FACE'));
    
    		//删除展厅图片 和展厅缩略图片 
    		$image_list = M('xgj_furnish_dealer_img')->where(array('d_id'=>$id))->select();//var_dump($image_list);exit;
    		foreach ($image_list as $vv) {
    			deleteImage($vv['url'], C('IMG_THUMB'));
    		}
    		M('xgj_furnish_dealer_img')->where(array('d_id'=>$id))->delete();
    
    		return true;
    	}else{
    		//var_dump($this->getLastSql());exit;
    		return false;
    	}
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}