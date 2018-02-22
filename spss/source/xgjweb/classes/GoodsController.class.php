<?php
/**
 * 商品控制器, 欧团,母婴基类
 * @date 2016-3-23
 * @author grass <14712905@qq.com>
 */

class GoodsController extends Controller{

    /*
    商品详情页面
     */
    public function detail(){
        switch_money();
        //接受数据
        $goods_id = I('id',true);
        
        if(empty($_GET['vid'])){
            $v_id = 0;
            $this->assign('vv',-1);
        }else{
            $v_id = I('vid',true)-1;
            $this->assign('vv',$v_id);
        }
        if(empty($goods_id)) $this->error('参数错误','index.php');

        $d = D('Eugroup')->getDetailAll($goods_id);
        $id=empty($_GET['cat_id'])?C('EU_CATE_ID1'):intval($_GET['cat_id']);
        $data = D('Eugroup')->getIndexAll($id);
        //获取面包屑数据
        // $cate_list  = D('Eugroup')->getCateList();
        // $bread_list = D('Eugroup')->getBreadByCateId($cate_list,$goods['cate_id']);
        if(isset($_GET['test'])){
            echo '<pre>';
                print_r($d['goods']);
            echo '</pre>';
        }
        
       
        $os = true;
        if (empty($_COOKIE['DetailClickCount'])) {
            $DetailClickCount = $goods_id.'/'.date('Y-m-d');
        }else{
            $DetailDataAll = explode('|', $_COOKIE['DetailClickCount']);
            foreach ($DetailDataAll as $key => $value) {
                $DetailDataRow = explode('/', $value);
                $DetailId = $DetailDataRow['0'];
                $DetailTime = $DetailDataRow['1'];
                if ($DetailId==$goods_id && $DetailTime==date('Y-m-d')) {
                    $os = false;
                }
            }
            $DetailClickCount = $_COOKIE['DetailClickCount'].'|'.$goods_id.'/'.date('Y-m-d');
        }

        if ($os != false){
            $c = D('Eugroup')->setDetailClickCount($goods_id);
            if ($c==true) {
                setcookie('DetailClickCount',$DetailClickCount,time()+86400);
            }
        }

        
        //评论列表
        $comment=D('Goods')->getEuCommentList($goods_id);  
        $this->assign('comment', $comment);

		$data1 = D('Eugroup')->category(1);
		$cate_list = D('Eugroup')->Ov_Category();
		$this->assign('eu_tree', $data1);
		$this->assign("ov_tree",$cate_list);
        
        $this->assign('detail',1);
        $this->assign('v_id',$v_id);
        $this->assign('goods', $d['goods']);
		$this->assign('ad_list', $data['ad_list']);//品牌
        $this->assign('cate_list', $data['cate_list']);
        $this->assign('brand_list', $data['brand_list']);//品牌

        // $this->assign('goods_comment', $data['comment']);//平均客户评论

        $this->assign('siblings_cate_list', $d['siblings_cate_list']);
        $this->assign('siblings_brand_list', $d['siblings_brand_list']);
        $this->assign('hot_list', $d['hot_list']);

        $this->display('eugoods/detail.html');
    }
	//赠品详情页
    public function giveaway(){
        switch_money();
        //接受数据
        $goods_id = I('id',true);
        
        if(empty($_GET['vid'])){
            $v_id = 0;
            $this->assign('vv',-1);
        }else{
            $v_id = I('vid',true)-1;
            $this->assign('vv',$v_id);
        }
        if(empty($goods_id)) $this->error('参数错误','index.php');

        $d = D('Eugroup')->getDetailAll($goods_id);
        $id=empty($_GET['cat_id'])?C('EU_CATE_ID1'):intval($_GET['cat_id']);
        $data = D('Eugroup')->getIndexAll($id);
        //获取面包屑数据
        // $cate_list  = D('Eugroup')->getCateList();
        // $bread_list = D('Eugroup')->getBreadByCateId($cate_list,$goods['cate_id']);
        if(isset($_GET['test'])){
            echo '<pre>';
                print_r($d['goods']);
            echo '</pre>';
        }
        //评论列表
        // $comment=D('Goods')->getCommentList($goods_id);	
        // $this->assign('comment', $comment);

        		$data1 = D('Eugroup')->category(1);
		$cate_list = D('Eugroup')->Ov_Category();
		$this->assign('eu_tree', $data1);
		$this->assign("ov_tree",$cate_list);
        
        $this->assign('detail',1);
        $this->assign('v_id',$v_id);
        $this->assign('goods', $d['goods']);
		$this->assign('ad_list', $data['ad_list']);//品牌
        $this->assign('cate_list', $data['cate_list']);
        $this->assign('brand_list', $data['brand_list']);//品牌
        $this->assign('siblings_cate_list', $d['siblings_cate_list']);
        $this->assign('siblings_brand_list', $d['siblings_brand_list']);
        $this->assign('hot_list', $d['hot_list']);

        $this->display('eugoods/giveaway.html');
    }
}