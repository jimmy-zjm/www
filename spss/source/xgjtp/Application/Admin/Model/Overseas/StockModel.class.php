<?php
namespace Admin\Model\Overseas;
use Think\Model;
/**
 * 库存模型
 */
class StockModel extends Model{
    protected $trueTableName = 'xgj_ov_stock';



    //通过商品的id获取商品的信息
    //和所有的商品对应的复选属性,按照属性id升序排列
    public function getAll($goods_id){

        //查询出商品的名称和货号
        $goods = M('xgj_ov_goods')->field('id,goods_sn,goods_title,type_id')->where(array('id'=>$goods_id))->find();

        //查询出商品的属性的类型为复选的所有id,再通过商品属性表连接属性表,查询出指定商品的(商品属性和属性名称)的数据,条件为id在前者查询出的id列表中, 一定要按照xgj_ov_goods_attr表中的id升序排列, 属性名和值才能对应
        $sql = "SELECT a.*,b.name FROM xgj_ov_goods_attr AS a LEFT JOIN xgj_ov_attribute AS b ON a.attr_id=b.id WHERE a.attr_id IN (SELECT id FROM xgj_ov_attribute WHERE type_id={$goods['type_id']} AND mode = 1 AND input_type = 1) AND goods_id = {$goods_id} ORDER BY a.id ASC";
        $data = M()->query($sql);


        $goods_attr_list = array();
        foreach ($data as $key => $value) {
            $goods_attr_list[$value['attr_id']][] = $value;
        }

        //查询出所有的库存信息
        $goods_stock_arr = $this->field('id,number,goods_attr_id')->where(array('goods_id'=>$goods_id))->order('id ASC')->select();
        $gaModel = M('xgj_ov_goods_attr');
        foreach ($goods_stock_arr as &$v) {
            $temp = explode(',',$v['goods_attr_id']);
            foreach ($temp as $v1) {
                $v['attr_value'][] = $gaModel->where(array('id'=>$v1))->getField('attr_value');
            }
        }

        //返回所有数据
        return array(
            'goods'=>$goods,
            'goods_attr_list'=>$goods_attr_list,
            'goods_stock_arr'=>$goods_stock_arr,
        );
    }
}