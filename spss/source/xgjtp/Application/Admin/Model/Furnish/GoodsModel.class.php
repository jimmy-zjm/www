<?php
namespace Admin\Model\Furnish;
use \Think\Model;
/**
 * 品牌model
 */
class GoodsModel extends Model{
	protected $trueTableName='xgj_furnish_goods';
    protected $fields= array('goods_id', 'class_id', 'cat_id', 'keywords', 'goods_sn', 'goods_mnemonic', 'goods_name', 'goods_name_style', 'b_id', 'brand', 'goods_lv', 'goods_brand', 'goods_model', 'goods_unit', 'click_count', 'goods_number', 'market_price', 'shop_price', 'goods_brief', 'goods_desc', 'specification', 'goods_img', 'add_time', 'update_time', 'seller_note', 'starttime', 'endtime', 'is_putaway', 'is_delete', 'shop_price_a','features','origin');
	protected $pk='goods_id';
    protected $_validate = array(
    		array('goods_name','require','商品名称不能为空!',1),
    		array('goods_sn','','不能有相同的商品编码!',1,'unique',1),
            array('goods_sn','/^\w+$/','商品编码格式不正确，由字母、数字、下划线组成',2,'',1),
            array('goods_sn','1,30','商品编号的长度为1-30位',2,'length',1),
    		array('goods_mnemonic','','不能有相同的商品助记码!',1,'unique',1),
            array('goods_mnemonic','require','商品助记码不能为空!',1),
    		array('goods_model','require','商品型号不能为空!',1),
    		array('goods_unit','require','商品单位不能为空!',1),
    		array('market_price','require','市场价格为必填',1),
    		array('market_price','currency','市场价格不合理',1),
    		array('goods_lv','require','是否为主辅材',1),
    		array('shop_price','require','商品售价为必填',1),
            array('shop_price','currency','商品售价不合理',1),
    		array('shop_price_a','currency','结算价不合理',1),
    		//array('is_putaway','1','是否上架信息错误',0,'equal'),
    		array('keywords','0,255','商品的关键字内容太长',0,'length'),
    		array('goods_desc','0,255','商品的简单描述内容过长',0,'length'),
        );

    //添加商品之前处理一下数据
    protected function _before_insert(&$data, $options){
    	/*******处理基本信息*******/
    	$data['add_time']             = time();//添加时间
    	$data['is_delete']           = 0;  
    	//$data['is_putaway']          = I('post.is_putaway')?1:0;
    	$data['starttime'] = strtotime($data['starttime']);
    	$data['endtime']   = strtotime($data['endtime']);
        $data['goods_sn']  = empty($_POST['goods_sn'])?uniqid():I('post.goods_sn');
    	
    	//商品商品的封面图
    	if(isset($_FILES['goods_img'])&&$_FILES['goods_img']['error']==0){
    		$image = uploadOne('goods_img','Furnish',C('IMG_THUMB_FACE'));
    		if($image['code']!=1){
    			//商品封面图片上传失败
    			$this->error = $image['error'];
    			return false;
    		}
    		$data['goods_img'] = $image['images'];
    	}
    	
    }

    /*******插入商品之后**************************************************************/
    protected function _after_insert($data, $options){
    	//商品id
    	$goods_id = $data['goods_id'];
    	
    	//修改商品的编号
    	$goods_sn = C('GOODS_SN_PREFIX').str_pad($goods_id,8,0,STR_PAD_LEFT);
    	$this->where(array('goods_id'=>$goods_id))->setField('goods_sn', $goods_sn);
    	
    	/***上传商品相册图片***/
    	if(!empty($_FILES['img_url'])){
    		$pics = array();
    		$img_model = M('xgj_furnish_image');
    	
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
    				$image_info = uploadOne($key,'Furnish',C('IMG_THUMB'));
    				if($image_info['code']==1){
    					$data = array(
    							'goods_id' => $goods_id,
    							'url'      => $image_info['images'],
    							'is_show'  => 1
    					);
    					if(!$img_model->add($data)){
    						$this->error = '商品图片数据插入失败';
    						return false;
    					}
    				}else{
    					$this->error = '商品相册图片上传失败:'.$image_info['error'];
    					return false;
    				}
    			}
    		}
    	}
    	
    }
    
    //修改商品基本信息
    protected function _before_update(&$data,$options){
    	/*******处理基本信息*******/
    	$data['update_time']         = time();//添加时间
    	$data['is_delete']           = 0;
    	$data['is_putaway']          = I('post.is_putaway')?1:0;
    	$data['starttime'] = strtotime($data['starttime']);
    	$data['endtime']   = strtotime($data['endtime']);
    	
    	//商品商品的封面图
    	if(isset($_FILES['goods_img'])&&$_FILES['goods_img']['error']==0){//有新的图片上传
    		$image = uploadOne('goods_img','Furnish',C('IMG_THUMB_FACE'));
    		if($image['code']!=1){
    			//商品封面图片上传失败
    			$this->error = $image['error'];
    			return false;
    		}
    		$data['goods_img'] = $image['images'];
    	
    		//删除y原来的封面图片
    		$img_url = M('xgj_furnish_goods')->where($options['where'])->getField('goods_img');
    		deleteImage($img_url,C('IMG_THUMB_FACE'));
    	}
    	return true;
    }

    //商品信息修改成功后,
    protected function _after_update($data, $options){
    	$goods_id = $options['where']['goods_id'];
    	
    	/***上传商品相册图片***/
    	if(!empty($_FILES['img_url'])){
    		$pics = array();
    		$img_model = M('xgj_furnish_image');
    	
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
    				$image_info = uploadOne($key,'Furnish',C('IMG_THUMB'));
    				if($image_info['code']==1){
    					$data = array(
    							'goods_id' => $goods_id,
    							'url'      => $image_info['images'],
    							'is_show'  => 1
    					);
    					if(!$img_model->add($data)){
    						$this->error = '商品图片数据插入失败';
    						return false;
    					}
    				}else{
    					$this->error = '商品相册图片上传失败:'.$image_info['error'];
    					return false;
    				}
    			}
    		}
    	}
    }
    
    /**
     * 获取商品的品牌数据
     * @return [type] [description]
     */
    public function getData(){
    
    	//获取品牌数据
    	$brand = new \Admin\Model\Furnish\BrandModel;
    	$brand_list = $brand->where(array('is_show'=>1))->order('`order` asc')->select();
    	$data['brand_list'] = $brand_list;
    	return $data;
    }
    
    

    /**
     * 获取所有的商品信息
     * @return [type] [description]
     */
    public function getAll($is_delete=0,$pagesize=FURNISH_GOODS_PAGE_SIZE){
    	//拼凑条件
    	$map = array();
    	if(isset($_GET['send'])){
			$goods_name = I('goods_name');
			$goods_sn   = I('goods_sn');
			$b_id       = I('b_id/d');
			$goods_lv   = I('goods_lv/d');
			$is_putaway = I('is_putaway/d');
			$starttime  =strtotime(I('starttime'));
			$endtime    =strtotime(I('endtime'));
    		
    		if(!empty($goods_name)){
    			$map['goods_name'] = array('like',"%{$goods_name}%");
    		}
    		if(!empty($goods_sn)){
    			$map['goods_sn'] = array('eq',$goods_sn);
    		}
    		if(!empty($b_id)){
    			$map['b_id'] = array('eq',$b_id);
    		}
    		if(!empty($goods_lv)){
    			$map['goods_lv'] = array('eq',$goods_lv);
    		}
    		if($is_putaway == 1){
    			$map['is_putaway'] = array('eq',1);
    		}elseif($is_putaway == 2){
    			$map['is_putaway'] = array('eq',0);
    		}
            if (!empty($starttime) && !empty($endtime)) {
                $map['starttime'] = array('egt',$starttime);
                $map['endtime'] = array('elt',$endtime);
            }else if(!empty($starttime)){
                $map['starttime'] = array('egt',$starttime);
            }else if(!empty($endtime)){
                $map['endtime'] = array('elt',$endtime);
            }
    	}
    
    	$map['is_delete'] = array('eq', $is_delete);

    	//分页
    	$total        = $this->where($map)->count();
    	$page         = getPage($total, C("$pagesize"));

    	$data['page'] = $page['page'];
    	
    	// 商品数据
    	$data['goods_list'] =$this->field('g.*,b.name AS brand_name')->alias('g')->join('LEFT JOIN xgj_furnish_brand AS b ON g.b_id=b.brand_id')->where($map)->order('add_time DESC')->limit($page['limit'])->select();
    
    	//处理数据
    	foreach ($data['goods_list'] as &$v) {
    		$v['goods_img'] = getImage($v['goods_img'],54,54);
    		$v['starttime'] = date('Y-m-d H:i', $v['starttime']);
    		$v['endtime']   = date('Y-m-d H:i', $v['endtime']);
    		$v['add_time']  = date('Y-m-d H:i', $v['add_time']);
    		$v['stock']     = max(0,$v['goods_number']);
    	}
    
    	return $data;
    }
    
    /*
     获取商品数据, 用于回填
    */
    public function getGoods($id){
    	
    	//商品数据
    	$data['goods'] = $this->where(array('goods_id'=>$id))->find();
    	$data['goods']['goods_img'] = getImage($data['goods']['goods_img'],350,350);
    	$data['goods']['starttime'] = date('Y/m/d H:i:s',$data['goods']['starttime']);
    	$data['goods']['endtime'] = date('Y/m/d H:i:s',$data['goods']['endtime']);
    	$data['goods']['goods_brief'] = htmlspecialchars_decode($data['goods']['goods_brief']);
    
    	//通过商品id查询出商品相册信息
    	$data['image'] = M('xgj_furnish_image')->where(array('goods_id'=>$id))->select();
    	array_walk($data['image'], function(&$v,$k){
    		$v['url'] = getImage($v['url'],350,350);
    	});   		
    	//返回数据
    	return $data;
    }
	
    /*
     真正删除商品
    */
    public function deleteGoods($id){
    	$goods = $this->where(array('goods_id'=>$id))->find();
    	if(!$goods) return false;
    	if($this->where(array(
    			'goods_id'=>array('eq',$id),
    			'is_delete'=>array('eq',1),
    		))->delete()){
    
    		// 取出商品的图片地址,删除封面图和封面缩略图片
    		$image_path = $goods['goods_img'];
    		deleteImage($image_path, C('IMG_THUMB_FACE'));
    
    		//删除商品的相册图片 和 相册缩略图片 和 相册记录
    		$image_list = M('xgj_furnish_image')->where(array('goods_id'=>$id))->select();
    		foreach ($image_list as $vv) {
    			deleteImage($vv['url'], C('IMG_THUMB'));
    		}
    		M('xgj_furnish_image')->where(array('goods_id'=>$id))->delete();

    		return true;
    	}else{
    		return false;
    	}
    }
}