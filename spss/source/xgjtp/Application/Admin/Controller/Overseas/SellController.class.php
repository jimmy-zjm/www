<?php
namespace Admin\Controller\Overseas;
use \Admin\Controller\Index\AdminController;

use \Think\PageAjax;
use \Think\Page;

class SellController extends AdminController{
    
    public function index(){
        $GoodModel=new \Admin\Model\Overseas\GoodsModel;
        $data_ = $GoodModel->getData();
        $this->assign('cate_list', $data_['cate_tree']);
        $this->display();
    }

    public function ajaxIndex(){
        $where = ' 1 = 1 '; // 搜索条件                 
        $cat_id = I('cate_id');

        // 关键词搜索               
        $goods_title = I('goods_title') ? trim(I('goods_title')) : '';
        $goods_sn    = I('goods_sn') ? trim(I('goods_sn')) : '';
        $identifier  = I('identifier') ? trim(I('identifier')) : '';
        $starttime   = I('starttime') ? trim(I('starttime')) : '';
        $endtime     = I('endtime') ? trim(I('endtime')) : '';

        if($goods_title)
        {
            $where .= " and g.goods_title like '%$goods_title%'" ;
        }

        if($goods_sn)
        {
            $where .= " and g.goods_sn like '%$goods_sn%'" ;
        }

        if($identifier)
        {
            $id=M('xgj_ov_dealer')->where("identifier = '$identifier'")->getField('id');
            $where .= " and g.d_id=$id ";
        }

        if ($starttime)
        {
            $where .= " and o.add_time >= '{$starttime}'";
        } 

        if ($endtime)
        {
            $where .= " and o.add_time <= '{$endtime}'";
        }  
                   
        if($cat_id > 0)
        {
            $grandson_ids = getCatGrandson($cat_id,'xgj_ov_category'); 
            $where .= " and cate_id in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
        }

        $count=M('xgj_ov_order_goods s')
                ->join('xgj_ov_order o on o.id=s.order_id')
                ->join('xgj_ov_goods g on g.id=s.goods_id')
                ->where($where)
                ->count();
        $Page  = getAjaxPage($count,10);
        
        $goodsList = M('xgj_ov_order_goods s')
                ->field('o.sn,o.add_time as sell_time,g.addtime,g.goods_title,g.goods_sn,g.cate_id,g.brand_id,g.d_id')
                ->join('xgj_ov_order o on o.id=s.order_id')
                ->join('xgj_ov_goods g on g.id=s.goods_id')
                ->where($where)
                ->limit($Page['limit'])
                ->select();
        $goodsList_ = M('xgj_ov_order_goods s')
                ->field('o.sn,o.add_time as sell_time,g.addtime,g.goods_title,g.goods_sn,g.cate_id,g.brand_id,g.d_id')
                ->join('xgj_ov_order o on o.id=s.order_id')
                ->join('xgj_ov_goods g on g.id=s.goods_id')
                ->where($where)
                ->select();
        foreach ($goodsList as &$v) {
            $v['addtime']=date("Y-m-d H:i:s",$v['addtime']);
        }
        foreach ($goodsList_ as &$v) {
            $v['addtime']=date("Y-m-d H:i:s",$v['addtime']);
        }
        $catList = D('xgj_ov_category')->select();
        $catList = convert_arr_key($catList, 'id');
        $brandList = D('xgj_ov_brand')->select();
        $brandList = convert_arr_key($brandList, 'id');
        $dealerList = D('xgj_ov_dealer')->select();
        $dealerList = convert_arr_key($dealerList, 'id');
        if($_GET['tab']=='daochu'){
            foreach ($goodsList_ as $k => $v) {
                $list['data'][$k]=[$goodsList_[$k][goods_sn],$goodsList_[$k][goods_title],$dealerList[$goodsList_[$k][d_id]][identifier],$catList[$goodsList_[$k][cate_id]][name],$brandList[$goodsList_[$k][brand_id]][name],$goodsList_[$k][addtime],'‘'.$goodsList_[$k][sn],$goodsList_[$k][sell_time]];
            }
            $list['key']=['商品编号','商品标题','供应商识别号','分类','品牌','发布时间','订单编号','出售日期'];
            $list['width']=['B'=>'20','C'=>'50','D'=>'20','E'=>'20','F'=>'20','G'=>'20','H'=>'30','I'=>'30'];
            exl($list);die;
        }
        $this->assign('catList',$catList);
        $this->assign('brandList',$brandList);
        $this->assign('dealerList',$dealerList);
        $this->assign('goodsList',$goodsList);
        $this->assign('page',$Page['page']);// 赋值分页输出
        $this->display();
    }
}