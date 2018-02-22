<?php
namespace Admin\Model\Eugroup;
use \Think\Model;

/**
 * 品牌model
 */
class BrandModel extends Model{
    //指定真实表名
    protected $trueTableName = 'xgj_eu_brand';

    //指定字段
    protected $fields = array('id','description','is_show', 'name', 'url', 'order','cate_id');
    protected $pk = 'id';

    //自动验证
    protected $_validate = array(
        array('name','require','品牌名称不能为空',0,'',3),
        array('name','','品牌已经存在',0,'unique',1),
        array('name','0,20','品牌名称内容过长',0,'length',3),
        array('url','url','请输入合法的url',0,'',3),
        array('url','0,255','品牌url内容过长',0,'length',3),
        array('cate_id','require','品牌分类不能为空',0,'',3),
        array('description','0,255','品牌描述内容过长',0,'length',3),
        array('order','number', '请输入正确的排序号.',0,'',3),
        array('order','require','品牌排序不能为空',2),
        array('order','number','品牌排序必须为数字',2),
        array('order','0,255','品牌排序应该在0-255之间',2,'between'),
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
            deleteImage($oldImg, C('IMG_THUMB_BRAND'));

            //上传新图片
            $info = uploadOne('logo','Brand',C('IMG_THUMB_BRAND'));

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
        // $where['class_id'] = '1';

        //分页
        $total = $this->where($where)->count();
        $page = getPage($total, C('BRAND_PAGE_SIZE'));
        $data['page'] = $page['page'];
        //执行查询数据
        $data['brand_list'] = $this->where($where)->limit($page['limit'])->select();

        //处理图片路径
        $size = C('IMG_THUMB_BRAND');
        foreach($data['brand_list'] as &$value){
            $value['logo'] = getImage($value['logo'],$size[0][0], $size[0][1]);
        }

        //返回数据
        return $data;
    }


    public function cate_list($pid = 0, $num = 0, $curid = 0){
        //$sql="select * from xgj_article_cat where parent_id=$pid";
        $result=M('xgj_eu_category')->where("pid=$pid")->select();
        
        $str = str_repeat ( '+', $num );
        $num += 2;
        $string='';
        foreach ($result as $v){
            if ($v ['id'] == $curid) {
                $string.="<option selected value='{$v['id']}'>|+{$str}{$v['name']}</option>{$this->cate_list($v ['id'], $num ,$curid)}";
            } else {
                $string.="<option value='{$v['id']}'>|+{$str}{$v['name']}</option>{$this->cate_list($v ['id'], $num ,$curid )}";
            }
        }        
        return $string;
    }
}