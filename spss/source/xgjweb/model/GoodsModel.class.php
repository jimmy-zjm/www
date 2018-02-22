<?php
/**
 * 欧团,母婴基础模型
 * @date 2016-3-23
 * @author grass <14712905@qq.com>
 */

class GoodsModel extends Model{
    protected $tableName = 'xgj_eu_goods_new';//该模型的表名
    protected $class_id  = 0;
    public $error        = '';


    public function __construct(){
        parent::__construct($this->tableName);
    }

    /*
    获取商品详情页面的所有数据
     */
    public function getDetailAll($goods_id){
        //获取所有的分类
        $this->cate_list = $this->getCateList();


        //获取商品信息, 包括商品的基本信息,关心信息,相册信息,商品属性信息
        $data['goods']     = $this->getGoods($goods_id);
        $this->class_id    = $data['goods']['class_id'];
        $data['cate_list'] = $this->getCate($data['goods']['class_id']);

        //获取相关分类信息
        $data['siblings_cate_list']  = $this->getSiblingsCateById($data['goods']['cate_id']);

        //获取其他同类品牌信息
        $data['siblings_brand_list'] = $this->getSiblingsBrandByCateId($data['goods']['cate_id']);

        //获取评论列表, 修改为ajax请求, 所以注释下行代码
        // $data['comment_list'] = $this->getCommentList($goods_id,'0,10');

        //ajax获取评论列表的url地址
        //ajax.php?getCommentList&id=4

        //商品介绍的属性数据
        $arr_before = array(
            '商品名称' => $data['goods']['goods_title'],
            '商品编号' => $data['goods']['goods_sn'],
            '品牌'     => '<a href="javascript:;">'.$data['goods']['brand_name'].'</a>',
        );
        $arr_after = array(
            '上架时间' => $data['goods']['addtime'],
        );
        $data['goods']['attr_list_desc']=array_merge($arr_before, $data['goods']['attr_list_rad'], $arr_after);

        //获取热卖榜数据
        $data['hot_list'] = $this->getHotGoods();
        // var_dump($data['hot_list']);

        return $data;
    }
    

    /*
    分类列表 , 顶级分类,限制8条
    */
    public function getCate($class_id ,$pid=0){
        $map['is_show']  = 1;
        $map['class_id'] = $class_id;
        $map['pid']      = $pid;
        $cate_list = M('xgj_eu_category')->where($map)->order('`order` ASC')->limit('0,8')->select();
        return $cate_list;
    }

    /*
    获取热卖ban榜数据, 限制6条数据
     */
    public function getHotGoods($length=6, $search_all=false){
        if(!$search_all){
            $class_where = "class_id='{$this->class_id}'";
        }
        $sql = "SELECT goods_id FROM xgj_eu_order_goods WHERE $class_where GROUP BY goods_id ORDER BY SUM(goods_num) DESC LIMIT 0,{$length}";
        $temp = M()->fetchAll($sql);
        $ids  = array_map(function($v){return $v['goods_id'];}, $temp);
        $ids  = join(',',$ids);
        if(!empty($ids)){
            $sql = "SELECT * FROM xgj_eu_goods_new WHERE id IN({$ids}) ORDER BY FIELD(id,{$ids})";
            $data = M()->fetchAll($sql);
            $data = $this->processGoods($data);
        }else{
            $data = array();
        }
        return $data;
    }

    /*
    处理商品数据
     */
    public function processGoods($arr ,$option = array('image','price','comm_num','concern')){
        //var_dump($arr[0]['purchase']);exit;
        //var_dump($arr);exit;
        foreach ($arr as $k=>&$t) {
            if(in_array('image',$option)) $t['image']        = getImage($t['face_image']);//图片
            if(in_array('price',$option)) $t['price']        = $this->getPrice($t['id']);//价格
            if(in_array('comm_num',$option)) $t['comm_num']  = $this->getCommentNum($t['id']);//评论人数
            if(in_array('concern',$option)) $t['is_concern'] = $this->isConcern($t['id']);//是否已经关注
            if(in_array('brand',$option)) $t['brand_name']   = M('xgj_eu_brand')->where(array('id'=>$t['brand_id']))->getField('name');
        }
        
        return $arr;
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

    /*
    是否已经关注, 返回 1或者0
     */
    public function isConcern($goods_id){
        if($user_id = session('userId')){
            $map['user_id']   = $user_id;
            $map['goods_id']  = $goods_id;
            $concern_count    = M('xgj_concern')->where($map)->count();
            $concern = $concern_count>0?'1':'0';
        }else{
            $concern = '0';
        }
        return $concern;
    }

    /**
     * 获取评论的数量
     */
    public function getCommentNum($goods_id, $user_id=''){
        $where = '';
        if(!empty($user_id)){
            $where = "AND user_id='{$user_id}'";
        }
        $sql = "SELECT COUNT(*) AS total FROM xgj_eu_comment WHERE goods_id='{$goods_id}' $where";
        $total = M()->fetchColumn($sql,'total');
        return $total?$total:0;
    }

    /*
    获取商品的评价信息
     */
    public function getCommentList($goods_id,$limit='0,10'){
        //获取商品评论数据
        $sql = "SELECT c.*,og.goods_attr FROM xgj_eu_comment AS c LEFT JOIN xgj_eu_order_goods AS og ON c.order_goods_id=og.id WHERE c.goods_id={$goods_id} LIMIT $limit";
        $comm_list = M()->fetchAll($sql);

        // $map['goods_id'] = $goods_id;
        // $comm_list = M('xgj_eu_comment')->where($map)->limit($limit)->select();
        foreach ($comm_list as $key => &$v) {
            if (!empty($v['images'])){
                $v['images'] = explode('|', $v['images']);
                for ($i=0; $i <count($v['images']) ; $i++) { 
                    $v['images'][$i] = getImage($v['images'][$i]);
                }
            } 
            $v['imagesNum']  = count($v['images']);
            $v['addtime']    = date('Y.m.d H:i',$v['add_time']);
            $v['user_name']  = processName($v['user_name']);
        }
        return $comm_list;
    }

    public function getEuCommentList($goods_id){
        $comment_list = M('xgj_eu_comment')->field('star')->where("goods_id=$goods_id")->select();
        $data = 0;
        if (!empty($comment_list)) {
            foreach ($comment_list as $key => $v) {
                $data+=$v['star'];
            }
            $data = ceil($data/count($comment_list));
        }
        return $data;
    }


    /*
    获取"商品的同类品牌"
     */
    public function getSiblingsBrandByCateId($cate_id){
        $cate_id = $this->getTopCateById($this->cate_list, $cate_id);//获取顶级id
        $map['cate_id'] = $cate_id;
        $map['is_show'] = 1;
        $brand_list = M('xgj_eu_brand')->where($map)->select();
        return $brand_list;
    }

    /*
    通过分类id获取 该分类的父分类的同辈分类  用于遍历内容页的"相关分类"数据
     */
    public function getSiblingsCateById($cate_id){
        $cate = M('xgj_eu_category')->find($cate_id);
        $cate_siblings = M('xgj_eu_category')->where(array('pid'=>$cate['pid']))->select();
        return $cate_siblings;
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

    /**
     * 获取商品数据
     * @return Array 返回商品信息
     */
    public function getGoods($goods_id){
         //获取欧元转人民币的汇率
        if(isset($_SESSION['currency'])){
            $currency=$_SESSION['currency'];
        }else{
            $_SESSION['currency']=switch_money();
            $currency=$_SESSION['currency']; 
        }
        //商品信息, 包括商品基本信息, 商品品牌信息
        $sql   = "SELECT g.*,b.name AS brand_name,b.logo AS brand_logo,b.description AS brand_desc FROM xgj_eu_goods_new AS g LEFT JOIN xgj_eu_brand AS b ON g.brand_id=b.id WHERE g.id={$goods_id}";
        $goods = M()->fetch($sql);
        if(empty($goods)){
            $this->error = '商品不存在';
            return false;
        }

        //计算商品的基本价格信息
        $goods['price']           = $this->getPrice($goods['id']);
        $goods['addtime']         = date('Y-m-d H:i:s', $goods['addtime']);
        $goods['jq_image']        = getImage($goods['face_image']);
        $purchase                 = ($goods['purchase']*$currency);//采购价
        $duties                   = round($purchase*$goods['duties'],2);//关税价
        $arr                      = explode('-',$goods['luggage']);
        $luggage1                 = ($arr[0]);//海运运费
        $luggage2                 = ($arr[1]);//空运运费
        $vat1                     = round(($purchase+$duties+$luggage1)*$goods['vat'],2);//增值税1
        $service_charge1          = round(($purchase+$duties+$luggage1+$vat1)*$goods['service_charge'],2);//服务费1
        $vat2                     = round(($purchase+$duties+$luggage2)*$goods['vat'],2);//增值税2
        $service_charge2          = round(($purchase+$duties+$luggage2+$vat2)*$goods['service_charge'],2);//服务费2
        $goods['luggage1']        = ($luggage1);
        $goods['luggage2']        = ($luggage2);
        $goods['vat1']            = ($vat1);
        $goods['vat2']            = ($vat2);
        $goods['service_charge1'] = ($service_charge1);
        $goods['service_charge2'] = ($service_charge2);
        $goods['money']           = ($purchase);
        $goods['duties1']         = ($duties);
        $goods['total1']          = round($purchase+$duties+$luggage1+$vat1+$service_charge1,1);
        $goods['total2']          = round($purchase+$duties+$luggage2+$vat2+$service_charge2,1);
        //$goods['video_image']   = getImage($goods['video_image']);
        $goods['src_image']       = getImage($goods['face_image'],350,350);
        $goods['brand_logo']      = getImage($goods['brand_logo']);

        //商品的关注信息
        if($user_id = session('userId')){
            $concern_count    = M('xgj_concern')->where(array('user_id'=>$user_id,'goods_id'=>$goods['id']))->count();
            $goods['concern'] = $concern_count>0?'1':'0';
        }else{
            $goods['concern'] = '0';
        }


        //商品相册信息
        $map['goods_id'] = $goods_id;
        $map['is_show'] = 1;
        $images = M('xgj_eu_image')->field('url')->where($map)->select();
		if(!empty($images)){
			foreach ($images as $key => $img) {
				$goods['images'][] = array(
					'jq'  => getImage($img['url']),
					'src' => getImage($img['url'],350,350),
				);
			}
        }else{
			$goods['images'][] = array(
					'jq'  => '',
					'src' => '',
				);
		}

        //商品视频册信息
        $map1['goods_id'] = $goods_id;
        $map1['is_show'] = 1;
        $images1 = M('xgj_eu_video')->where($map1)->select();
        foreach ($images1 as $key => $img) {
            $goods['video'][] = array(
                'video'  => getImage($img['url']),
                'title'  =>$img['title'],
            );
        }

        //商品的属性信息
        $sql = "SELECT ga.*,a.name AS attr_name,a.mode FROM xgj_eu_goods_attr AS ga LEFT JOIN xgj_eu_attribute AS a ON ga.attr_id=a.id WHERE ga.goods_id={$goods_id} ORDER BY ga.id";
        $attr_list = $attr_list_pro = $attr_list_rad = array();
        $attr_list = M()->fetchAll($sql);
        foreach ($attr_list as $attr) {
            if($attr['mode']==1){
                $attr_list_pro[$attr['attr_name']][] = $attr;//多选属性
            }else{
                $attr_list_rad[$attr['attr_name']] = $attr['attr_value'];//单选属性
            }
        }


        $goods['attr_list'] = $attr_list_pro;//多选属性
        $goods['attr_list_rad'] = $attr_list_rad;//单选属性

        return $goods;
    }

    /*
    获取商品价格
     */
    public function getPrice($id){
        $goods = M('xgj_eu_goods_new')->find($id);
        if($goods['is_groupbuy']==1){
            return $goods['groupbuy_price'];
        }else{
            return $goods['shop_price'];
        }
    }



    /*
    通过分类id获取类似"面包屑"的数据
     */
    public function getBreadByCateId($cate_list, $cate_id){
        if(empty($cate_list)) return array();
        static $bread_arr = array();
        foreach ($cate_list as $cate) {
            if($cate_id == $cate['id']){
                $bread_arr[] = $cate;
                $this->getBreadByCateId($cate_list, $cate['pid']);
            }
        }
        return array_reverse($bread_arr);
    }


    public function implodeInByCateId($cate_id){
        $this->cate_arr = array();
        $cate = $this->_getCateTree($this->getCateList(),$cate_id);
        $ids  = array();
        foreach ($cate as $v) {
            $ids[] = $v['id'];
        }
        $ids[] = $cate_id;
        return $ids;
    }

    public function implodeInByCateId2($cate_id){
        $this->cate_arr = array();
        $cate = $this->_getCateTree($this->getCateList(),$cate_id);
        $ids  = array();
        foreach ($cate as $v) {
            $ids[] = $v['id'];
        }
        if(empty($cate)){
          return ' IN('.$cate_id.')';
        }else{
          return ' IN('.$cate_id.','.implode(',',$ids).')';
        }
    }

    public function _getCateTree($cate_list, $pid=0, $deep=0, $deep_limit=-1){
      if(!is_array($cate_list)) return array();
        //必须是静态的,或者全局的,递归有作用域问题
        foreach ($cate_list as $cate) {
            //判断设置的深度
            if($deep_limit!=-1 and $deep_limit < $deep) break;
            if($cate['pid'] == $pid){
                $cate['deep']     = $deep;
                $this->cate_arr[] = $cate;
                $this->_getCateTree($cate_list, $cate['id'], $deep+1, $deep_limit);
            }
        }
        return $this->cate_arr;
    }

    /*
    获取所有的 分类
     */
    public function getCateList(){
      return M('xgj_eu_category')->where(array(
          'is_show'=>1,
          'class_id'=>$this->class_id,
        ))->order('`order`')->select();
    }

    public function setDetailClickCount($goods_id){
        $count = M('xgj_eu_goods_new')->where('id='.$goods_id)->find();
        $num = $count['click_count']+1;
        $count = M('xgj_eu_goods_new')->where('id='.$goods_id)->setField('click_count',$num);
        return $count;
    }

    // /**
    //  * getComment
    //  */
    // public function getComment(){
    //    return M('xgj_eu_comment')->where(array(
    //       'is_show'=>1,
    //       'class_id'=>$this->class_id,
    //     ))->order('`order`')->select(); 
    // }
}

