<?php
/**
 * 购物车类
 * @date 2016-3-9
 * @author grass <14712905@qq.com>
 */

class CartModel extends Model{
    private $tableName = 'xgj_eu_cart';//该模型的表名

    public function __construct(){
        parent::__construct($this->tableName);
    }

    /**
     * 添加商品到购物车  ,支持未登陆和已登陆
     * @param integer $id 商品id
     * @param integer $num 商品数量
     * @param String $attr_id 商品属性id, 多个用逗号(,)隔开
     */
    public function addCart(){
        $goods_id = I('id');
        $num      = I('amount');
        $express  = I('luggage');
		$give=I('giveaway');
		$giveaway=!empty($give)?$give:'0';
		
        $attr_id='';
        $user_id  = session('userId');
        if(empty($user_id)){
            //没有登陆的状态
            $key = $goods_id;
            if(isset($_SESSION['cart'][$key])){
                //已经存在相同的商品信息
                $_SESSION['cart'][$key] += $num;
            }else{
                $_SESSION['cart'][$key] = $num;
            }
            return true;
        }else{
            //已经登陆的状态
            $data = array(
                'user_id'       => $user_id,
                'goods_id'      => $goods_id,
                'goods_num'     => $num,
                'express'       => $express,
            );
         
            // echo $_SESSION['zp'];exit;
            if (!empty($giveaway) && $giveaway=='1') {
                $tname = 'xgj_eu_carts';
            }else{
                $tname = 'xgj_eu_cart';
            }
            $sql = "SELECT * FROM {$tname} WHERE user_id={$user_id} AND goods_id={$goods_id}";

            if($this->fetch($sql)){
                //已经存在相同的商品
				  if (!empty($giveaway) && $giveaway=='1') {
						return M('xgj_eu_carts')->where(array('goods_id'=>$goods_id))->setInc('goods_num', $num);
					
				  }else{
					   return $this->setIncCart($goods_id, $attr_id, $num);
				  }
              
            }else{
                if (!empty($giveaway) && $giveaway=='1') {

                    //$sql="INSERT INTO xgj_eu_carts VALUES('','{$user_id}','{$goods_id}','{$num}','{$attr_id}','')";
                    return M('xgj_eu_carts')->data($data)->add();
                }else{
                    return $this->data($data)->add();
                }
            }
        }
    }


    /**
     * 将本地购物车数据保存到数据库
     * 执行本方法之前必须登陆
     */
    public function moveToDB(){
        if(empty($_SESSION['cart'])) return;
        $user_id = $_SESSION['userId'];
        $cart    = $_SESSION['cart'];
        foreach ($cart as $k=>$v) {
            $tmp  = explode('-', $k);
            $data = array(
                    'goods_id'      => $tmp[0],
                    'goods_attr_id' => $tmp[1],
                    'goods_num'     => $v,
                    'user_id'       => $user_id,
            );
            $goods_id = $tmp[0];
            $attr_id  = $tmp[1];

            $sql = "SELECT * FROM xgj_eu_cart WHERE user_id={$user_id} AND goods_id={$goods_id} AND goods_attr_id='{$attr_id}'";
            if($this->fetch($sql)){
                //已经存在相同的商品
                $this->setIncCart($goods_id, $attr_id, $v);
            }else{
                $this->data($data)->add();
            }
        }
    }

    /**
     * 从购物车中删除商品 ,支持未登陆和已登陆, 支持单个删除和批量删除
     * @param  mixed $id 购物车id
     * @return [type]     [description]
     */
    public function delCart($cart_id,$type=''){
        if(is_array($cart_id)){
            $id = $cart_id;
        }else{
            $id[] = $cart_id;
        }
        $user_id = session('userId');
        if(empty($user_id)){
            if (empty($type)) {
                foreach ($id as $key => $v) {
                    if(isset($_SESSION['cart'][$v])){
                        unset($_SESSION['cart'][$v]);
                    }else{
                        $this->error = '指定删除的购物车商品不存在';
                        return false;exit;
                    }
                }
            }else{
                foreach ($id as $key => $v) {
                    if(isset($_SESSION['carts'][$v])){
                        unset($_SESSION['carts'][$v]);
                    }else{
                        $this->error = '指定删除的购物车商品不存在';
                        return false;exit;
                    }
                }
            }
            return true;
        }else{
            if (empty($type)) {
                foreach ($id as $key => $v) {
                    if(!$this->delete(intval($v))){
                        $this->error = '指定删除的购物车商品不存在';
                        return false;exit;
                    }
                }
            }else{
                foreach ($id as $key => $v) {
                    $sql = "DELETE FROM xgj_eu_carts WHERE id = '{$v}' and user_id = '{$user_id}'";
                    $re = $this->exec($sql);
                    if(empty($re)){
                        $this->error = '指定删除的购物车商品不存在';
                        return false;exit;
                    }
                }
            }
            return true;
        }
    }


    /*
    获取购物车中所有商品 ,支持未登陆和已登陆
     */
    public function getAll(){
        $user_id = session('userId');
        if(empty($user_id)){
            
            //没有登陆的情况
            $cart = isset($_SESSION['cart'])?$_SESSION['cart']:array();
            $data = array();

            foreach ($cart as $k=>$v) {
                $tmp = explode('-', $k);
                $data[] = array(
                    'goods_id'      => $tmp[0],
                    'goods_attr_id' => $tmp[1],
                    'goods_num'     => $v,
                    'user_id'       => 0,
                    'id'            => $k,
                );

            }
        }else{
            //已经登陆的情况
            // echo $zp;exit;
            $sql = "SELECT * FROM xgj_eu_cart WHERE user_id='{$user_id}'";
            $data = $this->fetchAll($sql);
        }

        //获取所有的商品 因为要兼容未登陆情况, 所有下行的代码作废
        //$sql = "SELECT c.*,g.goods_title,g.shop_price,g.groupbuy_price,g.face_image,g.is_groupbuy FROM xgj_eu_cart AS c LEFT JOIN xgj_eu_goods_new AS g ON c.goods_id=g.id WHERE user_id={$user_id} ORDER BY c.id DESC";
        //$data = $this->fetchAll($sql);

        //查询出商品的属性信息, 计算总价
        $total_price = 0;
        $total_num = 0;
        $discount = 0;
        $goods_list  = array();
        foreach ($data as $key => $cart) {
            $value = $this->field('*')->table('xgj_eu_goods_new')->find($cart['goods_id']);

            //购物车中的商品信息不存在时, 自动删除该记录
            if($value===false) {
                M('xgj_eu_cart')->delete($cart['id']);
                continue;
            }
            if (!empty($cart['coupon'])) {
                $value['coupon'] = $cart['coupon'];
            }else{
                $value['coupon'] = '0';
            }
            
            $value['goods_num'] = $cart['goods_num'];
            $value['goods_id']  = $cart['goods_id'];
            $value['attr_id']   = $cart['goods_attr_id'];
            $value['id']        = $cart['id'];
            $value['express']   = $cart['express'];

            $value['image']     = getImage($value['face_image'],100,100);
            //开启了团购就是用团购价, 没有开启就是本店价格
            if($value['is_groupbuy']==1){
                $value['price'] = $value['groupbuy_price'];
            }else{
                $value['price'] = $value['shop_price'];
            }

            //处理属性: 拼接属性字符创, 累加属性价格
            $value['attr_str'] = '';
            if(!empty($cart['goods_attr_id'])){
                $sql = "SELECT * FROM xgj_eu_goods_attr WHERE id IN ({$cart['goods_attr_id']})";
                $attr_list = $this->fetchAll($sql);
                $attr_str = '';
                foreach ($attr_list as $v) {
				
                    //属性字符串
                    $attr_str .= $v['attr_value'] . '<br/>';

                    //商品属性价格大于0 就累加
                    if($v['attr_price']>0){
                        $value['price'] += $v['attr_price'];
                    }
                }
                $value['attr_str'] = $attr_str;
            }

            //计算单个商品的总价
            $value['total_price'] = number_format($cart['goods_num']*$value['price'],2,'.','');
          
            //优惠价
            $discount += number_format($cart['goods_num']*($value['market_price']-$value['price']),2,'.','');
			
            //计算全部的总价
            $total_price += $value['total_price'];
            //计算全部的数量
            
			$total_num += $cart['goods_num'];
            $goods_list[] = $value;
        }

        /*猜你喜欢*/
        $like_list = array();
        if(!empty($goods_list)){
            $cate_id = $goods_list[0]['cate_id'];
            $map['cate_id'] = $cate_id;
            $like_list = M('xgj_eu_goods_new')->where($map)->limit('0,3')->select();
            $like_list = D('Goods')->processGoods($like_list);
        }
        return array(
            'cart_list'   => $goods_list,
            'total_price' => $total_price,
            'total_num'   => $total_num,
            'discount'    => $discount,
            'like_list'   => $like_list,
            );
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
    查询购物车的总数量
     */
    public function getTotal(){
        $user_id = session('userId');
        if(empty($user_id)){
            if(isset($_SESSION['cart'])){
                return count($_SESSION['cart']);
            }
            return 0;
        }else{
            $map['user_id'] = $user_id;
            $total = M('xgj_eu_cart')->where($map)->count();
            return $total?$total:0;
        }
    }

    /*
    设置购物车中的商品数量
     */
    public function setNum($id, $attr, $num){
        $user_id = session('userId');
        $sql = "UPDATE xgj_eu_cart SET goods_num={$num} WHERE goods_id={$id} AND user_id={$user_id} AND goods_attr_id='{$attr}' LIMIT 1";
        return $this->exec($sql);
    }

    /*
    增加购物车中的商品数量
     */
    public function setIncCart($id, $attr, $num=1){
        $user_id = session('userId');
        $sql = "UPDATE xgj_eu_cart SET goods_num=goods_num+{$num} WHERE goods_id={$id} AND user_id={$user_id} AND goods_attr_id='{$attr}' LIMIT 1";
        return $this->exec($sql);
    }

    /*
    减少购物车中的商品数量
     */
    public function setDecCart($id, $attr, $num=1){
        $user_id = session('userId');
        $sql = "UPDATE xgj_eu_cart SET goods_num=goods_num-{$num} WHERE goods_id={$id} AND user_id={$user_id} AND goods_attr_id='{$attr}' AND goods_num>1 LIMIT 1";
        return $this->exec($sql);
    }


/******************no grass***************************/
    /*
    修改购物车中的商品数量
     */
     public function setCartsNum($id, $goods_id, $num=1,$type=''){
        $user_id = session('userId');
        if (empty($type)) {
            $name = 'xgj_eu_cart';
        }else{
            $name = 'xgj_eu_carts';
        }
        $sql = "UPDATE {$name} SET goods_num={$num} WHERE goods_id={$goods_id} AND user_id={$user_id} AND id='{$id}' LIMIT 1";

        $return = $this->exec($sql);
        if ($return == 1) {
            return 1;
        }else{
            return 2;
        }
    }
    /*
    增加购物车中的商品数量
     */
    public function setIncCarts($id, $goods_id, $num=1,$type=''){
        $user_id = session('userId');
        if (empty($type)) {
            $sql = "UPDATE xgj_eu_cart SET goods_num=goods_num+{$num} WHERE goods_id={$goods_id} AND user_id={$user_id} AND id='{$id}' LIMIT 1";  
        }else{
            $sql = "UPDATE xgj_eu_carts SET goods_num=goods_num+{$num} WHERE goods_id={$goods_id} AND user_id={$user_id} AND id='{$id}' LIMIT 1";  
        }
        $return = $this->exec($sql);

        if ($return == 1) {
            return 1;
        }else{
            return 2;
        }
        
    }

    /*
    减少购物车中的商品数量
     */
    public function setDecCarts($id, $goods_id, $num=1,$type=''){

        $user_id = session('userId');
        if (empty($type)) {
            $sql = "UPDATE xgj_eu_cart SET goods_num=goods_num-{$num} WHERE goods_id={$goods_id} AND user_id={$user_id} AND id='{$id}' AND goods_num>1 LIMIT 1";
        }else{
            $sql = "UPDATE xgj_eu_carts SET goods_num=goods_num-{$num} WHERE goods_id={$goods_id} AND user_id={$user_id} AND id='{$id}' AND goods_num>1 LIMIT 1";
        }

        $return = $this->exec($sql);
        if ($return == 1) {
            return 1;
        }else{
            return 2;
        }
        
    }

    /*
    购物车中的优惠券
     */
    public function coupon($id,$num){
        $user_id = session('userId');
        $sql = "SELECT * FROM xgj_furnish_cart WHERE user_id = {$user_id} AND cart_id = '{$id}' LIMIT 1";
        $list = $this->fetchAll($sql);
        if ($list['0']['type'] == '0') {
            $sql = "UPDATE xgj_furnish_cart SET type='1',coupon='{$num}' WHERE user_id={$user_id} AND cart_id='{$id}' LIMIT 1";
            return $this->exec($sql);
        }else if($list['0']['type'] == '1'){
            $sql = "UPDATE xgj_furnish_cart SET type='0',coupon='{$num}' WHERE user_id={$user_id} AND cart_id='{$id}' LIMIT 1";
            return $this->exec($sql);
        }else{
            return false;
        }
        
    }

    public function UpdataUserCoupon($x){
        $user_id = session('userId');
        $sql = "UPDATE xgj_users SET coupon='{$x}' WHERE user_id={$user_id} LIMIT 1";
        return $this->exec($sql);
    }


    public function homecart(){
        $user_id = session('userId');
        $sql = "SELECT c.*,q.sale,q.coupon cou,q.gift FROM xgj_furnish_cart c JOIN xgj_furnish_quote q ON c.cat_id=q.quote_id WHERE c.user_id = {$user_id}";
        $data = $this->fetchAll($sql);
        return $data;
    }

     
     /**
     * 从购物车中删除商品 ,支持未登陆和已登陆, 支持单个删除和批量删除
     * @param  mixed $id 购物车id
     * @return [type]     [description]
     */

    public function delHomeCartRow($cart_id){
        if(is_array($cart_id)){
            $id = $cart_id;
        }else{
            $id[] = $cart_id;
        }
        $user_id = session('userId');
        if(empty($user_id)){
            foreach ($id as $key => $v) {
                if(isset($_SESSION['homecart'][$v])){
                    unset($_SESSION['homecart'][$v]);

                }else{
                    $this->error = '指定删除的购物车商品不存在';
                    return false;
                }
            }
            return true;
        }else{
            foreach ($id as $key => $v) {
                $sql = "DELETE FROM xgj_furnish_cart WHERE cart_id = '{$v}' and user_id = '{$user_id}'";
                $re = $this->exec($sql);
                if(empty($re)){
                    $this->error = '指定删除的购物车商品不存在';
                    return false;
                }
            }
            return true;
        }
    }

    public function getimage($cat_id){
        $sql = "SELECT img FROM xgj_furnish_quote WHERE quote_id = '{$cat_id}' limit 1";
        $data = $this->fetchAll($sql);
        return $data['0']['img'];
    }

    public function usercoupon(){
        $user_id = session('userId');
        $sql = "SELECT coupon FROM xgj_users WHERE user_id = '{$user_id}' limit 1";
        $data = $this->fetchAll($sql);
        return $data['0']['coupon'];
    }
     

        /*
    获取购物车中所有商品 ,支持未登陆和已登陆
     */
    public function homecarts(){
        $user_id = session('userId');
        if(empty($user_id)){
            
            //没有登陆的情况
            $cart = isset($_SESSION['carts'])?$_SESSION['carts']:array();
            $data = array();

            foreach ($cart as $k=>$v) {
                $tmp = explode('-', $k);
                
                $data[] = array(
                    'goods_id'      => $tmp[0],
                    'goods_attr_id' => $tmp[1],
                    'goods_num'     => $v,
                    'user_id'       => 0,
                    'id'            => $k,
                );

            }
        }else{
            //已经登陆的情况
            // echo $zp;exit;
            $sql = "SELECT * FROM xgj_eu_carts WHERE user_id='{$user_id}'";
            $data = $this->fetchAll($sql);
        }

        //获取所有的商品 因为要兼容未登陆情况, 所有下行的代码作废
        //$sql = "SELECT c.*,g.goods_title,g.shop_price,g.groupbuy_price,g.face_image,g.is_groupbuy FROM xgj_eu_cart AS c LEFT JOIN xgj_eu_goods_new AS g ON c.goods_id=g.id WHERE user_id={$user_id} ORDER BY c.id DESC";
        //$data = $this->fetchAll($sql);

        //查询出商品的属性信息, 计算总价
        $total_price = 0;
        $total_num = 0;
        $discount = 0;
        $goods_list  = array();
        foreach ($data as $key => $cart) {
            $value = $this->field('*')->table('xgj_eu_goods_new')->find($cart['goods_id']);

            //购物车中的商品信息不存在时, 自动删除该记录
            if($value===false) {
                M('xgj_eu_carts')->delete($cart['id']);
                continue;
            }
            if (!empty($cart['coupon'])) {
                $value['coupon'] = $cart['coupon'];
            }else{
                $value['coupon'] = '0';
            }
            
            $value['goods_num'] = $cart['goods_num'];
            $value['goods_id']  = $cart['goods_id'];
            $value['attr_id']   = $cart['goods_attr_id'];
            $value['id']        = $cart['id'];
            $value['express']   = $cart['express'];

            $value['image']     = getImage($value['face_image'],100,100);
            //开启了团购就是用团购价, 没有开启就是本店价格
            if($value['is_groupbuy']==1){
                $value['price'] = $value['groupbuy_price'];
            }else{
                $value['price'] = $value['shop_price'];
            }

            //处理属性: 拼接属性字符创, 累加属性价格
            $value['attr_str'] = '';
            if(!empty($cart['goods_attr_id'])){
                $sql = "SELECT * FROM xgj_eu_goods_attr WHERE id IN ({$cart['goods_attr_id']})";
                $attr_list = $this->fetchAll($sql);
                $attr_str = '';
                foreach ($attr_list as $v) {
                
                    //属性字符串
                    $attr_str .= $v['attr_value'] . '<br/>';

                    //商品属性价格大于0 就累加
                    if($v['attr_price']>0){
                        $value['price'] += $v['attr_price'];
                    }
                }
                $value['attr_str'] = $attr_str;
            }

            //计算单个商品的总价
            $value['total_price'] = number_format($cart['goods_num']*$value['price'],2,'.','');
          
            //优惠价
            $discount += number_format($cart['goods_num']*($value['market_price']-$value['price']),2,'.','');
            
            //计算全部的总价
            $total_price += $value['total_price'];
            //计算全部的数量
            
            $total_num += $cart['goods_num'];
            $goods_list[] = $value;
        }

        /*猜你喜欢*/
        $like_list = array();
        if(!empty($goods_list)){
            $cate_id = $goods_list[0]['cate_id'];
            $map['cate_id'] = $cate_id;
            $like_list = M('xgj_eu_goods_new')->where($map)->limit('0,3')->select();
            $like_list = D('Goods')->processGoods($like_list);
        }
        return array(
            'cart_list'   => $goods_list,
            'total_price' => $total_price,
            'total_num'   => $total_num,
            'discount'    => $discount,
            'like_list'   => $like_list,
            );
    }
/******************no grass***************************/






/******************************************new***************************************/

    /**
     * 添加商品到购物车  ,支持未登陆和已登陆
     * @param integer $id 商品id
     * @param integer $num 商品数量
     * @param String $attr_id 商品属性id, 多个用逗号(,)隔开
     */
    public function addOvCart(){
        $goods_id = I('id');
        $num      = I('amount');
        $attr_id='';
        $user_id  = session('userId');
        if(empty($user_id)){
            //没有登陆的状态
            $key = $goods_id;
            if(isset($_SESSION['ovcart'][$key])){
                //已经存在相同的商品信息
                $_SESSION['ovcart'][$key] += $num;
            }else{
                $_SESSION['ovcart'][$key] = $num;
            }
            return true;
        }else{
            //已经登陆的状态
            $data = array(
                'user_id'       => $user_id,
                'goods_id'      => $goods_id,
                'goods_num'     => $num,
            );

            $sql = "SELECT * FROM xgj_ov_cart WHERE user_id={$user_id} AND goods_id={$goods_id}";

            if($this->fetch($sql)){
                //已经存在相同的商品
                return $this->setIncOvCart($goods_id, $attr_id, $num);
            }else{
                return M('xgj_ov_cart')->data($data)->add();
            }
        }
    }

    /*
    修改购物车中的商品数量
     */
     public function setOvCartsNum($id, $goods_id, $num=1){
        $user_id = session('userId');        
        $sql = "UPDATE xgj_ov_cart SET goods_num={$num} WHERE  goods_id={$goods_id} AND user_id={$user_id} AND id={$id} LIMIT 1";

        $return = $this->exec($sql);
        if ($return == 1) {
            return 1;
        }else{
            return 2;
        }
    }
    /*
    增加购物车中的商品数量
     */
    public function setIncOvCarts($id, $goods_id, $num=1){
        $user_id = session('userId');
        $sql = "UPDATE xgj_ov_cart SET goods_num=goods_num+{$num} WHERE goods_id={$goods_id} AND user_id={$user_id} AND id={$id} LIMIT 1";  
        $return = $this->exec($sql);

        if ($return == 1) {
            return 1;
        }else{
            return 2;
        }
    }

    /*
    减少购物车中的商品数量
     */
    public function setDecOvCarts($id, $goods_id, $num=1){

        $user_id = session('userId');
        $sql = "UPDATE xgj_ov_cart SET goods_num=goods_num-{$num} WHERE goods_id={$goods_id} AND user_id={$user_id} AND id={$id} AND goods_num>1 LIMIT 1";

        $return = $this->exec($sql);
        if ($return == 1) {
            return 1;
        }else{
            return 2;
        }
    }
/*
    获取购物车中所有商品 ,支持未登陆和已登陆
     */
    public function getOvAll(){
        $user_id = session('userId');
        if(empty($user_id)){
            //没有登陆的情况
            $cart = isset($_SESSION['ovcart'])?$_SESSION['ovcart']:array();
            $data = array();
            foreach ($cart as $k=>$v) {
                $tmp = explode('-', $k);
                $data[] = array(
                    'goods_id'      => $tmp[0],
                    'goods_attr_id' => $tmp[1],
                    'goods_num'     => $v,
                    'user_id'       => 0,
                    'id'            => $k,
                );
            }
        }else{
            //已经登陆的情况
            $sql = "SELECT * FROM xgj_ov_cart WHERE user_id={$user_id}";
            $data = $this->fetchAll($sql);
        }

        //查询出商品的属性信息, 计算总价
        $total_price = 0;
        $total_num = 0;
        $discount = 0;
        $goods_list  = array();
        foreach ($data as $key => $cart) {
            $value = $this->field('*')->table('xgj_ov_goods')->find($cart['goods_id']);
            //购物车中的商品信息不存在时, 自动删除该记录
            if($value===false) {
                M('xgj_ov_cart')->delete($cart['id']);
                continue;
            }
            if (!empty($cart['coupon'])) {
                $value['coupon'] = $cart['coupon'];
            }else{
                $value['coupon'] = '0';
            }
            
            $value['goods_num'] = $cart['goods_num'];
            $value['goods_id']  = $cart['goods_id'];
            $value['attr_id']   = $cart['goods_attr_id'];
            $value['id']        = $cart['id'];
            $value['image']     = getImage($value['face_image'],100,100);
        
            $goods_list[] = $value;
        }

        return array(
            'cart_list'   => $goods_list,
            );
    }
    /**
     * 从购物车中删除商品 ,支持未登陆和已登陆, 支持单个删除和批量删除
     * @param  mixed $id 购物车id
     * @return [type]     [description]
     */
    public function delOvCart($cart_id){
        if(is_array($cart_id)){
            $id = $cart_id;
        }else{
            $id[] = $cart_id;
        }
        $user_id = session('userId');
        if(empty($user_id)){
            foreach ($id as $key => $v) {
                if(isset($_SESSION['ovcart'][$v])){
                    unset($_SESSION['ovcart'][$v]);
                }else{
                    $this->error = '指定删除的购物车商品不存在';
                    return false;exit;
                }
            }
            return true;
        }else{
            foreach ($id as $key => $v) {
                $sql = "DELETE FROM xgj_ov_cart WHERE id = {$v} and user_id = {$user_id}";
                $re = $this->exec($sql);
                if(empty($re)){
                    $this->error = '指定删除的购物车商品不存在';
                    return false;exit;
                }
            }
            return true;
        }
    }

    /*
    增加购物车中的商品数量
     */
    public function setIncOvCart($id, $attr, $num=1){
        $user_id = session('userId');
        $sql = "UPDATE xgj_ov_cart SET goods_num=goods_num+{$num} WHERE goods_id={$id} AND user_id={$user_id} AND goods_attr_id='{$attr}' LIMIT 1";
        return $this->exec($sql);
    }

    /*
    减少购物车中的商品数量
     */
    public function setDecOvCart($id, $attr, $num=1){
        $user_id = session('userId');
        $sql = "UPDATE xgj_ov_cart SET goods_num=goods_num-{$num} WHERE goods_id={$id} AND user_id={$user_id} AND goods_attr_id='{$attr}' AND goods_num>1 LIMIT 1";
        return $this->exec($sql);
    }
}