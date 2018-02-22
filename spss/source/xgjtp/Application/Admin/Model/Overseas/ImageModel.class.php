<?php
namespace Admin\Model\Overseas;
use \Think\Model;
/**
 * 图片空间模型
 */
class ImageModel extends Model{
    protected $trueTableName = 'xgj_ov_image';

    /*
    获取所有的商品图片
     */
    public function getAll(){
        //分页
        $total = $this->where(array('class_id'=>2))->count();
        $page = getPage($total, C('IMAGE_PAGE_SIZE'));
        $data['page'] = $page['page'];

        //图片数据
        $data['image_list'] = $this->where(array('class_id'=>2))->limit($page['limit'])->select();
        foreach ($data['image_list'] as &$image) {
            $image['url'] = getImage($image['url'],350,350);
        }
        return $data;
    }
}