<?php
namespace Admin\Model\Furnish;
use \Think\Model;
/**
 * 品牌model
 */
class CouponModel extends Model{
    //指定字段
    protected $fields = array('id','description','is_show', 'name', 'url', 'order');
    protected $pk = 'id';

    //自动验证
    protected $_validate = array(
        array('name','require','品牌名称不能为空',0,'',3),
        array('name','','品牌已经存在',0,'unique',3),
        array('url','url','请输入合法的url',0,'',3),
        array('order','number', '请输入正确的排序号.',0,'',3),
        array('url','0,255','品牌url内容过长',0,'length',3),
        array('name','0,20','品牌名称内容过长',0,'length',3),
        array('description','0,255','品牌描述内容过长',0,'length',3),
    );

    /**
     * 执行添加品牌
     * @return [type] [description]
     */
    protected function _before_insert(&$data, $option){
        if(isset($_FILES['logo']) && $_FILES['logo']['error']==0){
            $info = uploadOne('logo', 'Brand', C('IMG_THUMB_BRAND'));
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

    /**
     * 执行修改商品品牌的logo图片
     */
    protected function _before_update(&$data, $option){
        if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0){
            //删除老的图片
            $oldImg = I('post.old_logo');
            deleteImage($oldImg, array(
                    array(50,200),
                ));

            //上传新图片
            $info = uploadOne('logo','Brand',array(
                    array(50,200),
                ));

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
}