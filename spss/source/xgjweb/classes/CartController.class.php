<?php
/**
 * 购物车控制器
 * 适用于欧洲团购&德国母婴
 * @date 2016-3-10
 * @author grass <14712905@qq.com>
 */
class CartController extends Controller{

    /**
     * 添加到购物车
     *
     */
	

    public function addCart(){
        //var_dump($_POST);exit;
        if(IS_POST){
            if($id = D('Cart')->addCart()){
                $this->redirect('?cartList');
            }else{
                $this->error('添加失败','?index');
            }
        }else{
            die('非法请求');
        }
    }


     /*
        展示购物车列表
     */
    public function cartList(){
		$user_id = session('userId');		
        if(empty($user_id)) $this->error('未登录! 请先去登录','user.php?login');
			
        $data = D('Cart')->getAll();
        
        $homecart = D('Cart')->homecart();
        $coupon = D('Cart')->usercoupon();
        $homecarts = D('Cart')->homecarts();
        $homecarts['cart_list'] =$this->getTotal($homecarts['cart_list']);
        $data['cart_list'] =$this->getTotal($data['cart_list']);

        // 海外超市购物车
        $data_ = D('Cart')->getOvAll();
        $this->assign('ovcart_list', $data_['cart_list']);

        $map['is_show']    = 1;
        $map['class_id']   = 2;
        $map['pid']        = 0;
        $cate_list = M('xgj_ov_category')->where($map)->order('`order` ASC')->select();
        foreach($cate_list as $k=>$v){
            $cate_list[$k]['list']=M('xgj_ov_category')->where("pid={$v['id']}")->order('`order` ASC')->select();
        }

        $data1 = D('Eugroup')->category(1);
		$cate_list = D('Eugroup')->Ov_Category();
		$this->assign('eu_tree', $data1);
		$this->assign("ov_tree",$cate_list);

        $this->assign('coupon', $coupon);
        $this->assign('homecart', $homecart);
        $this->assign('homecarts', $homecarts);

        $this->assign('cart_list', $data['cart_list']);
        $this->assign('total_price', $data['total_price']);
        $this->assign('total_num',$data['total_num']);
        $this->assign('discount',$data['discount']);
        $this->display('cart/eucart.tpl.html');
    }

    /*
    删除购物车中的商品
     */
    public function delCart(){
        $id = I('cart_id');
        if(empty($id)) $this->redirect('?cartList');
        if(D('Cart')->delCart($id)){
            $this->redirect('?cartList');
        }else{
            $this->error('删除失败','?cartList');
        }
    }

    /*
    移动到我的关注
     */
    public function moveToConcern(){
        $goods_id = I('goods_id');
        if(empty($goods_id)) die;
        $user_id = session('userId');
        if(empty($user_id)){
            $_SESSION['redirect_url'] = $_SERVER['PHP_SELF'];
            api(-2,'先登陆才能进行关注','','user.php?login');
        }
        if(is_array($goods_id)){
            $id = $goods_id;
        }else{
            $id[] = $goods_id;
        }
        $flag = true;
        $error = 0;
        foreach ($id as $key => $v) {
            //获取商品数据
            $goods                = M('xgj_eu_goods_new')->find($v);

            //拼凑关注表中的数据
            $data['goods_id']     = $v;
            $data['class_id']     = $goods['cate_id'];
            $data['user_id']      = $user_id;
            $data['c_images']     = $goods['face_image'];
            $data['c_goodsname']  = $goods['goods_title'];
            $data['c_goodsprice'] = D('Eugroup')->getPrice($v);

            //已经关注的情况
            if(M('xgj_concern')->where(array('goods_id'=>$v,'user_id'=>$user_id))->count()>0){
                continue;
            }

            //添加关注的情况
            if(!M('xgj_concern')->data($data)->add()){
                $error++;
            }
        }
        if($error>0){
            api(-1,'参数错误');
        }else{
            api(1,'success');
        }

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
        	 $market_price		    =$t['market_price']*$currency;//原价
			 $dutiesy               =round($market_price*$t['duties'],2);//关税价
             $arry                  =explode('-',$t['luggage']);
             $luggage1y             =$arry[0];//海运运费
             $luggage2y             =$arry[1];//空运运费
             $vat1y                 =round(($market_price+$dutiesy+$luggage1y)*$t['vat'],2);//增值税1
             $service_charge1y      =round(($market_price+$dutiesy+$luggage1y+$vat1y)*$t['service_charge'],2);//服务费1
             $vat2y                 =round(($market_price+$dutiesy+$luggage2y)*$t['vat'],2);//增值税2
             $service_charge2y      =round(($market_price+$dutiesy+$luggage2y+$vat2y)*$t['service_charge'],2);//服务费2
             $goods_list[$k]['yuanjia1']          = round($market_price+$dutiesy+$luggage1y+$vat1y+$service_charge1y,1);
             $goods_list[$k]['yuanjia2']          = round($market_price+$dutiesy+$luggage2y+$vat2y+$service_charge2y,1);

             $purchase             =$t['purchase']*$currency;//采购价
             $duties               =$purchase*$t['duties'];//关税价
             $arr                  =explode('-',$t['luggage']);
             $luggage1             =$arr[0];//海运运费
             $luggage2             =$arr[1];//空运运费
             $vat1                 =($purchase+$duties+$luggage1)*$t['vat'];//增值税1
             $service_charge1      =($purchase+$duties+$luggage1+$vat1)*$t['service_charge'];//服务费1
             $vat2                 =($purchase+$duties+$luggage2)*$t['vat'];//增值税2
             $service_charge2      =($purchase+$duties+$luggage2+$vat2)*$t['service_charge'];//服务费2
             $goods_list[$k]['luggage1']        = $luggage1;
             $goods_list[$k]['luggage2']        = $luggage2;
             $goods_list[$k]['vat1']            = $vat1;
             $goods_list[$k]['vat2']            = $vat2;
             $goods_list[$k]['service_charge1'] = $service_charge1;
             $goods_list[$k]['service_charge2'] = $service_charge2;
             $goods_list[$k]['money']           = $purchase;
             $goods_list[$k]['duties1']         = $duties;
             $goods_list[$k]['total1']          = round($purchase+$duties+$luggage1+$vat1+$service_charge1,1);
             $goods_list[$k]['total2']          = round($purchase+$duties+$luggage2+$vat2+$service_charge2,1);
        }
        return $goods_list;
    }

/******************no grass***************************/

    /*
    关注
     */
    public function moveToConcerns(){
        $goods_id = I('goods_id');
        $class = I('class');
        if(empty($goods_id)) die;
        $user_id = session('userId');
        if(empty($user_id)){
            $_SESSION['redirect_url'] = $_SERVER['PHP_SELF'];
            echo '2';
        }
        if(is_array($goods_id)){
            $id = $goods_id;
        }else{
            $id[] = $goods_id;
        }
        $flag = true;
        $error = 0;


        foreach ($id as $key => $v) {
            //获取商品数据
            if (!empty($class) && $class == 1 && empty($type)) {
                $goods                = M('xgj_furnish_cart')->find($v);

                //拼凑关注表中的数据
                $data['goods_id']     = $goods['cat_id'];
                $data['class_id']     = '1';
               // $data['cate_id']      = $goods['cat_id'];
                $data['user_id']      = $user_id;
                $data['c_images']     = D('Cart')->getimage($goods['cat_id']);
                $data['c_goodsname']  = $goods['shop_name'];
                $data['c_goodsprice'] = $goods['price'];
                //已经关注的情况
                if(M('xgj_concern')->where(array('c_goodsname'=>$goods['shop_name'],'user_id'=>$user_id,'class_id'=>1))->count()>0){
                    continue;
                }
                //添加关注的情况
                if(!M('xgj_concern')->data($data)->add()){
                    $error++;
                }
                
            }else if(!empty($class) && $class == 3){
                $goods                = M('xgj_ov_goods')->find($v);

                //拼凑关注表中的数据
                $data['goods_id']     = $v;
                $data['class_id']     = '3';
                $data['cate_id']      = $goods['cate_id'];
                $data['user_id']      = $user_id;
                $data['c_images']     = $goods['face_image'];
                $data['c_goodsname']  = $goods['goods_title'];
                $data['c_goodsprice'] = $goods['purchase'];

                //已经关注的情况
                if(M('xgj_concern')->where(array('goods_id'=>$v,'user_id'=>$user_id,'class_id'=>3))->count()>0){
                    continue;
                }
                //添加关注的情况
                if(!M('xgj_concern')->data($data)->add()){
                    $error++;
                }
            }else{
                $goods                = M('xgj_eu_goods_new')->find($v);

                //拼凑关注表中的数据
                $data['goods_id']     = $v;
                $data['class_id']     = '2';
                $data['cate_id']      = $goods['cate_id'];
                $data['user_id']      = $user_id;
                $data['c_images']     = $goods['face_image'];
                $data['c_goodsname']  = $goods['goods_title'];
                $data['c_goodsprice'] = $goods['purchase'];

                //已经关注的情况
                if(M('xgj_concern')->where(array('goods_id'=>$v,'user_id'=>$user_id,'class_id'=>2))->count()>0){
                    continue;
                }

                //添加关注的情况
                if(!M('xgj_concern')->data($data)->add()){
                    $error++;
                }
            }
            
        }
        if($error>0){
            echo '3';
            // api(-1,'参数错误');
        }else{
            echo '1';
            // api(1,'success');
        }

    }

    /*
    删除购物车中的商品
     */
    public function delCartRow(){
        $id = I('cart_id');
        if(empty($id)) $this->redirect('?cartList');

        $type = I('type');

        if(D('Cart')->delCart($id,$type)){
            echo '1';exit;
        }else{
            echo '2';exit;
        }
    }
  

    //购物车数量
    public function setCartsNum(){

        $id = $_GET['id'];
       
        $goods_id = $_GET['goods_id'];

        $num = $_GET['num'];

        if (!empty($_GET['type'])) {
            $type = $_GET['type'];
        }else{
            $type = '';
        }
        //判断是不是数字
        if(preg_match("/^[0]$/", $num) || !preg_match("/^[0-9]*$/", $num)){
            return false;exit;
        }

        //未登录状态下
        if ($id == 0) {
            foreach ($_SESSION['cart'] as $key => $value) {
                if ($num >= 1) {
                    if (empty($type)) {
                        $_SESSION['cart'][$key] = $num;
                    }else{
                        $_SESSION['carts'][$key] = $num;
                    }
                }else{
                    echo '';exit;
                }
            }
            echo 1;exit;
        }

        //已登录状态下
        if(!preg_match("/^[0-9]*$/", $goods_id) || !preg_match("/^[0-9]*$/", $id)){
            return false;exit;
        }

        $data = D('Cart')->setCartsNum($id,$goods_id,$num,$type);

        echo $data;
    }

    //购物车减少数量
    public function setDecCarts(){

        $id = $_GET['id'];
       
        $goods_id = $_GET['goods_id'];

        if (!empty($_GET['type'])) {
            $type = $_GET['type'];
        }else{
            $type = '';
        }
        
        //未登录状态下
        if ($id == 0) {

            if (empty($type)) {
                foreach ($_SESSION['cart'] as $key => $value) {
                    if ($_SESSION['cart'][$key] > 1) {
                        $_SESSION['cart'][$key] = $value-1;
                    }else{
                        echo '';exit;
                    }
                }
            }else{
                foreach ($_SESSION['carts'] as $key => $value) {
                    if ($_SESSION['carts'][$key] > 1) {
                        $_SESSION['carts'][$key] = $value-1;
                    }else{
                        echo '';exit;
                    }
                }
            }
            echo 1;exit;
        }

        //已登录状态下
        if(!preg_match("/^[0-9]*$/", $goods_id) || !preg_match("/^[0-9]*$/", $id)){
            return false;exit;
        }

        $data = D('Cart')->setDecCarts($id,$goods_id,1,$type);

        echo $data;
    }

    //购物车增加数量
    public function setIncCarts(){
        $id = $_GET['id'];
        $goods_id = $_GET['goods_id'];
        if (!empty($_GET['type'])) {
            $type = $_GET['type'];
        }else{
            $type = '';
        }
        //未登录状态下
        if ($id == 0) {
            if (empty($type)) {
                foreach ($_SESSION['cart'] as $key => $value) {
                    $_SESSION['cart'][$key] = $value+1;
                }
                echo 1;exit;
            }else{
                foreach ($_SESSION['carts'] as $key => $value) {
                    $_SESSION['carts'][$key] = $value+1;
                }
                echo 1;exit;
            }
            
        }
        
        //已登录状态下
        if(!preg_match("/^[0-9]*$/", $goods_id) || !preg_match("/^[0-9]*$/", $id)){
            return false;exit;
        }
        $data = D('Cart')->setIncCarts($id,$goods_id,'1',$type);
        echo $data;
    }

    //优惠券
    public function coupon(){

        $id = $_GET['id'];
        $num = $_GET['num'];
        $x = $_GET['x'];
        $data = D('Cart')->coupon($id,$num);
        if ($data == 1) {
            $userdata = D('Cart')->UpdataUserCoupon($x);
        }
        echo $userdata;
    }


    /*
    删除购物车中家居商品
     */
    public function delHomeCartRow(){
        $id = I('cart_id');
        if(empty($id)) $this->redirect('?cartList');
        if(D('Cart')->delHomeCartRow($id)){
            echo '1';exit;
        }else{
            echo '2';exit;
        }
    }
/******************no grass***************************/



public function addOvCart(){
        //var_dump($_POST);exit;
        if(IS_POST){
            if($id = D('Cart')->addOvCart()){
                $this->redirect('?cartList');
            }else{
                $this->error('添加失败','?index');
            }
        }else{
            die('非法请求');
        }
    }


//购物车减少数量
    public function setDecOvCarts(){
        $id = $_GET['id'];
        $goods_id = $_GET['goods_id'];        
        //未登录状态下
        if ($id == 0) {
            foreach ($_SESSION['ovcart'] as $key => $value) {
                if ($_SESSION['ovcart'][$key] > 1) {
                    $_SESSION['ovcart'][$key] = $value-1;
                }else{
                    echo '';exit;
                }
            }
            echo 1;exit;
        }
        //已登录状态下
        if(!preg_match("/^[0-9]*$/", $goods_id) || !preg_match("/^[0-9]*$/", $id)){
            return false;exit;
        }
        $data = D('Cart')->setDecOvCarts($id,$goods_id,1);
        echo $data;
    }

    //购物车增加数量
    public function setIncOvCarts(){
        $id = $_GET['id'];
        $goods_id = $_GET['goods_id'];
        //未登录状态下
        if ($id == 0) {
            foreach ($_SESSION['ovcart'] as $key => $value) {
                $_SESSION['ovcart'][$key] = $value+1;
            }
            echo 1;exit;
        }
        //已登录状态下
        if(!preg_match("/^[0-9]*$/", $goods_id) || !preg_match("/^[0-9]*$/", $id)){
            return false;exit;
        }
        $data = D('Cart')->setIncOvCarts($id,$goods_id,'1');
        echo $data;
    }

    //购物车数量
    public function setOvCartsNum(){
        $id = $_GET['id'];
        $goods_id = $_GET['goods_id'];
        $num = $_GET['num'];
        //判断是不是数字
        if(preg_match("/^[0]$/", $num) || !preg_match("/^[0-9]*$/", $num)){
            return false;exit;
        }
        //未登录状态下
        if ($id == 0) {
            foreach ($_SESSION['ovcart'] as $key => $value) {
                if ($num >= 1) {
                    $_SESSION['cart'][$key] = $num;
                }else{
                    echo '';exit;
                }
            }
            echo 1;exit;
        }
        //已登录状态下
        if(!preg_match("/^[0-9]*$/", $goods_id) || !preg_match("/^[0-9]*$/", $id)){
            return false;exit;
        }
        $data = D('Cart')->setOvCartsNum($id,$goods_id,$num);
        echo $data;
    }

     /*
    删除购物车中的商品
     */
    public function delOvCartRow(){
        $id = I('cart_id');
        if(empty($id)) $this->redirect('?cartList');

        if(D('Cart')->delOvCart($id)){
            echo '1';exit;
        }else{
            echo '2';exit;
        }
    }




}