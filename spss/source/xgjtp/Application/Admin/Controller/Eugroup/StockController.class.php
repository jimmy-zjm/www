<?php
namespace Admin\Controller\Eugroup;
use \Admin\Controller\Index\AdminController;
/*
商品库存控制器
 */
class StockController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Eugroup\StockModel;
    }

    /*
    展示库存量
     */
    public function index(){
        //接受商品id
        $goods_id = I('get.id/d');
        if(!$goods_id) $this->redirect('Admin/Eugroup/Goods/index');

        //获取数据
        $data = $this->m->getAll($goods_id);
        $this->assign('goods',$data['goods']);
        $this->assign('goods_attr_list',$data['goods_attr_list']);
        $this->assign('goods_stock_list', $data['goods_stock_arr']);
        $this->display();
    }

    /*
    删除库存
     */
    public function delete($id){
        $id = (int)$id;
        if(!$id) die;
        if($this->m->delete($id)){
            echo 1;
        }else{
            echo -1;
        }
        die;
    }

    /*
    添加库存 或者 修改库存数量
     */
    public function insert(){
        // dump($_POST);die;//goods_stock
        if(!IS_POST) $this->redirect('index');
        //接受参数, 添加的时候的数据
        $goods_id      = I('post.goods_id/d');
        $goods_attr_id = I('post.goods_attr_id');
        $goods_stock   = I('post.goods_stock');
        $goods_stock = array_filter($goods_stock);//过滤没有填写库存的值

        //接受参数, 修改的时候的数据, 只修改库存数量
        $goods_stock_id       = I('post.goods_stock_id');//修改时的库存
        $goods_stock_number   = I('post.goods_stock_number');//修改时的库存

        //修改已经添加过的数据的 库存
        for($i=0,$len=count($goods_stock_id); $i<$len ;++$i){
            $goods_stock_number[$i] = max(0,intval($goods_stock_number[$i]));
            $this->m->where(array('id'=>$goods_stock_id[$i]))->setField('number',$goods_stock_number[$i]);
        }



        //拼装数据, 插入数据
        $map['goods_id'] = $info['goods_id'] = $goods_id;
        $goods_attr_id = array_chunk($goods_attr_id, count($goods_attr_id)/count($goods_stock));
        foreach ($goods_stock as $key => $value) {

            //商品有属性
            if($goods_attr_id){
                sort($goods_attr_id[$key]);
                if(in_array('',$goods_attr_id[$key])) continue;//没有选择属性值就不执行插入
                $map['goods_attr_id'] = $info['goods_attr_id'] = join(',',$goods_attr_id[$key]);
            }

            $info['number'] = max(0,$value);

            //是否已经有相同的商品属性 存在
            $stock = $this->m->where($map)->find();
            if($stock){
                //执行更新
                $this->m->where(array('id'=>$stock['id']))->setInc('number', $info['number']);
            }else{
                //执行添加
                if(!$this->m->data($info)->add()){
                    $this>error('添加失败');
                }
            }
        }
        $this->success('操作成功',U('index',array('id'=>$goods_id)));
    }
}