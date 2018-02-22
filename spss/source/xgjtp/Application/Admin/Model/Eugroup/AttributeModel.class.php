<?php
namespace Admin\Model\Eugroup;
use \Think\Model;
/**
 * 类型模型
 */
class AttributeModel extends Model{
    protected $trueTableName = 'xgj_eu_attribute';
    protected $insertFields = array('name','type_id','mode','input_type','value_list');
    protected $updateFields = array('id','name','type_id','mode','input_type','value_list');

    protected $_validate = array(
        array('name', 'require', '属性名称不能为空！', 1, 'regex', 3),
        // array('name','','属性名称已经存在',0,'unique',3),
        array('type_id','0','请选择所属商品的类型',1,'notequal',3),
        array('type_id','number','请选择所属商品的类型不正确',1,'',3),
        array('mode','0,2','请正确选择录入方式',1,'between',3),
        array('input_type','0,2','请正确选择可选值列表',0,'between',3),
    );

    protected $_auto = array(
    );


    protected function _after_select(&$resultSet,$options) {
        $arr = array('唯一属性','可选属性');
        $arr2 = array('手工输入','列表中选择','多行文本框');
        foreach($resultSet as &$value){
            $value['mode'] = $arr[$value['mode']];
            $value['input_type'] = $arr2[$value['input_type']];
        }
    }


    //通过类型id获取商品的属性
    public function getAttribute($type_id){
        // $data = $this->where(array('type_id'=>$type_id))->select();
        // dump($data);die;
    }

}