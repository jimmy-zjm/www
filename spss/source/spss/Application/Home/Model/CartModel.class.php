<?php
namespace Home\Model;
use Think\Model;
class CartModel extends Model {
	protected $trueTableName = 'xgj_furnish_cart';

	//获取当前用户的购物车
    public function getCartByUserId($user_id){
    	$data=M('xgj_furnish_cart')->where("user_id = $user_id")->order('class')->select();
    	return $data;
    }
    
    //统计当前系统图片
    public function getFuImage($cat_id){

        $img=M('xgj_furnish_quote')->where("quote_id = '{$cat_id}'")->limit('1')->getField('img');
        return $img;
    }

    //统计当前商品图片
    public function getEuImage($cat_id){

        $img=M('xgj_eu_goods_new')->where("id = '{$cat_id}'")->limit('1')->getField('img');
        return $img;
    }

    /**
     * 从购物车中删除商品 ,支持未登陆和已登陆, 支持单个删除和批量删除
     * @param  mixed $id 购物车id
     * @return [type]     [description]
     */
    public function delHomeCartRow($cart_id){

        $user_id = $_SESSION['user']['userId'];
        $res=M('xgj_furnish_cart')->where(array('cart_id'=>$cart_id, 'user_id'=>$user_id))->delete();
        return $res;
    }

}