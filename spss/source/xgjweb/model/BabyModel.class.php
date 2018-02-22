<?php
/**
 * 欧洲母婴模型
 * @date 2016-22
 * @author grass <14712905@qq.com>
 */
class BabyModel extends GoodsModel{
    protected $class_id = 2;
    protected $cate_arr = array();
    public $tableName   = 'xgj_ov_goods';//该模型的表名

    public function __construct(){
        parent::__construct($this->tableName);
    }

    /*
    获取首页的所有数据
     */
    public function getIndexAll(){

        /*分类列表 , 顶级分类,*/
        $map['is_show']    = 1;
        $map['class_id']   = $this->class_id;
        $map['pid']        = 0;
        $data['cate_list'] = M('xgj_ov_category')->where($map)->order('`order` ASC')->select();
        /*查询分类下推荐的三条数据*/
     
        foreach($data['cate_list'] as $k=>$v){
            $data['cate_list'][$k]['list']=M('xgj_ov_category')->field('id,pid,name')->where("pid={$v['id']}")->order('`order` ASC')->select();
            foreach($data['cate_list'][$k]['list'] as $ke=>$va){
                $data['cate_list'][$k]['list'][$ke]['goods']=M('xgj_ov_goods')->field('id,goods_title,market_price,is_hot,purchase,face_image')->where("cate_id = $va[id] and is_rec=1 and is_delete=0 AND is_putaway=1")->order('`id` DESC')->limit('0,5')->select();
            }
         
        }
		//var_dump('<pre>', $data['cate_list']);
        /*查询用户关注的所有商品*/
        if(isset($_SESSION['userId'])){
            $info=M('xgj_concern')->field('goods_id')->where(array('class_id'=>"3",'user_id'=>$_SESSION['userId']))->select();
            if(!empty($info)){
                foreach ($info as $k => $v) {
                    $concern[]=$v['goods_id'];
                }
            }else{
                $concern=array();
            }
            
            $data['concern_list']=$concern;
        }else{
            $data['concern_list']=array();
        }
        /*banner广告图片列表*/
        unset($map);
        $map['is_on']      = 1;
        $map['ad_pos_id']  = C('BABY_INDEX_BANNER_ADPOS_ID');
        $data['bann_list'] = M('xgj_ad')->where($map)->find();
        $data['bann_list']['image'] = getImage($data['bann_list']['image']);
        /*查询海外超市国家专区*/
        unset($map);
        $map['is_show']      = 1;
        $data['country_list'] = M('xgj_eu_country')->where($map)->limit('0,10')->select();
        foreach ($data['country_list'] as $key => &$bann) {
            $bann['image'] = getImage($bann['image']);
        }
        return $data;
    }

    /*
    获取首页的所有数据
     */
    public function getListAll(){
        /**分类id接收值    查询**/
        $id = I('get.id');
        if(empty($id)){
            die('参数错误');
        }else{
            $arr=explode(',', $id);
            $pid=$arr[0];
            $id=$arr[1];
        }
        /***拼凑条件**/
        if($id==0){
            $map['is_show']    = 1;
            $map['class_id']   = $this->class_id;
            $map['pid']        = $pid;
            $cate = M('xgj_ov_category')->where($map)->order('`order` ASC')->select();
            $ids='';
            foreach ($cate as $k => $v) {
                $ids.=$v['id'].',';
            }
            $ids=rtrim($ids,',');
            $where=" cate_id in ($ids) and is_delete=0 AND is_putaway=1 and class_id=2 ";
            $where2=" cate_id in ($ids) and is_delete=0 AND is_putaway=1 and class_id=2 ";
        }else{
            $where=" cate_id=$id and is_delete=0 AND is_putaway=1 and class_id=2 ";
            $where2=" cate_id=$id and is_delete=0 AND is_putaway=1 and class_id=2 ";
        }
        /**更多查询接收值*/
        $b_id=I('get.brand_id');//品牌查询
        $s_price=I('get.price');//价格查询
        $val=I('get.val');//属性查询
        if(!empty($b_id)){
             $where .= " AND brand_id = '$b_id' ";
             $where2 .= " AND brand_id = '$b_id' ";
        }
        //var_dump($where);die;
        if(!empty($s_price)){
            if(strpos($s_price, '-')!==false){
                $temp        = explode('-', $s_price);
                $start_price = $temp[0];
                $end_price   = $temp[1];
            }else{
                if(preg_match('/^\d+/',$s_price,$start_price_a)){
                    $start_price = $start_price_a[0];
                }else{
                    $start_price = 0;
                }
            }
 
            if(isset($end_price)){
                $where .= " AND purchase >= $start_price AND purchase <= $end_price ";                
            }else{
                $where .= " AND purchase >= $start_price ";                
            }
        }
        $att=array(); 
        $goods_ids='';
        if(!empty($val)){
            if(strpos($val, '|')!==false){
                $temp        = explode('|', $val);
                foreach ($temp as $k => $v) {
                    $aa[]=M()->fetchAll("SELECT * FROM xgj_ov_goods_attr where attr_value = '$v' ");
                }
                foreach($aa as $k=>$v){
                    foreach ($v as $ke => $va) {
                        if($v[0]['goods_id']==$va['goods_id']){
                            $res[]=$va;
                        }
                    }

                }
            }else{
                $res=M()->fetchAll("SELECT * FROM xgj_ov_goods_attr where attr_value = '{$val}'");
            }
            foreach ($res as $k => $v) {
                $att[]=$v['attr_id'];
                $goods_ids.=$v['goods_id'].',';
            }
            $goods_ids=" and id in (".rtrim($goods_ids,',').") ";
        } 

        /**拼凑排序**/
        $order='order by is_rec asc, is_hot asc, is_new asc';
        if(isset($_GET['sk']) && $_GET['sk']=='zong'){
            $order=' order by id asc';
        }
        if(isset($_GET['sk']) && $_GET['sk']=='xiao'){
            $order=' order by  click_count asc';
        }
        if(isset($_GET['sk']) && $_GET['sk']=='jia'){
            $order=' order by  purchase asc';
        }
        if(isset($_GET['sk']) && $_GET['sk']=='ping'){
            $order=' order by  id asc';
        }
        if(isset($_GET['sk']) && $_GET['sk']=='new'){
            $order=' order by  addtime asc';
        }

        /*分类列表 , 顶级分类,*/
        $map['is_show']    = 1;
        $map['class_id']   = $this->class_id;
        $map['pid']        = 0;
        $data['cate_list'] = M('xgj_ov_category')->where($map)->order('`order` ASC')->select();
        foreach($data['cate_list'] as $k=>$v){
            $data['cate_list'][$k]['list']=M('xgj_ov_category')->where("pid={$v['id']}")->order('`order` ASC')->select();
        }
        /*品牌列表*/
        $data['brand_list']=$this->getBrandList($pid);
        /*分类下的商品*/
        $data['goods_list']=M()->fetchAll("SELECT * FROM xgj_ov_goods where $where $goods_ids $order");

        /***搜索条件***/

        /**分类搜索*/
        $map['is_show']    = 1;
        $map['class_id']   = $this->class_id;
        $map['pid']        = $pid;
        $data['list_cate'] =M('xgj_ov_category')->where($map)->order('`order` ASC')->select();


        //根据商品的最低价格和最高价格计算价格分段数据
        $sql        = "SELECT MAX(purchase) maxprice,MIN(purchase) minprice FROM xgj_ov_goods where $where2 $goods_ids";//查询出最大的价格和最小的价格
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
                    $end_price = abs(floor(($minprice+$sprice)/10)*10-1);
                }
                $price_list[] = $start_price . '-' . $end_price;
                $minprice+=$sprice;
            }
            $price_list   = array_unique($price_list);
            $price_list[] = $end_price.'以上';
        }
        $data['price_list']=$price_list;
        //更多条件查询
        if($id==0){
            $type_id      = M('xgj_ov_category')->where("id=$pid")->getField('type_id');
        }else{
            $type_id      = M('xgj_ov_category')->where("id=$id")->getField('type_id');
        }
        

        $sql= "SELECT id,name,value_list FROM xgj_ov_attribute where type_id=$type_id and is_screen=2";
        $order_list     = M()->fetchAll($sql);
        if(!empty($order_list)){
            foreach ($order_list as $k => &$v) {
                if(empty($val)){
                    $aa=str_replace("，",",",$v['value_list']);
                    $data['order_list'][$k]['name']=$v['name'];
                    $data['order_list'][$k]['value_list']=explode(',',$aa);
                }elseif(!empty($att) && !in_array($v['id'],$att)){
                    $aa=str_replace("，",",",$v['value_list']);
                    $data['order_list'][$k]['name']=$v['name'];
                    $data['order_list'][$k]['value_list']=explode(',',$aa);
                }else{
                    $data['order_list'][$k]['name']='';
                    $data['order_list'][$k]['value_list'] = '';
                }
            }
        }else{
            $data['order_list'][$k]['name']='';
            $data['order_list'][$k]['value_list'] = ''; 
        }
        /*查询用户关注的所有商品*/
        if(isset($_SESSION['userId'])){
            $info=M('xgj_concern')->field('goods_id')->where(array('class_id'=>"3",'user_id'=>$_SESSION['userId']))->select();
            if(!empty($info)){
                foreach ($info as $k => $v) {
                    $concern[]=$v['goods_id'];
                }
            }else{
                $concern=array();
            }
            
            $data['concern_list']=$concern;
        }else{
            $data['concern_list']=array();
        }
        /******************分页*************/
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

        //显示列表内容
        $page1 = ($page-1)*$num;
        $sql2 = "SELECT * FROM xgj_ov_goods where $where $goods_ids $order  limit $page1,$num";
        $data['goods_list2'] = M()->fetchAll($sql2);

		//品牌查询开始
		$barndgoods=M('xgj_ov_goods')->field('id,brand_id')->where(array('cate_id'=>$id))->select();
	
		$brand=array('1,2,3');
		foreach( $barndgoods as $key=>$vl){
			$brand[$key]=$vl['brand_id'];
		}
		$wherebrand['id']=array('in',array_unique($brand));
		$data['brand_list']=M('xgj_ov_brand')->where($wherebrand)->select();
		//品牌查询结束


        //分页的总条数
        $orderAll = count($data['goods_list']);
        if(isset($_GET['sk'])){
            $sk='&sk='.$_GET['sk'];
        }else{
            $sk='';
        }

        //实例化分页类
        $t = new Page($num, $orderAll, $page, 5, "baby.php?list&id=$pid,$id&price=$s_price&brand_id=$b_id&val=$val$sk&p=");
        //分页样式
        $page=$t->subPageCss2();//分页样式
        //模板传值
        if (empty($data['goods_list']) && $p1 == 1) {
            $data["page"]='';
        }else{
            $data["page"]=$page;
        }
        return $data;
    }

    /*
    获取商品详情页面的所有数据
     */
    public function getDetailAll($goods_id){
        /*分类列表 , 顶级分类,*/
        $map['is_show']    = 1;
        $map['class_id']   = $this->class_id;
        $map['pid']        = 0;
        $data['cate_list'] = M('xgj_ov_category')->where($map)->order('`order` ASC')->select();
        foreach($data['cate_list'] as $k=>$v){
            $data['cate_list'][$k]['list']=M('xgj_ov_category')->where("pid={$v['id']}")->order('`order` ASC')->select();
        }
        /*查询用户关注的所有商品*/
        if(isset($_SESSION['userId'])){
            $info=M('xgj_concern')->field('goods_id')->where(array('class_id'=>"3",'user_id'=>$_SESSION['userId']))->select();
            if(!empty($info)){
                foreach ($info as $k => $v) {
                    $concern[]=$v['goods_id'];
                }
            }else{
                $concern=array();
            }
            
            $data['concern_list']=$concern;
        }else{
            $data['concern_list']=array();
        }
        /**商品信息*/
        $goods=$this->getGoods($goods_id);
        $data['goods_info']=$goods['info'];
        $data['goods_image']=$goods['image'];

        
        $comment_list = M('xgj_ov_comment')->field('star')->where("goods_id=$goods_id")->select();
        $data['comment'] = 0;
        if (!empty($comment_list)) {
            foreach ($comment_list as $key => $v) {
                $data['comment']+=$v['star'];
            }
            $data['comment'] = ceil($data['comment']/count($comment_list));
        }
    
        return $data;
    }

    public function getGoods($goods_id){
        $goods['info']=M('xgj_ov_goods')->where("id=$goods_id and is_delete=0 AND is_putaway=1 and class_id=2")->find();
		$data=M('xgj_eu_country')->field('name')->find($goods['info']['country_id']);
		$goods['info']['country']=$data['name'];
		
        $goods['image']=M('xgj_ov_image')->where("goods_id=$goods_id  and class_id=2")->select();
        return $goods;
    }

    /*
    获取楼层商品数据
     */
    public function getFoolGoods($cate_id){
        $map['class_id']   = $this->class_id;
        $map['cate_id']    = array('in',$this->implodeInByCateId($cate_id));
        $arr = M('xgj_eu_goods_new')->where($map)->order('addtime DESC')->limit('0,8')->select();
        $arr = $this->processGoods($arr);
        return $arr;
    }

    /**
     * 获取指定分类的商品
     * @param  mixed $id id或者id数组
     * @return Array     返回查询到的商品, 没有返回空数组
     */
    public function getGoodsByCateId($id ,$limit_='0,8'){
        $where = "WHERE is_delete=0 AND is_putaway=1 AND is_groupbuy=0 AND class_id={$this->class_id} ";
        if(empty($id)){
            $where .= ' ';
        }elseif(is_array($id)){
            $where .= "AND cate_id IN(".join(',', $id).") ";
        }else{
            $where .= "AND cate_id={$id} ";
        }
        $order = "ORDER BY addtime DESC ";
        $limit = 'LIMIT '.$limit_;

        $sql = "SELECT * FROM xgj_eu_goods_new $where $order $limit";
        $data = M()->fetchAll($sql);
        return $data?$data:array();
    }

    /*获取品牌列表*/
    public function getBrandList($pid){
        unset($map);
        $map['is_show']    = 1;
        $map['class_id']   = $this->class_id;
        $map['cate_id']    = $pid;
        $data = M('xgj_ov_brand')->where($map)->order('`order` ASC')->limit("0,32")->select();
        foreach ($data as $key => &$bann) {
            $bann['logo'] = getImage($bann['logo']);
        }
        return $data;
    }
    /*
    获取商品的评价信息
     */
    public function getCommentList($goods_id,$limit='0,10'){
        //获取商品评论数据
        $sql = "SELECT c.*,og.goods_attr FROM xgj_ov_comment AS c LEFT JOIN xgj_ov_order_goods AS og ON c.order_goods_id=og.id WHERE c.goods_id={$goods_id} LIMIT $limit";
        $comm_list = M()->fetchAll($sql);

        foreach ($comm_list as $key => &$v) {
            if (!empty($v['images'])){
                $v['images'] = explode('|', $v['images']);
                for ($i=0; $i <count($v['images']) ; $i++) { 
                    $v['images'][$i] = getImage($v['images'][$i]);
                }
            } 
            $v['addtime']    = date('Y.m.d H:i',$v['add_time']);
            $v['user_name']  = processName($v['user_name']);
            $v['imagesNum']  = count($v['images']);
        }
        return $comm_list;
    }

}