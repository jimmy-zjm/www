<?php
namespace Admin\Model\Eugroup;
use \Think\Model;
/**
 * 类型模型
 */
class TypeModel extends Model{
    protected $trueTableName = 'xgj_eu_type';
    protected $fields = array('id','name','is_use');
    protected $insertFields = array('name','is_use');
    protected $updateFields = array('id','name','is_use');

    protected $_validate = array(
        array('name', 'require', '类型名称不能为空！', 1, 'regex', 3),
        // array('name','','类型名称已经存在',0,'unique'),
        array('name','0,20','类型名称长度不能大于20个字符',0,'length'),
    );


    public function getAll(){

        $where = array();
        if(isset($_GET['name'])){
            $where['name'] = array('like', "%{$_GET['name']}%");
        }
        // $where['class_id'] = '1';
        $total = $this->where($where)->count();
        $page = getPage($total, C('TYPE_PAGE_SIZE'));
        $data['page'] = $page['page'];
        $data['data'] = $this->where($where)->limit($page['limit'])->select();

        //计算出类型对应的属性的数量
        // $attr_model = M('Attribute');
        // foreach ($data['data'] as &$value) {
        //     $num = $attr_model->where(array('type_id'=>$value['id']))->count();
        //     $value['attr_num'] = $num;
        // }
        return $data;
    }

}