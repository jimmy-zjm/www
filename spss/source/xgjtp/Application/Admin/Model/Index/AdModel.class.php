<?php
namespace Admin\Model\Index;
use \Think\Model;
/*
广告位置模型
 */
class AdModel extends Model{
    protected $trueTableName = 'xgj_ad';
    protected $fileds = array('id','ad_pos_id','type','title','url','image','is_on','start_time','end_time','linkman','linkman_email','linkman_tel');

    protected $_validate=array(
        array('type','require','广告类型不能为空',1,''),
        array('ad_pos_id','require','广告位置不能为空',1,''),
        // array('url','url','广告url地址不合法',1),
        array('start_time','require','必须选择广告开始时间',1,''),
        array('end_time','require','必须选择广告结束时间',1,''),
        array('linkman_email','email','联系人邮箱不合法',2),
    );

    /*
    插入广告之前
     */
    protected function _before_insert(&$data, $option){

        //处理基本数据
        $data['start_time'] = strtotime(I('post.start_time'));
        $data['end_time']   = strtotime(I('post.end_time'));
        $data['is_on']      = $data['is_on']==1?1:0;

        //如果有图片就上传
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $info = uploadOne('image','Ad');

            if($info['code']==1){
                $data['image'] = $info['images'];
            }else{
                $this->error = $info['error'];
                return false;
            }
        }
        return true;
    }

    /*
    修改广告之前
     */
    protected function _before_update(&$data, $option){

        //处理基本数据
        $data['start_time'] = strtotime(I('post.start_time'));
        $data['end_time']   = strtotime(I('post.end_time'));
        $data['is_on']      = $data['is_on']==1?1:0;

        //如果有图片就上传
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $info = uploadOne('image','Ad');

            if($info['code']==1){
                $data['image'] = $info['images'];
            }else{
                $this->error = $info['error'];
                return false;
            }

            //删除老的图片
            $old_img = M('xgj_ad')->where($option['where'])->getField('image');
            if(!empty($old_img)) deleteImage($old_img);
        }
        return true;

    }


}