<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");

class FurnishCartModel{


	/**
     *添加至购物车
    */
    function add_cart($data=array()){

        $mysql = new db();

        $return = $mysql->add('xgj_furnish_cart',$data);

        return $return;

    }

    //获取购物车信息
    function getCart(){
        if (!empty($_SESSION['userId'])) {
            $db = new db();
            $sql = "SELECT * FROM xgj_furnish_cart where user_id={$_SESSION['userId']}";
            $detail=$db->getAll($sql);
            return $detail;
        }else{
            return ;
        }
        
    }

    /*
    检查用户是否已经有订单
    */
    function check_order($user_id){

        $mysql = new db();

        $return = $mysql->getOne("select count(order_id) from xgj_furnish_order_info where user_id=$user_id limit 1");
        //var_dump($return);exit;
        return $return;

    }
    /**
     *检查购物车里的商品是否重复
    */
    function check_cart($user_id,$cat_id){

        $mysql = new db();

        $return = $mysql->getOne("select cart_id from xgj_furnish_cart where user_id=$user_id and cat_id=$cat_id limit 1");

        return $return;

    }
    /**
     *删除一条数据
    */
    function del_cart($cart_id){
    	$mysql = new db();
    	$return=$mysql->query("delete from xgj_furnish_cart where cart_id=$cart_id");
    	return $return;
    }
    /**
     *删除多条数据
    */
    function delAllCart($where){
    	$mysql = new db();
    	$return=$mysql->query("delete from xgj_furnish_cart where cart_id in ($where)");
    	return $return;
    }
}