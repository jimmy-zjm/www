<?php
/**
 * 欧洲团代购模型
 * @date 2016-3-9
 * @author grass <14712905@qq.com>
 */

class EugroupModel extends GoodsModel{
    protected $class_id = 1;
    protected $cate_arr = array();
    public $tableName   = 'xgj_eu_goods_new';//该模型的表名

    public function __construct(){
        parent::__construct($this->tableName);
    }

    /*
    获取首页的所有数据
     */
    public function getIndexAll($id='',$limit='',$giveaway=''){
		
        //获取欧元转人民币的汇率
        if(isset($_SESSION['currency'])){
            $currency=$_SESSION['currency'];
        }else{
            $_SESSION['currency']=switch_money();
            $currency=$_SESSION['currency']; 
        }
        $id=empty($id)?C('EU_CATE_ID1'):$id;
        //获取欧团首页所有广告
        $time=time();
        $map3['is_on']    = 1;
        $map3['start_time']=array('lt',$time);
        $map3['end_time']=array('gt',$time);
        $map3['ad_pos_id']  = C('EU_AD_ID');
        $data['ad_list']    = M('xgj_ad')->where($map3)->select();
        foreach ($data['ad_list'] as $k=>$v){
            $data['ad_list'][$k]['image']=getImage($v['image']);
        }
        //获取所有品牌分类
        $map2['is_show']    = 1;
        $map2['class_id']   = $this->class_id;
        $data['brand_list'] = M('xgj_eu_brand')->where($map2)->order('`name` ASC')->select();
        /*分类列表 , 顶级分类,*/
        $map['is_show']    = 1;
        $map['class_id']   = $this->class_id;
        $map['pid']        = 0;
        //获取所有商品分类
        $data['cate_list'] = M('xgj_eu_category')->where($map)->order('`order` ASC')->select();
        //循环获取所有分类的子分类
        foreach ($data['cate_list'] as $k=>$v){
            $data['cate_list'][$k]['list']=$this->getCateBySon($v['id']);
            $data['cate_list'][$k]['goods']=$this->getGoodsByCateId($this->implodeInByCateId($v['id']));
                foreach ($data['cate_list'][$k]['list'] as $ke=>$va){
                    $data['cate_list'][$k]['list'][$ke]['son_list']=$this->getCateBySon($va['id']);
                }
        }
        //获取子分类下的所有商品
        $map1['pid']        = $id;
        $data['goods_cate']=M('xgj_eu_category')->where($map1)->order('`order` ASC')->select();
        foreach ($data['goods_cate'] as $k=>$v){
            $data['goods_cate'][$k]['list']=$this->getCateBySon($v['id']);
                foreach ($data['goods_cate'][$k]['list'] as $ke=>$va){
                    $data['goods_cate'][$k]['list'][$ke]['goods']=$this->getGoodsByCateId($va['id'],$limit,$giveaway);
                    foreach ($data['goods_cate'][$k]['list'][$ke]['goods'] as $key=>$val){
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['face_image']=getImage($val['face_image']);
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['sale']=round((($val['market_price']-$val['purchase'])/$val['market_price']*100),2).'%';
                         $purchase                 =$val['purchase']*$currency;//采购价
                         $duties                   =round($purchase*$val['duties'],2);//关税价
                         $arr                      =explode('-',$val['luggage']);
                         $luggage1                 =$arr[0];//海运运费
                         $luggage2                 =$arr[1];//空运运费
                         $vat1                     =round(($purchase+$duties+$luggage1)*$val['vat'],2);//增值税1
                         $service_charge1          =round(($purchase+$duties+$luggage1+$vat1)*$val['service_charge'],2);//服务费1
                         $vat2                     =round(($purchase+$duties+$luggage2)*$val['vat'],2);//增值税2
                         $service_charge2          =round(($purchase+$duties+$luggage2+$vat2)*$val['service_charge'],2);//服务费2
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['luggage1']        = $luggage1;
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['luggage2']        = $luggage2;
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['vat1']            = ($vat1);
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['vat2']            = ($vat2);
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['service_charge1'] = ($service_charge1);
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['service_charge2'] = ($service_charge2);
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['money']           = ($purchase);
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['duties1']         = ($duties);
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['total1']          = round($purchase+$duties+$luggage1+$vat1+$service_charge1,1);
                         $data['goods_cate'][$k]['list'][$ke]['goods'][$key]['total2']          = round($purchase+$duties+$luggage2+$vat2+$service_charge2,1);
                    }
                }
        }
        
        
        return $data;
    }

    /*
    获取列表的所有数据
     */
    public function getListAll(){
        $cate_id = I('id',true);
        if(empty($cate_id)) die('参数错误');
        /*面包屑数据*/
        $data['bread_list'] = $this->getBreadByCateId($this->getCateList(),$cate_id);

        /*列表页面商品数据*/
        $data['goods_list'] = $this->processGoods($this->getGoodsByCateId($this->implodeInByCateId($cate_id),'0,6'),array('image','price','comm_num','concern','brand'));

        return $data;
    }

    public function getListAll_P($page1,$num){
        $cate_id = I('id',true);
        if(empty($cate_id)) die('参数错误');

        /*列表页面商品数据*/
        $data = $this->processGoods($this->getGoodsByCateId($this->implodeInByCateId($cate_id),"$page1,$num"),array('image','price','comm_num','concern','brand'));

        return $data;
    }

    /*获取楼层分类数据*/
    public function getFoolCate($id){
        $map['is_show']  = 1;
        $map['class_id'] = $this->class_id;
        $map['id']       = $id;
        $data            = M('xgj_eu_category')->where($map)->select();
        $data            = $data[0];
        unset($map['id']);
        $map['pid']      = $id;
        $data['list']    = M('xgj_eu_category')->where($map)->order('`order` ASC')->limit('0,5')->select();
        return $data;
    }

    /**
     * 获取指定分类的商品
     * @param  mixed $id id或者id数组
     * @return Array     返回查询到的商品, 没有返回空数组
     */
    public function getGoodsByCateId($id ,$limit_='0,8',$giveaway=''){
		
        $where = "WHERE is_delete=0 AND is_putaway=1 AND is_groupbuy=0 AND class_id={$this->class_id} ";
		if(!empty($giveaway)){
             $where .= "AND  is_real={$giveaway} ";
        }
        if(empty($id)){
            $where .= ' ';
        }elseif(is_array($id)){
            $where .= "AND cate_id IN(".join(',', $id).") ";
        }else{
            $where .= "AND cate_id={$id} ";
        }
        $order = "ORDER BY addtime DESC ";
        if(!empty($limit_)){
            $limit = 'LIMIT '.$limit_;
        }else{
            $limit = '';
        }
        

        $sql = "SELECT * FROM xgj_eu_goods_new $where $order $limit";
        $data = M()->fetchAll($sql);
        // echo '<pre>';
        // var_dump($data);exit;
        return $data?$data:array();
    }

    /**
     * 获取整点抢购的商品, 未完待续..........
     */
    public function getZdqgGoods(){
        $today_zero_time  = strtotime(date('Y-m-d'));//今天的零点时间
        $tomorrow_zero_time  = strtotime(date('Y-m-d')) + 86400;//明天的零点时间
        $start_time = strtotime(date('Y-m-d')) + C('START_BUY_TIME') * 3600;//抢购的开始时间
        $where = "WHERE is_delete=0 AND is_putaway=1 AND is_groupbuy=1 AND groupbuy_start_date>={$today_zero_time} AND class_id={$this->class_id} ";
        $order = "ORDER BY addtime DESC ";
        $limit = 'LIMIT 0,4';

        $sql  = "SELECT * FROM xgj_eu_goods_new $where $order $limit";
        $data = M()->fetchAll($sql);
        return $data?$data:array();
    }

    /**
     * 获取子分类..........
     */
    public function getCateBySon($id){
         /*分类列表 , 顶级分类,限制8条*/
        $map['is_show']    = 1;
        $map['class_id']   = $this->class_id;
        $map['pid']        = $id;
        $data = M('xgj_eu_category')->where($map)->order('`order` ASC')->select();
        return $data;
    }

     /**
     * 获取品牌子分类..........
     */
    public function getBrandBySon($id){
         /*分类列表 ,*/
        $map['is_show']    = 1;
        $map['class_id']   = $this->class_id;
        $map['pid']        = $id;
        $data = M('xgj_eu_brand')->where($map)->order('`order` ASC')->select();
        return $data;
    }

    //获取所有合作品牌
    public function getCbrandAll($class_id,$limit='0,8'){
        $map['class_id']    = $class_id;
        $data = M('xgj_cbrand')->where($map)->order('`order` ASC')->limit($limit)->select();
        foreach ($data as $k=>&$v){
            $v['logo']=getImage($v['logo']);
        }
        return $data;
    }

    public function category($class_id){
        $data = M('xgj_eu_category')->where("class_id=$class_id and pid=0")->select();
        //后台三级分类 前台读取后两级分类
        $pid='';
        foreach ($data as $k=>$v){
            $pid .= ','.$v['id'];
        }
        $pid =ltrim($pid,',');
        $data1 = M('xgj_eu_category')->where("class_id=$class_id and pid in ( {$pid} )")->select();
        foreach ($data1 as $key=>$v1){
            
            $data1[$key]['list']=M('xgj_eu_category')->where("class_id=$class_id and pid={$v1['id']}")->select();
        }
        return $data1;
    }
	//海外超市分类----导航分类
	public function Ov_Category(){
       // $cate_list=$this->m->getAll("select * from xgj_ov_category where class_id=2 and pid=0 and is_show=1");
	   $cate_list=	M('xgj_ov_category')->where("class_id=2 and pid=0 and is_show=1")->select();
     
        foreach($cate_list as $k=>$v){
            $cate_list[$k]['list']=M('xgj_ov_category')->where("class_id=2 and pid={$v['id']} and is_show=1")->select();
			//$this->m->getAll("select * from xgj_ov_category where class_id=2 and pid={$v['id']} and is_show=1");
        }
        return $cate_list;
	}

}

