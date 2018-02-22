<?php
namespace Admin\Controller\Eugroup;
use \Admin\Controller\Index\AdminController;

use \Think\PageAjax;
use \Think\Page;

class SellController extends AdminController{
    
    public function index(){
        $GoodModel=new \Admin\Model\Eugroup\GoodsModel;
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
            $grandson_ids = getCatGrandson($cat_id,'xgj_eu_category'); 
            $where .= " and cate_id in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
        }

        $count=M('xgj_eu_order_goods s')
                ->join('xgj_eu_order o on o.id=s.order_id')
                ->join('xgj_eu_goods_new g on g.id=s.goods_id')
                ->where($where)
                ->count();
        $Page  = getAjaxPage($count,10);
        
        $goodsList = M('xgj_eu_order_goods s')
                ->field('o.sn,o.add_time as sell_time,g.addtime,g.goods_title,g.goods_sn,g.cate_id,g.brand_id')
                ->join('xgj_eu_order o on o.id=s.order_id')
                ->join('xgj_eu_goods_new g on g.id=s.goods_id')
                ->where($where)
                ->limit($Page['limit'])
                ->select();
        $goodsList_ = M('xgj_eu_order_goods s')
                ->field('o.sn,o.add_time as sell_time,g.addtime,g.goods_title,g.goods_sn,g.cate_id,g.brand_id')
                ->join('xgj_eu_order o on o.id=s.order_id')
                ->join('xgj_eu_goods_new g on g.id=s.goods_id')
                ->where($where)
                ->select();

        foreach ($goodsList as &$v) {
            $v['addtime']=date("Y-m-d H:i:s",$v['addtime']);
        }
        foreach ($goodsList_ as &$v) {
            $v['addtime']=date("Y-m-d H:i:s",$v['addtime']);
        }
        $catList = D('xgj_eu_category')->select();
        $catList = convert_arr_key($catList, 'id');
        $brandList = D('xgj_eu_brand')->select();
        $brandList = convert_arr_key($brandList, 'id');
        if($_GET['tab']=='daochu'){
            foreach ($goodsList_ as $k => $v) {
                $list['data'][$k]=[$goodsList_[$k][goods_sn],$goodsList_[$k][goods_title],$catList[$goodsList_[$k][cate_id]][name],$brandList[$goodsList_[$k][brand_id]][name],$goodsList_[$k][addtime],'‘'.$goodsList_[$k][sn],$goodsList_[$k][sell_time]];
            }
            $list['key']=['商品编号','商品标题','分类','品牌','发布时间','订单编号','出售日期'];
            $list['width']=['B'=>'20','C'=>'50','D'=>'30','E'=>'30','F'=>'30','G'=>'30','H'=>'30'];
            exl($list);die;
        }
        $this->assign('catList',$catList);
        $this->assign('brandList',$brandList);
        $this->assign('goodsList',$goodsList);
        $this->assign('page',$Page['page']);// 赋值分页输出
        $this->display();
    }
}