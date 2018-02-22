<?php
/**
 * 搜索控制器
 * 适用于欧洲团购&德国母婴
 * @date 2016-3-23
 * @author grass <14712905@qq.com>
 */
class SearchController extends GoodsController{

	 public function seachAll(){
		  $keywords=I('seachall');
		  if(empty($keywords)){
		     $this->error('请输入关键字!!',"index.php");
          }
		  $sql = "SELECT id,class_id,goods_title,face_image FROM xgj_eu_goods_new WHERE is_putaway=1 AND is_delete=0 AND class_id=1 AND goods_title LIKE '%{$keywords}%' ";
          $euList = M()->fetchAll($sql);
		  $sql1 = "SELECT id,class_id,goods_title,face_image FROM xgj_ov_goods WHERE is_putaway=1 AND is_delete=0 AND class_id=2 AND goods_title LIKE '%{$keywords}%' ";
          $ovList = M()->fetchAll($sql1);
		  $sql2 = "SELECT quote_id,quote_name,img FROM xgj_furnish_quote WHERE is_putaway=1 AND quote_name LIKE '%{$keywords}%' ";
          $fuList = M()->fetchAll($sql2);

		  $data1=D('Eugroup')->category(1);
          $this->assign('eu_tree',$data1);
		  $data11=D('Eugroup')->Ov_Category();
		  $this->assign('ov_tree',$data11);
		 
		  $this->assign('euList',$euList);
		  $this->assign('ovList',$ovList);
		  $this->assign('fuList',$fuList);
		  $this->display('common/searchAll.html');
	}
    public function index(){
        //获取欧元转人民币的汇率
        if(isset($_SESSION['currency'])){
            $currency=$_SESSION['currency'];
        }else{
            $_SESSION['currency']=switch_money();
            $currency=$_SESSION['currency']; 
        }
        $m = D('Goods');

        /*******************接收条件*********************/
       
        $bid      = I('bid');//品牌id
        $cid      = I('cid');//分类id
        $pid      = I('pid');//价格分段数据
        /*******************接收条件*********************/
        
        /*******************全部结果导航*********************/
        $search_result = array();
        if(!empty($bid)){
            $search_result[] = array(
                'name' => M('xgj_eu_brand')->where(array('id'=>$bid))->getField('name'),
                'type' => 'bid',
            );
        }
        if(!empty($cid)){
            $search_result[] = array(
                'name' => M('xgj_eu_category')->where(array('id'=>$cid))->getField('name'),
                'type' => 'cid',
            );
        }
        if(!empty($pid)){
            $search_result[] = array(
                'name' => $pid,
                'type' => 'pid',
            );
        }

        // var_dump($search_result);
        /*******************全部结果导航*********************/

		$data111 = D('Eugroup')->getIndexAll(C('EU_CATE_ID1'));
		//var_dump('<pre>',$data111['brand_list']);die();
		$this->assign('cate_list', $data111['cate_list']);//分类
		$this->assign('ad_list', $data111['ad_list']);//品牌logo
        $this->assign('brand_list', $data111['brand_list']);//品牌
        /*******************拼凑条件*********************/
        $where  = 'WHERE is_putaway=1 AND is_delete=0 AND class_id=1 ';
        $where2 = 'WHERE is_putaway=1 AND is_delete=0 AND class_id=1 ';
        $limit  = '0,20';
       
        if(!empty($bid)){
            $where .= "AND brand_id = $bid ";
			$where2 .= "AND brand_id = $bid ";
        }
        if(!empty($cid)){
            $ids=$this->implodeInByCateId($cid);
            $where .= "AND cate_id $ids ";
			$where2 .= "AND cate_id $ids ";
        }

        if(!empty($pid)){
            if(strpos($pid, '-')!==false){
                $temp        = explode('-', $pid);
                $start_price = $temp[0];
                $end_price   = $temp[1];
            }else{
                if(preg_match('/^\d+/',$pid,$start_price_a)){
                    $start_price = $start_price_a[0];
                }else{
                    $start_price = 0;
                }
            }
 
            if(isset($end_price)){
                $where .= "AND purchase >= $start_price AND purchase <= $end_price ";                
            }else{
                $where .= "AND purchase >= $start_price ";                
            }
        }
        /*******************拼凑条件*********************/



        /*******************查询商品数据*********************/
        $sql = "SELECT * FROM xgj_eu_goods_new $where LIMIT $limit";
        $goods_list = M()->fetchAll($sql);

        $goods_list = $m->processGoods($goods_list);//
        
        $goods_list = $this->getTotal($goods_list);
        //var_dump($goods_list);die;
        /*******************查询商品数据*********************/

        require_once(WWW_DIR."/libs/page.php");
        //分页每页的条数
        $num=20;

        //全部订单
        if(empty($_GET['p']) || $_GET['p']<=1){
            $page = 1;
            $p1 = 1;
        }else{
            $page = $_GET['p'];
            $p1 = 0;
        }

        $sql1 = "SELECT * FROM xgj_eu_goods_new $where";
        $goods_list1 = M()->fetchAll($sql1);
        $goods_list1 = $m->processGoods($goods_list1);
        $goods_list1 = $this->getTotal($goods_list1);

        //显示列表内容
        $page1 = ($page-1)*$num;
        $sql2 = "SELECT * FROM xgj_eu_goods_new $where limit $page1,$num";
        $goods_list2 = M()->fetchAll($sql2);
        $goods_list = $m->processGoods($goods_list2);
        $goods_list = $this->getTotal($goods_list);
        //分页的总条数
        $orderAll = count($goods_list1);

        
        $bids=empty($bid)?'':"&bid=$bid";
        $cids=empty($cid)?'':"&cid=$cid";
        $pids=empty($pid)?'':"&pid=$pid";
        //实例化分页类
        $t = new Page($num, $orderAll, $page, 5, "search.php?eu$bids$cids$pids&p=");
        //分页样式
        $page=$t->subPageCss2();//分页样式
        //模板传值
        if (empty($goods_list) && $p1 == 1) {
            $this->assign("page",'');
        }else{
            $this->assign("page",$page);
        }

        
        $bidss=empty($bid)?'':"and n.brand_id = $bid";
        $cidss=empty($cid)?'':"and n.cate_id = $cid";

        $sql = "SELECT distinct n.brand_id id,b.name name FROM xgj_eu_goods_new n join xgj_eu_brand b on n.brand_id = b.id  where n.is_putaway = '1'   $bidss $cidss";
        $goods_title = M()->fetchAll($sql);

        $this->assign("goods_title",$goods_title);


        /*******************筛选数据*********************/
        //筛选数据:
        /*
        思路:
        根据输入的关键字找到很多商品
        根据商品的分类id找到顶级分类
        #根据顶级分类找到下面的品牌列表
        #根据商品的分类id找出同辈分类数据 + 当前筛选的分类的同辈分类
        #根据商品的最低价格和最高价格计算价格分段数据
        */
        $cate_list_eu = $m->getCateList();
        

        //根据顶级分类找到下面的品牌列表
        $idss = array();
        foreach ($goods_list as $v) {
            $idss[] = $m->getTopCateById($cate_list_eu, $v['cate_id']);//获取顶级id
        }
        $idss = array_unique($idss);
        $in  = empty($idss)?'':"WHERE cate_id IN(".join(',',$idss).")";
        $sql = "SELECT * FROM xgj_eu_brand $in";
        $brand_list_eu = M()->fetchAll($sql);


        //根据商品的分类id找出同辈分类数据 + 当前筛选的分类的同辈分类
        $ids = array();
        foreach ($goods_list as $v2) {
            $ids = array_merge($ids,$m->getSiblingsCateById($v2['cate_id']));
        }
        $cate_list_eu = array();
        // if(!empty($cid)){
        //     $cate_list_eu = array_merge($cate_list_eu, $m->getSiblingsCateById($cid));
        // }
        foreach ($ids as $v3) {
            $cate_list_eu[$v3['id']] = $v3;
        }
        $ids = array_keys($cate_list_eu);
        $in  = empty($ids)?'':"AND cate_id IN(".join(',',$ids).")";


        //根据商品的最低价格和最高价格计算价格分段数据
        $sql        = "SELECT MAX(purchase) maxprice,MIN(purchase) minprice FROM xgj_eu_goods_new $where2";//查询出最大的价格和最小的价格
        $price_list = array();
        $price      = M()->fetch($sql);

        if($price['minprice']){
            $minprice   = floor($price['minprice']/10)*10;
            $maxprice   = ceil($price['maxprice']/10)*10;
            $split      = 3;
            $sprice     = ($maxprice-$minprice)/$split;
            for ($i=0; $i < $split; ++$i) {
                $start_price = floor($minprice/10)*10;
                if($i==$split-1){
                    $end_price = floor(($minprice+$sprice)/10)*10;
                }else{
                    $end_price = floor(($minprice+$sprice)/10)*10-1;
                }
                $price_list[] = $start_price . '-' . $end_price;
                $minprice+=$sprice;
            }
            $price_list   = array_unique($price_list);
            $price_list[] = $end_price.'以上';
        }
        
        /*******************筛选数据*********************/


        /*******************热卖推荐*********************/
        $hot_list = D('Goods')->getHotGoods(8);
        $hot_list = $this->getTotal($hot_list);
        /*******************热卖推荐*********************/





        /*******************分配数据*********************/
        if(isset($_GET['test']) && C('APP_DEBUG')){
            // var_dump($brand_list_eu);
            // var_dump($cate_list_eu);
            // var_dump($price_list);
            var_dump($goods_list);
            // var_dump($hot_list);
        }


        $data1=D('Eugroup')->category(1);
        $this->assign('eu_tree',$data1);
		$data11=D('Eugroup')->Ov_Category();
		$this->assign('ov_tree',$data11);

        $this->assign('hot_list', $hot_list);
        $this->assign('search_result', $search_result);
        $this->assign('brand_list_eu', $brand_list_eu);
        $this->assign('cate_list_eu', $cate_list_eu);
        $this->assign('price_list', $price_list);
        $this->assign('goods_list', $goods_list);
        $this->display('common/search.html');
    }

    public function overseas(){
        /*******************接收条件*********************/
        $keywords = I('k');
        $brid     = I('brid');//品牌id
        $caid     = I('caid');//分类id
        $prid     = I('prid');//价格分段数据
        $cuid     = I('cuid');//海外超市国家专区数据
        /*******************接收条件*********************/
        

        /*******************全部结果导航*********************/
        $search_result = array();
        if(!empty($brid)){
            $search_result[] = array(
                'name' => M('xgj_ov_brand')->where(array('id'=>$brid))->getField('name'),
                'type' => 'brid',
            );
        }
        if(!empty($caid)){
            $search_result[] = array(
                'name' => M('xgj_ov_category')->where(array('id'=>$caid))->getField('name'),
                'type' => 'caid',
            );
        }
        if(!empty($prid)){
            $search_result[] = array(
                'name' => $prid,
                'type' => 'prid',
            );
        }
        if(!empty($cuid)){
            $search_result[] = array(
                'name' => M('xgj_eu_country')->where(array('id'=>$cuid))->getField('name'),
                'type' => 'cuid',
            );
        }
        /*******************拼凑条件*********************/
        $where  = 'WHERE is_putaway=1 AND is_delete=0 AND class_id=2 ';
        $where2 = 'WHERE is_putaway=1 AND is_delete=0 AND class_id=2 ';
        $limit  = '0,12';
        if(!empty($keywords)){
            $where  .= "AND goods_title LIKE '%{$keywords}%' ";
            $where2 .= "AND goods_title LIKE '%{$keywords}%' ";
        }
        if(!empty($brid)){
            $where .= "AND brand_id = $brid ";
        }
        if(!empty($caid)){
            $ids=$this->implodeInByCateId($caid);
            $where .= "AND cate_id $ids ";
        }
         if(!empty($cuid)){
            $where .= "AND country_id = $cuid ";
        }
        if(!empty($prid)){
            if(strpos($prid, '-')!==false){
                $temp        = explode('-', $prid);
                $start_price = $temp[0];
                $end_price   = $temp[1];
            }else{
                if(preg_match('/^\d+/',$prid,$start_price_a)){
                    $start_price = $start_price_a[0];
                }else{
                    $start_price = 0;
                }
            }
 
            if(isset($end_price)){
                $where .= "AND purchase >= $start_price AND purchase <= $end_price ";                
            }else{
                $where .= "AND purchase >= $start_price ";                
            }
        }
        /*******************拼凑条件*********************/


        /*******************查询商品数据*********************/
        $sql = "SELECT * FROM xgj_ov_goods $where LIMIT $limit";
        $goods_list = M()->fetchAll($sql);
        //var_dump($goods_list);die;
        /*******************查询商品数据*********************/


        /*******************查询商品结果显示*********************/
        require_once(WWW_DIR."/libs/page.php");
        //分页每页的条数
        $num=12;

        //全部订单
        if(empty($_GET['p']) || $_GET['p']<=1){
            $page = 1;
            $p1 = 1;
        }else{
            $page = $_GET['p'];
            $p1 = 0;
        }

        $sql1 = "SELECT * FROM xgj_ov_goods $where";
        $goods_list1 = M()->fetchAll($sql1);

        //显示列表内容
        $page1 = ($page-1)*$num;
        $sql2 = "SELECT * FROM xgj_ov_goods $where limit $page1,$num";
        $goods_list2 = M()->fetchAll($sql2);
        //分页的总条数
        $orderAll = count($goods_list1);

        $keywordss=empty($keywords)?'&k=+':"&k=$keywords";
        $bids=empty($brid)?'':"&brid=$brid";
        $cids=empty($caid)?'':"&caid=$caid";
        $pids=empty($prid)?'':"&prid=$prid";
        $cuids=empty($cuid)?'':"&cuid=$cuid";
        //实例化分页类
        $t = new Page($num, $orderAll, $page, 5, "search.php?$keywordss$bids$cids$pids$cuids&p=");
        //分页样式
        $page=$t->subPageCss2();//分页样式
        //模板传值
        if (empty($goods_list) && $p1 == 1) {
            $this->assign("page",'');
        }else{
            $this->assign("page",$page);
        }
        $keywordsss=empty($keywords)?'':"AND goods_title LIKE '%{$keywords}%'";
        $bidss=empty($brid)?'':"and n.brand_id = $brid";
        $cidss=empty($caid)?'':"and n.cate_id = $caid";

        $sql = "SELECT distinct n.brand_id id,b.name name FROM xgj_ov_goods n join xgj_ov_brand b on n.brand_id = b.id  where n.is_putaway = '1'  $keywordsss $bidss $cidss";
        $goods_title = M()->fetchAll($sql);

        $this->assign("goods_title",$goods_title);
        /*******************查询商品结果显示*********************/

        unset($map);
        $map['is_show']    = 1;
        $map['class_id']   = 2;
        $brand_list = M('xgj_ov_brand')->where($map)->order('`order` ASC')->limit("0,32")->select();
        foreach ($brand_list as $key => &$bann) {
            $bann['logo'] = getImage($bann['logo']);
        }

        //找到所有国家专区列表
        $country_list = M('xgj_eu_country')->where("is_show = '1'")->select();

        /*******************筛选数据*********************/
        //筛选数据:
        /*
        思路:
        根据输入的关键字找到很多商品
        根据商品的分类id找到顶级分类
        #根据顶级分类找到下面的品牌列表
        #根据商品的分类id找出同辈分类数据 + 当前筛选的分类的同辈分类
        #根据商品的最低价格和最高价格计算价格分段数据
        */
        $cate_list_eu = $this->getCateList('xgj_ov_category',2);
        

        //根据顶级分类找到下面的品牌列表
        $idss = array();
        foreach ($goods_list as $v) {
            $idss[] = $this->getTopCateById($cate_list_eu, $v['cate_id']);//获取顶级id
        }
        $idss = array_unique($idss);
        $in  = empty($idss)?'':"WHERE cate_id IN(".join(',',$idss).")";
        $sql = "SELECT * FROM xgj_ov_brand $in";
        $brand_list_eu = M()->fetchAll($sql);

        

        //根据商品的分类id找出同辈分类数据 + 当前筛选的分类的同辈分类
        $ids = array();
        foreach ($goods_list as $v2) {
            $ids = array_merge($ids,$this->getSiblingsCateById($v2['cate_id'],'xgj_ov_category'));
        }
        $cate_list_eu = array();
        foreach ($ids as $v3) {
            $cate_list_eu[$v3['id']] = $v3;
        }
        $ids = array_keys($cate_list_eu);
        $in  = empty($ids)?'':"AND cate_id IN(".join(',',$ids).")";

        //根据商品的最低价格和最高价格计算价格分段数据
        $sql  = "SELECT MAX(purchase) maxprice,MIN(purchase) minprice FROM xgj_ov_goods $where2";//查询出最大的价格和最小的价格
        $price_list = array();
        $price      = M()->fetch($sql);
        if($price['minprice']){
            $minprice   = floor($price['minprice']/10)*10;
            $maxprice   = ceil($price['maxprice']/10)*10;
            $split      = 3;
            $sprice     = ($maxprice-$minprice)/$split;
            for ($i=0; $i < $split; ++$i) {
                $start_price = floor($minprice/10)*10;
                if($i==$split-1){
                    $end_price = floor(($minprice+$sprice)/10)*10;
                }else{
                    $end_price = floor(($minprice+$sprice)/10)*10-1;
                }
                $price_list[] = $start_price . '-' . $end_price;
                $minprice+=$sprice;
            }
            $price_list   = array_unique($price_list);
            $price_list[] = $end_price.'以上';
        }
        
        /*******************筛选数据*********************/

        /*******************分配数据*********************/
        if(isset($_GET['test']) && C('APP_DEBUG')){
            var_dump($brand_list_eu);
            var_dump($cate_list_eu);
            var_dump($price_list);
            var_dump($goods_list);
            var_dump($hot_list);
        }
        /*查询用户关注的所有商品*/
        if(isset($_SESSION['userId'])){
            $info=M('xgj_concern')->field('goods_id')->where(array('class_id'=>"2",'user_id'=>$_SESSION['userId']))->select();
			if(!empty($info)){
					foreach ($info as $k => $v) {
						$concern[]=$v['goods_id'];
					}
					$data['concern_list']=$concern;
			}else
				 $data['concern_list']=array();
        }else{
            $data['concern_list']=array();
        }

        $data1=D('Eugroup')->category(1);
        $this->assign('eu_tree',$data1);
		$data11=D('Eugroup')->Ov_Category();
		$this->assign('ov_tree',$data11);
        $this->assign('country_list', $country_list);
        $this->assign('search_result', $search_result);
        $this->assign('concern_list', $data['concern_list']);
        $this->assign('brand_list', $brand_list);
        $this->assign('cate_list_eu', $cate_list_eu);
        $this->assign('price_list', $price_list);
        $this->assign('goods_list', $goods_list);
        $this->display('common/overseas.html');
    }
        



      /*
    获取所有的 分类
     */
    public function getCateList($table="xgj_eu_category",$class_id=1){
      return M($table)->where(array(
          'is_show'=>1,
          'class_id'=>$class_id,
        ))->order('`order`')->select();
    }

    /*
    通过分类id获取 该分类的父分类的同辈分类  用于遍历内容页的"相关分类"数据
     */
    public function getSiblingsCateById($cate_id,$table="xgj_eu_category"){
        $cate = M($table)->find($cate_id);
        $cate_siblings = M($table)->where(array('pid'=>$cate['pid']))->select();
        return $cate_siblings;
    }

    /*
    通过分类id查询出该分类下的所有子分类和当前分类的id,并拼接成IN语句的格式返回
     */
    public function implodeInByCateId($cate_id){
        $cate = $this->_getCateTree($this->getCateList(),$cate_id);
        $ids = array();
        foreach ($cate as $v) {
            $ids[] = $v['id'];
        }
        if(empty($cate)){
          return ' IN('.$cate_id.')';
        }else{
          return ' IN('.$cate_id.','.implode(',',$ids).')';
        }
    }
     /*
    获取分类的树形结构
     */
    public function _getCateTree($cate_list_eu, $pid=0){
      global $cate_arr;
      $cate_arr = array();
      return $this->__getCateTree($cate_list_eu, $pid);
    }

    public function __getCateTree($cate_list_eu, $pid=0){
      if(!is_array($cate_list_eu)) return array();

        //必须是静态的,或者全局的,递归有作用域问题
        global $cate_arr;
        foreach ($cate_list_eu as $cate) {
            //判断设置的深度
            if($cate['pid'] == $pid){
                $cate_arr[] = $cate;
                $this->__getCateTree($cate_list_eu, $cate['id']);
            }
        }
        return $cate_arr;
    }

     /*
    查询指定分类的 顶级分类
     */
    public function getTopCateById($cate_list, $cate_id){
        static $temp = '';
        foreach ($cate_list as $value) {
            if($value['id'] == $cate_id){
                if($value['pid']==0){
                    $temp = $value;
                    break;
                }
                $this->getTopCateById($cate_list, $value['pid']);
            }
        }
        if(empty($temp))return $cate_id;
        return $temp['id'];
    }


    function getTotal($goods_list){
           //获取欧元转人民币的汇率
        if(isset($_SESSION['currency'])){
            $currency=$_SESSION['currency'];
        }else{
            $_SESSION['currency']=switch_money();
            $currency=$_SESSION['currency']; 
        }
        foreach ($goods_list as $k=>$t) {
             $purchase             =$t['purchase']*$currency;//采购价
             $duties               =round($purchase*$t['duties'],2);//关税价
             $arr                  =explode('-',$t['luggage']);
             $luggage1             =$arr[0];//海运运费
             $luggage2             =$arr[1];//空运运费
             $vat1                 =round(($purchase+$duties+$luggage1)*$t['vat'],2);//增值税1
             $service_charge1      =round(($purchase+$duties+$luggage1+$vat1)*$t['service_charge'],2);//服务费1
             $vat2                 =round(($purchase+$duties+$luggage2)*$t['vat'],2);//增值税2
             $service_charge2      =round(($purchase+$duties+$luggage2+$vat2)*$t['service_charge'],2);//服务费2
             $goods_list[$k]['luggage1']        = $luggage1;
             $goods_list[$k]['luggage2']        = $luggage2;
             $goods_list[$k]['vat1']            = $vat1;
             $goods_list[$k]['vat2']            = $vat2;
             $goods_list[$k]['service_charge1'] = $service_charge1;
             $goods_list[$k]['service_charge2'] = $service_charge2;
             $goods_list[$k]['money']           = $purchase;
             $goods_list[$k]['duties1']         = $duties;
             $goods_list[$k]['sale']            = round((($t['market_price']-$t['purchase'])/$t['market_price']*100),2).'%'; 
             $goods_list[$k]['total1']          = round($purchase+$duties+$luggage1+$vat1+$service_charge1,1);
             $goods_list[$k]['total2']          = round($purchase+$duties+$luggage2+$vat2+$service_charge2,1);
        }
        return $goods_list;
    }


}