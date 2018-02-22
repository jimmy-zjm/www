<?php
/**
 * 欧洲团代购控制器
 * @date 2016-3-9
 * @author grass <14712905@qq.com>
 */
class EugroupController extends GoodsController{

	/*
	首页
	 */
	public function index(){

		$data = D('Eugroup')->getIndexAll(C('EU_CATE_ID2'));
		$data_ = D('Eugroup')->getIndexAll(C('EU_CATE_ID1'));
		if(isset($_GET['test']) && C('APP_DEBUG')){
			echo '<pre>';
			    print_r($data['ad_list']);
			echo '</pre>';
		}

		$data1 = D('Eugroup')->category(1);
		$cate_list = D('Eugroup')->Ov_Category();
		$this->assign('eu_tree', $data1);
		$this->assign("ov_tree",$cate_list);
        $this->assign('f1', $data['goods_cate']);//卫浴
		$this->assign('f2', $data_['goods_cate']);//厨房
        $this->assign('ad_list', $data['ad_list']);//广告
        $this->assign('brand_list', $data['brand_list']);//品牌
        $this->assign('cate_list', $data['cate_list']);//分类
		$this->display('eugoods/index.html');
	}

	/*
	列表页面
	 */
	public function lst(){
		$data = D('Eugroup')->getListAll();
		if(isset($_GET['test']) && C('APP_DEBUG')){
			echo '<pre>';
			    print_r($data);
			echo '</pre>';
		}

		require_once(WWW_DIR."/libs/page.php");
        //分页每页的条数
        $num=6;

        //全部订单
        if(empty($_GET['p']) || $_GET['p']<=1){
            $page = 1;
            $p1 = 1;
        }else{
            $page = $_GET['p'];
            $p1 = 0;
        }
    
        //显示列表内容
        $page1 = ($page-1)*$num;
       	$data_p = D('Eugroup')->getListAll_P($page1,$num);

        //分页的总条数
        $orderAll = count($data['goods_list']);
        //实例化分页类
        $t = new Page($num, $orderAll, $page, 5, "eugroup.php?list&id=".$_GET['id']."&p=");
        //分页样式
        $page=$t->subPageCss2();//分页样式
        //模板传值
        if (empty($data_p) && $p1 == 1) {
            $this->assign("page",'');
        }else{
            $this->assign("page",$page);
        }

		$this->assign('bread_list', $data['bread_list']);
		$this->assign('goods_list', $data_p);
		// $this->assign('goods_list', $data['goods_list']);
		$this->display('eugoods/list.html');
	}

	/*
	全球攻略
	 */
	public function raiders(){

		$this->display('eugoods/raiders.html');
	}
	//欧团首页
	public function euIndex(){

		//获取所有欧洲团代购分类
		
		$data = D('Eugroup')->getIndexAll(C('EU_CATE_ID2'),'0,3');
		$data_ = D('Eugroup')->getIndexAll(C('EU_CATE_ID1'),'0,3');
		//合作品牌
		$cbrandList=D('Eugroup')->getCbrandAll(2);
		$this->assign('cbrandList',$cbrandList);

		$data1 = D('Eugroup')->category(1);
		$cate_list = D('Eugroup')->Ov_Category();
		$this->assign('eu_tree', $data1);
		$this->assign("ov_tree",$cate_list);

		//var_dump('<pre>',$aboutus);die();
		//var_dump($data['goods_cate']);exit;
		$this->assign('f1', $data['goods_cate']);//卫浴
		$this->assign('f2', $data_['goods_cate']);//厨房
		$this->display('euindex.tpl.html');
	}
	//欧团品牌详情页
	public function developer(){

		$this->display('euDeveloper.tpl.html');
	}
	//赠品专区
	public function giveaway(){

		if (isset($_GET['zp'])) {
			$_SESSION['zp']='1';
		}
		$data = D('Eugroup')->getIndexAll(C('EU_CATE_ID2'),'','1');
		$data_ = D('Eugroup')->getIndexAll(C('EU_CATE_ID1'),'','1');
		if(isset($_GET['test']) && C('APP_DEBUG')){
			echo '<pre>';
			    print_r($data['ad_list']);
			echo '</pre>';
		}

		$data1 = D('Eugroup')->category(1);
		$cate_list = D('Eugroup')->Ov_Category();
		$this->assign('eu_tree', $data1);
		$this->assign("ov_tree",$cate_list);
        $this->assign('f1', $data['goods_cate']);//卫浴
		$this->assign('f2', $data_['goods_cate']);//厨房
        $this->assign('ad_list', $data['ad_list']);//广告
        $this->assign('brand_list', $data['brand_list']);//品牌
        $this->assign('cate_list', $data['cate_list']);//分类
		$this->display('eugoods/giveawayList.html');
	}
	 
}