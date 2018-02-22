<?php
namespace Home\Controller;
use Think\Controller;
class CartController extends BaseController {
	private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Home\Model\CartModel;
    }
	//购物车首页
    public function index(){
    	$data=$this->m->getCartByUserId($_SESSION['user']['userId']);
    	$this->assign("data",$data);
    	$this->display();
    }
    /*关注单个商品*/
    public function moveToConcern(){
    	layout(false);
        $cart_id = I('cart_id');
        $class = I('class');
        if(empty($cart_id)) die;
        	$user_id = $_SESSION['user']['userId'];
        if(empty($user_id)){
            $_SESSION['redirect_url'] = $_SERVER['PHP_SELF'];
            echo '2';
        }
        $id = $cart_id;
        //获取商品数据
        if (!empty($class) && $class == 1 && empty($type)) {
            $goods                = M('xgj_furnish_cart')->find($id);
            //拼凑关注表中的数据
            $data['goods_id']     = $goods['cat_id'];
            $data['class_id']     = '1';
            $data['user_id']      = $user_id;
            $data['c_images']     = D('Cart')->getFuImage($goods['cat_id']);
            $data['c_goodsname']  = $goods['shop_name'];
            $data['c_goodsprice'] = $goods['price'];
            //已经关注的情况
            if(M('xgj_concern')->where(array('c_goodsname'=>$goods['shop_name'],'user_id'=>$user_id,'class_id'=>1))->count()>0){
                echo '4';die;
            }
            //添加关注的情况
            if(!M('xgj_concern')->data($data)->add()){
                echo '3';die;
            }else{
            	echo '1';die;
            }
        }else if(!empty($class) && $class == 2){//欧洲建材关注
        	$goods                = M('xgj_furnish_cart')->find($id);
            //拼凑关注表中的数据
            $data['goods_id']     = $goods['cat_id'];
            $data['class_id']     = '2';
            //$data['cate_id']      = $goods['cat_id'];
            $data['user_id']      = $user_id;
            $data['c_images']     = D('Cart')->getEuImage($goods['cat_id']);
            $data['c_goodsname']  = $goods['shop_name'];
            $data['c_goodsprice'] = $goods['price'];

            //已经关注的情况
            if(M('xgj_concern')->where(array('goods_id'=>$goods['cat_id'],'user_id'=>$user_id,'class_id'=>2))->count()>0){
                echo '4';die;
            }
            //添加关注的情况
            if(!M('xgj_concern')->data($data)->add()){
                echo '3';die;
            }else{
            	echo '1';die;
            }
        }else if(!empty($class) && $class == 8){//机电售后关注
            $goods                = M('xgj_furnish_cart')->find($id);
            //拼凑关注表中的数据
            $data['goods_id']     = $goods['cat_id'];
            $data['class_id']     = '8';
            $data['user_id']      = $user_id;
            $data['c_images']     = $goods['img'];
            $data['c_goodsname']  = $goods['shop_name'];
            $data['c_goodsprice'] = $goods['price'];

            //已经关注的情况
            if(M('xgj_concern')->where(array('goods_id'=>$goods['cat_id'],'user_id'=>$user_id,'class_id'=>8))->count()>0){
                echo '4';die;
            }
            //添加关注的情况
            if(!M('xgj_concern')->data($data)->add()){
                echo '3';die;
            }else{
                echo '1';die;
            }
        }else if(!empty($class) && $class == 9){//机电耗材关注
            $goods                = M('xgj_furnish_cart')->find($id);
            //拼凑关注表中的数据
            $data['goods_id']     = $goods['cat_id'];
            $data['class_id']     = '9';
            $data['user_id']      = $user_id;
            $data['c_images']     = $goods['img'];
            $data['c_goodsname']  = $goods['shop_name'];
            $data['c_goodsprice'] = $goods['price'];

            //已经关注的情况
            if(M('xgj_concern')->where(array('goods_id'=>$goods['cat_id'],'user_id'=>$user_id,'class_id'=>9))->count()>0){
                echo '4';die;
            }
            //添加关注的情况
            if(!M('xgj_concern')->data($data)->add()){
                echo '3';die;
            }else{
                echo '1';die;
            }
        }
    }       

    /*批量关注商品*/
    public function moveToConcerns(){
    	layout(false);
        $cart_id = I('cart_id');
        if(empty($cart_id)) die;
        	$user_id = $_SESSION['user']['userId'];
        if(empty($user_id)){
            $_SESSION['redirect_url'] = $_SERVER['PHP_SELF'];
            echo '2';
        }
        if(is_array($cart_id)){
            $id = $cart_id;
        }else{
            $id[] = $cart_id;
        }
        $flag = true;
        $error = 0;
        foreach ($id as $key => $v) {
        	$info=explode('-',$v);
            //获取商品数据
            if (!empty($info)) {
                $goods                = M('xgj_furnish_cart')->find($info[0]);
                //拼凑关注表中的数据
                $data['goods_id']     = $goods['cat_id'];
                $data['class_id']     = $info[1];
                $data['user_id']      = $user_id;
                if($info[1] == 1){
                	$data['c_images']     = D('Cart')->getFuImage($goods['cat_id']);
                }else if($info[1] == 2){
                	$data['c_images']     = D('Cart')->getEuImage($goods['cat_id']);
                }
                $data['c_goodsname']  = $goods['shop_name'];
                $data['c_goodsprice'] = $goods['price'];
                //已经关注的情况
                if(M('xgj_concern')->where(array('goods_id'=>$goods['cat_id'],'user_id'=>$user_id,'class_id'=>$info[1]))->count()>0){
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
        }else{
            echo '1';
        }
    }       
            
    /*删除购物车中家居商品*/
    public function delHomeCartRow(){
    	layout(false);
        $id = I('cart_id');
        if(empty($id)) $this->redirect('Cart/index');
        if(D('Cart')->delHomeCartRow($id)){
            echo '1';exit;
        }else{
            echo '2';exit;
        }
    }

    /*删除购物车中家居商品*/
    public function delHomeCartAll(){
    	layout(false);
        $cart_id = I('cart_id');
        if(is_array($cart_id)){
            $id = $cart_id;
        }else{
            $id[] = $cart_id;
        }
        $error = 0;
        if(empty($id)) $this->redirect('Cart/index');
        foreach ($id as $key => $v) {
        	$info=explode('-',$v);
            //获取商品数据
            if (!empty($info)) {
            	if(!D('Cart')->delHomeCartRow($info[0])){
		            $error++;
		        }
            }
        }
        if($error>0){
            echo '2';die;
        }else{
            echo '1';die;
        }
    }

    //提交购物车 L
    public function subCart(){
    	layout(false);
    	$cart_id = I('cart_id');

    	$key = array_keys($cart_id);
    	$map['cart_id']  = array('in',$key);
        $num = count(M('xgj_furnish_cart')->where($map)->group('house_id')->select());
    	$class = count(M('xgj_furnish_cart')->where($map)->group('class')->select());
        //var_dump($class);die;
        if ($class>1) {
            echo '家居、建材、保养及耗材产品需分开结算！！';
        }else if($class==1) {
            if ($num==1 || $num==0) {
                $_SESSION['user']['cart']=$cart_id;
                echo '1';
            }else{
                echo '舒适家居多套系统不同地址需分开结算！！';
            }
        }  
    }

}

