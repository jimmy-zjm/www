<?php
namespace Admin\Model\Furnish;
use \Think\Model;

/**
 * 品牌model
 */
class CbrandInfoModel extends Model{
    //指定真实表名
    protected $trueTableName = 'xgj_cbrand_info';

    //指定字段
    protected $fields = array('id','image','content','b_id');
    protected $pk = 'id';

    /**
     * 执行添加品牌
     * @return [type] [sketch]
     */
    protected function _before_insert(&$data, $option){
        if(isset($_FILES['image']) && $_FILES['image']['error']==0){
            $info = uploadOne('image', 'FurnishCbrandInfo');
            if($info['code'] == 1){
                //上传图片成功
                $data['image'] = $info['images'];
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
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            //删除老的图片
            $oldImg = I('post.old_logo');
            deleteImage($oldImg);

            //上传新图片
            $info = uploadOne('image','FurnishCbrandInfo');

            if($info['code'] ==1){
                $data['image'] = $info['images'];
                return true;
            }else{
                $this->error = $info['error'];
                return false;
            }
        }
        $this->error = '数据没有被修改!';
        return true;
    }

    /**
     * 获取所有的品牌列表
     * @return [type] [description]
     */
    public function getAll(){

        //根据品牌名称搜索
        $where = array();
        if(isset($_GET['b_id'])){
            $where['b_id'] = array('eq',"%{$_GET['b_id']}%");
        }

        //分页
        $total = $this->where($where)->count();
        $page = getPage($total, C('CBRANGINFO_PAGE_SIZE'));
        $data['page'] = $page['page'];
        //执行查询数据
        $data['brand_list'] = $this->join('xgj_cbrand ON xgj_cbrand_info.b_id = xgj_cbrand.brand_id')->where($where)->limit($page['limit'])->select();

        //处理图片路径
        foreach($data['brand_list'] as &$value){
            $value['image'] = getImage($value['image']);
        }

        //返回数据
        return $data;
    }
    /**
     * 获取品牌名称
     * [getCbrand description]
     * @return [type] [description]
     */
    public function getCbrand($class_id=1){
        $map['is_show']=1;
        $map['class_id']=$class_id;
        $data =M('xgj_cbrand')->where($map)->select();
        return $data;
    }
}