<?php
/**
 * ajax请求专用入口
 * @date 2016-3-16
 * @author grass <14712905@qq.com>
 */
require './libs/common/initialize.php';
/***************grass********************/
if(empty($_GET))
    $type = 'show';
elseif (isset($_GET['detailInit']))//商品详情页面ajax总入口
    $type = 'detailInit';
elseif (isset($_GET['ajaxGetArea']))//根据id获取城市
    $type = 'ajaxGetArea';
elseif (isset($_GET['addAddress']))//执行添加地址
    $type = 'addAddress';
elseif (isset($_GET['addrList']))//获取所有地址信息
    $type = 'addrList';
elseif (isset($_GET['setDefaultAddr']))//设置默认地址
    $type = 'setDefaultAddr';
elseif (isset($_GET['delAddr']))//删除地址
    $type = 'delAddr';
elseif (isset($_GET['getAddr']))//根据id获取地址
    $type = 'getAddr';
elseif (isset($_GET['saveAddress']))//执行保存地址
    $type = 'saveAddress';
elseif (isset($_GET['getStock']))//获取库存
    $type = 'getStock';
elseif (isset($_GET['getStock2']))//获取库存
    $type = 'getStock2';
elseif (isset($_GET['getCommentList']))//获取评论列表
    $type = 'getCommentList';
elseif (isset($_GET['getCommentList2']))//获取海外超市评论列表
    $type = 'getCommentList2';
elseif (isset($_GET['concern']))//关注商品
    $type = 'concern';
elseif (isset($_GET['addCart']))//商品详情页面加入购物车
    $type = 'addCart';
elseif (isset($_GET['addOvCart']))//海外超市商品详情页面加入购物车
    $type = 'addOvCart';
elseif (isset($_GET['getHeader']))//获取头部的所有数据
    $type = 'getHeader';
elseif (isset($_GET['realname']))//获取头部的所有数据
    $type = 'realname';


define("ACTION", "AjaxController.".$type);
$act = explode("." , ACTION);

if (!is_file(CLASS_DIR .'/'. $act[0] . '.class.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR .'/'.$act[0].".class.php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();
