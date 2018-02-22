<?php
namespace Home\Controller;

class CustomerController extends BaseController {
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Home\Model\CustomerModel;
    }
    
    public function index(){
        /**********************/
        //查询头部广告
        $map['is_on']     = 1;
        $map['ad_pos_id'] = 15;
        $image = M('xgj_ad')->where($map)->getField('image');
        $this->assign('image',getimage($image)); 
        /**********************/
        $this->display();
    }

    public function detail(){
        $id = I('id');

        /***************************/
        //查询商品信息
        $dataMap['is_put'] = '1';
        $dataMap['id']     = $id;
        $data = M('xgj_s_consumable')->where($dataMap)->find();

        //商品不存在返回
        if (empty($data)) {
            layout(false);
            $this->error('抱歉！没有您要的商品');
        }

        //查询系统名称
        $data['quote_name'] = M('xgj_furnish_quote')->where(array('quote_id'=>$data['quote_name']))->getField('quote_name');
        /***************************/


        /**********************/
        //查询登录状态下是否收藏并查询收藏总量
        if (!empty($_SESSION['user']['userId'])) {
            $userId  = $_SESSION['user']['userId'];
            $collect = M('xgj_concern')->where(array('goods_id'=>$id,'user_id'=>$userId,'class_id'=>4))->count();
            $this->assign('collect',$collect);
        }
        //收藏总数
        $count   = M('xgj_concern')->where(array('goods_id'=>$id,'class_id'=>4))->count();
        /************************/

        $this->assign('count',$count);
        $this->assign('data',$data);
        $this->display();
        // $this->assign('cateList',$cateList); 
    }

     //收藏
    public function collect(){
        if (empty($_SESSION['user']['userId'])) {
            echo '请先登录再收藏';
        }else{
            $id = I('post.id');
            $attr = I('post.attr');
            $userId = $_SESSION['user']['userId'];

            $dataMap['is_put'] = '1';
            $dataMap['id']     = $id;
            $data = M('xgj_s_consumable')->where($dataMap)->find();

            //商品不存在返回
            if (empty($data)) die('抱歉！没有您要的商品');


            if (M('xgj_concern')->where(array('goods_id'=>$id,'user_id'=>$userId,'class_id'=>4))->count()>0) {
                echo '您已收藏此商品！';
            }else{
                $re = $data;
                $data = array(
                    'class_id'     => 4,
                    'user_id'      => $userId,
                    'goods_id'     => $id,
                    'c_images'     => $re['c_img'],
                    'c_goodsname'  => $re['product_name'],
                    'c_goodsprice' => $re['price'],
                    );

                //添加关注的情况
                if(!M('xgj_concern')->data($data)->add()) echo '收藏失败';
                else echo '1';
            }
        }
    }

    //保养
    public function upkeep(){
        $count = $this->m->getUpkeepAll();
        $page  = getPage(count($count),6);
        $list  = $this->m->getUpkeepAll($page['limit']);
        $this->assign('page',$page['page']); 
        $this->assign('list',$list); 
        $this->display();
    }
    //耗材
    public function consumable(){
        $quote_id=I('quote_id')?trim(I('quote_id')):'';
        $data=$this->m->getConsumable($quote_id);
        $this->assign('data',$data); 
        $this->display();
    }
    //维修
    public function maintain(){
        $user_id=$_SESSION['user']['userId'];
        $quoteInfo=$this->m->getQuoteName($user_id);
        //var_dump($quoteInfo);die;
        $this->assign("quoteInfo",$quoteInfo);
        $this->display();
    }

    //查找用户订单的地址信息
    public function addressInfo(){
        $order_id=$_GET['order_id'];
        $addressArr=$this->m->getHouseAddr($order_id);
        $addressInfo=json_encode($addressArr);
        echo $addressInfo;
    }
    //保存用户提交的问题信息
    public function saveProblem(){
        if (empty($_POST['quote_id'])) {
            echo "请选择产品";die;
        }
        if(empty($_POST['phone'])){
            echo "手机号码不能为空";die;
        }else if(!preg_match("/^1[34578]\d{9}$/",$_POST['phone'])){
            echo "手机号码格式不正确空";die;
        }
        $quote_id='';
        foreach ($_POST['quote_id'] as $k => $v) {
            $arr=explode('-',$v);
            $order_id=$arr[0];
            $quote_id.=$arr[1].'-';
        }
        
        $addressArr=$this->m->getHouseAddr($order_id);
        $data=array (
            'order_id'=>intval($order_id),
            'quote_id'=>rtrim($quote_id,'-'),
            'name' => trim($_POST['name']),
            'phone' => trim($_POST['phone']),
            'type' => rtrim($type,'-'),
            'address' => $addressArr['province'].$addressArr['city'].$addressArr['district'].$addressArr['address'],
            'note' => html_filter($_POST['note']),
            'user_id'=>$_SESSION['user']['userId'],
            'time'=>strtotime($_POST['time']),
            );
        $rs=M('xgj_user_problem')->data($data)->add();
        if($rs){
            echo "提交成功";die;
        }else{
            echo "提交失败";die;
        }
    }


    //加入购物车
    public function goCart(){
        layout(false);

        if (empty($_SESSION['user']['userId'])) {
            echo 'login';die;
        }

        $id  = I('post.id');
        $num = I('post.num');
        if(empty($id)){
            echo '该商品不存在';die;
        }
        if(!preg_match("/^[1-9]([0-9]+)?$/", $num)){
            echo '请选择数量';die;
        }

        $user_id=$_SESSION['user']['userId'];
        $info=M("xgj_s_upkeep")->find($id);
        if($info['is_use']!=1){
            echo '该商品已下架';die;
        }
        $cartNum=M("xgj_furnish_cart")->where(array('cat_id'=>$id, 'user_id'   =>$user_id,'class'=>9))->count('cart_id');
        if($cartNum){
            if(M('xgj_furnish_cart')->where(array('cat_id'=>$id, 'user_id'   =>$user_id,'class'=>9))->setInc('num',$num)){
                echo '添加成功';die;
            }else{
                echo '添加失败';die;
            }  
        }
        $data=array(
                'user_id'   =>$user_id,
                'cat_id'    =>$info['id'],
                'shop_name' =>$info['name'],
                'price'     =>$info['price'],
                'num'       =>$num,
                'img'       =>$info['u_img'],
                'class'     =>'9',
            );
        if(M('xgj_furnish_cart')->add($data)){
            echo '添加成功';die;
        }else{
            echo '添加失败';die;
        }
    }


    public function addCart(){
        layout(false);
        $id=I('id')?I('id'):'';
        $num=I('num')?I('num'):'';

        if(empty($id)){
            $this->redirect("Customer/consumable");
        }

        $user_id=$_SESSION['user']['userId'];
        $info=M("xgj_s_consumable")->find($id);
        if($info['is_put']!=1){
            echo '-1';die;
        }
        $cartNum=M("xgj_furnish_cart")->where(array('cat_id'=>$id,'user_id'   =>$user_id,'class'=>8))->count('cart_id');
        if($cartNum){
            if (!empty($num)) {
                $re = M('xgj_furnish_cart')->where(array('cat_id'=>$id,'user_id'   =>$user_id,'class'=>8))->setInc('num',$num);
            } else {
                $re = M('xgj_furnish_cart')->where(array('cat_id'=>$id,'user_id'   =>$user_id,'class'=>8))->setInc('num');
            }
            $cart_id=M("xgj_furnish_cart")->where(array('cat_id'=>$id,'user_id'   =>$user_id,'class'=>8))->getField('cart_id');
            if($re){
                echo $cart_id;die;
            }else{
                echo '-2';die;
            }  
        }
        $data=array(
                'user_id'=>$user_id,
                'cat_id'=>$info['id'],
                'shop_name'=>$info['c_name'],
                'price'=>$info['price'],
                'img'=>$info['c_img'],
                'class'=>'8',
            );
        $id=M('xgj_furnish_cart')->add($data);
        if($id){
            echo $id;die;
        }else{
            echo '-2';die;
        }
    }

}