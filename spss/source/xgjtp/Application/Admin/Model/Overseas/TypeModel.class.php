<?php
namespace Admin\Model\Overseas;
use \Think\Model;
/**
 * 类型模型
 */
class TypeModel extends Model{
    protected $trueTableName = 'xgj_ov_type';
    protected $fields = array('id','name','is_use','class_id');
    protected $insertFields = array('name','is_use','class_id');
    protected $updateFields = array('id','name','is_use','class_id');

    protected $_validate = array(
        array('name', 'require', '类型名称不能为空！', 1, 'regex', 3),
        array('name','0,20','类型名称长度不能大于20个字符',0,'length'),
    );


    public function getAll(){

        $where = array();
        if(isset($_GET['name'])){
            $where['name'] = array('like', "%{$_GET['name']}%");
        }
        $where['class_id'] = 2;
        $total = $this->where($where)->count();
        $page = getPage($total, C('TYPE_PAGE_SIZE'));
        $data['page'] = $page['page'];
        $data['data'] = $this->where($where)->limit($page['limit'])->select();
        return $data;
    }

}