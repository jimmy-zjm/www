<?php
namespace Admin\Model\Eugroup;
use \Think\Model;
/**
 * 图片空间模型
 */
class ImageModel extends Model{
    protected $trueTableName = 'xgj_eu_image';

    /*
    获取所有的商品图片
     */
    public function getAll(){
        //分页
        $total = $this->count();
        $page = getPage($total, C('IMAGE_PAGE_SIZE'));
        $data['page'] = $page['page'];

        //图片数据
        $data['image_list'] = $this->limit($page['limit'])->select();
        foreach ($data['image_list'] as &$image) {
            $image['url'] = getImage($image['url'],350,350);
        }
        return $data;
    }
}