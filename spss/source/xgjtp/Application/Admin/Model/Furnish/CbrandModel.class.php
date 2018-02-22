<?php
namespace Admin\Model\Furnish;
use \Think\Model;

/**
 * 品牌model
 */
class CbrandModel extends Model{
    //指定真实表名
    protected $trueTableName = 'xgj_cbrand';

    //指定字段
    protected $fields = array('brand_id','name','sketch','is_show', 'url', 'order','product','logo','class_id');
    protected $pk = 'brand_id';

    //自动验证
    protected $_validate = array(
        array('name','require','品牌名称不能为空',0,'',3),
        array('name','','品牌已经存在',0,'unique',1),
        array('class_id','require','分类名称不能为空',0,'',3),
        // array('url','url','请输入合法的url',0,'',3),
        array('order','number', '请输入正确的排序号.',0,'',3),
        //array('url','0,255','品牌url内容过长',0,'length',3),
        array('name','0,20','品牌名称内容过长',0,'length',3),
        array('product','require','品牌名称不能为空',0,'',3),
        //array('sketch','0,255','品牌简述内容过长',0,'length',3),
    );

    /**
     * 执行添加合作品牌
     * @return [type] [sketch]
     */
    protected function _before_insert(&$data, $option){
        if(isset($_FILES['logo']) && $_FILES['logo']['error']==0){
            $info = uploadOne('logo', 'FurnishCbrand', C('IMG_THUMB_CBRAND'));
            if($info['code'] == 1){
                //上传图片成功
                $data['logo'] = $info['images'];
                $data['is_show'] = isset($data['is_show'])&&$data['is_show']==1?1:0;
                return true;
            }
            $this->error = $info['error'];
            return false;
        }
        return true;
    }

    /*******插入合作品牌之后**************************************************************/
    protected function _after_insert($data, $options){
        //合作品牌id
        $brand_id = $data['brand_id'];
                
        /***上传合作品牌相册图片***/
        if(!empty($_FILES['img_url'])){
            $pics = array();
            $img_model = M('xgj_cbrand_image');
        
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
                    //将上传成功的合作品牌相册图片地址保存起来
                    $image_info = uploadOne($key, 'FurnishCbrand', C('IMG_THUMB_CBRAND'));
                    if($image_info['code']==1){
                        $data = array(
                                'b_id'    => $brand_id,
                                'url'     => $image_info['images'],
                                'is_show' => 1
                        );
                        if(!$img_model->add($data)){
                            $this->error = '品牌图片数据插入失败';
                            return false;
                        }
                    }else{
                        $this->error = '品牌相册图片上传失败:'.$image_info['error'];
                        return false;
                    }
                }
            }
        }
        
    }

    /**
     * 执行修改合作品牌的logo图片
     */
    protected function _before_update(&$data, $option){
        if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0){
            //删除老的图片
            $oldImg = I('post.old_logo');
            deleteImage($oldImg, C('IMG_THUMB_CBRAND'));

            //上传新图片
            $info = uploadOne('logo','FurnishCbrand',C('IMG_THUMB_CBRAND'));

            if($info['code'] ==1){
                $data['logo'] = $info['images'];
            }else{
                $this->error = $info['error'];
                return false;
            }
        }
        $this->error = '数据没有被修改!';
        return true;
    }

    //合作品牌信息修改成功后,
    protected function _after_update($data, $options){
        $brand_id = $options['where']['brand_id'];
        
        /***上传合作品牌相册图片***/
        if(!empty($_FILES['img_url'])){
            $pics = array();
            $img_model = M('xgj_cbrand_image');
        
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
                    //将上传成功的合作品牌相册图片地址保存起来
                    $image_info = uploadOne($key, 'FurnishCbrand', C('IMG_THUMB_CBRAND'));
                    if($image_info['code']==1){
                        $data = array(
                                'b_id' => $brand_id,
                                'url'      => $image_info['images'],
                                'is_show'  => 1
                        );
                        if(!$img_model->add($data)){
                            $this->error = '合作品牌图片数据插入失败';
                            return false;
                        }
                    }else{
                        $this->error = '合作品牌相册图片上传失败:'.$image_info['error'];
                        return false;
                    }
                }
            }
        }
    }

    /**
     * 获取所有的品牌列表
     * @return [type] [description]
     */
    public function getAll(){

        //根据品牌名称搜索
        $where = array();
        if(isset($_GET['name'])){
            $where['name'] = array('like',"%{$_GET['name']}%");
        }

        //分页
        $total = $this->where($where)->count();
        $page = getPage($total, C('CBRAND_PAGE_SIZE'));
        $data['page'] = $page['page'];
        //执行查询数据
        $data['brand_list'] = $this->where($where)->limit($page['limit'])->select();

        //处理图片路径
        $size = C('IMG_THUMB_CBRAND');
        foreach($data['brand_list'] as &$value){
            $value['logo'] = getImage($value['logo']);
        }

        //返回数据
        return $data;
    }
}