<?php
namespace Admin\Model\Furnish;
use \Think\Model;
/**
 * 品牌model
 */
class CategoryModel extends Model{
	protected $trueTableName='xgj_furnish_cat';
    //指定字段
    protected $fields = array('cat_id','cat_name','is_show', 'sort_order','desc','image');
    protected $pk = 'cat_id';

    //自动验证
    protected $_validate = array(
        array('cat_name','require','分类名称不能为空',0,'',3),
        array('cat_name','','分类名称已经存在',0,'unique',3),
        array('sort_order','number', '请输入正确的排序号.',0,'',3),
        array('desc','0,255','简单描述太长',0,'length'),
    );

    /**
     * 执行添加分类之前
     */
    protected function _before_insert(&$data, $option){
    	/*******处理基本信息*******/
    	$data['add_time']             = time();//添加时间
    	$data['sort_order']           = I('post.sort_order/d');
    	$data['is_show']          	  = I('post.is_show')?1:0;
        //商品商品的封面图
        if(isset($_FILES['image'])&&$_FILES['image']['error']==0){
            $image = uploadOne('image','Category');
            if($image['code']!=1){
                //商品封面图片上传失败
                $this->error = $image['error'];
                return false;
            }
            $data['image'] = $image['images'];
        }
    }
	
    //修改商品基本信息
    protected function _before_update(&$data,$options){
        /*******处理基本信息*******/
        $data['add_time']             = time();//添加时间
        $data['sort_order']           = I('post.sort_order/d');
        $data['is_show']              = I('post.is_show')?1:0;
        
        //商品商品的封面图
        if(isset($_FILES['image'])&&$_FILES['image']['error']==0){//有新的图片上传
            $image = uploadOne('image','Category');
            if($image['code']!=1){
                //商品封面图片上传失败
                $this->error = $image['error'];
                return false;
            }
            $data['image'] = $image['images'];
        
            //删除y原来的封面图片
            $img_url = M('xgj_furnish_cat')->where($options['where'])->getField('image');
            deleteImage($img_url);
        }
        return true;
    }

    /**
     * 查询所有系统分类
     * @return unknown
     */
    public function getAll(){
    	//分页
    	$total        = $this->count();
    	$page         = getPage($total, C('FURNISH_CAT_PAGE_SIZE'));
    	$data['page'] = $page['page'];
    	// 商品数据
    	$data['cat_list'] =$this->field('*')->order('cat_id asc')->limit($page['limit'])->select();
        foreach ($data['cat_list'] as $k=>$v){
            $data['cat_list'][$k]['image']=getImage($v['image']);
        }
    	
    	return $data;
    }
    
    /**
     * 查询一条系统分类
     * @param unknown $id
     * @return unknown
     */
    public function getOne($id){
    	// 商品数据
    	$data=$this->field('*')->where(array('cat_id'=>$id))->find();
    	$data['image']=getImage($data['image']);     	 
    	return $data;
    }
}