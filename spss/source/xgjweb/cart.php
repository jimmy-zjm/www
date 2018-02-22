<?php
/**
 * 购物车入口文件
 * 适用于欧洲团购&德国母婴
 * @date 2016-3-10
 * @author grass <14712905@qq.com>
 */
require './libs/common/initialize.php';

/***************grass********************/
if(empty($_GET))//默认为 购物车列表
    $type = 'cartList';
elseif (isset($_GET['addCart']))//添加商品到购物车
    $type = 'addCart';
elseif (isset($_GET['addOvCart']))//添加海外超市商品到购物车
    $type = 'addOvCart';
elseif (isset($_GET['delCart']))//从购物车中删除商品
    $type = 'delCart';
elseif (isset($_GET['cartList']))//购物车列表
    $type = 'cartList';
elseif (isset($_GET['moveToConcern']))//移动到关注
    $type = 'moveToConcern';
elseif (isset($_GET['setIncCarts']))
    $type = 'setIncCarts';
elseif (isset($_GET['setDecCarts']))
    $type = 'setDecCarts';
elseif (isset($_GET['setCartsNum']))
    $type = 'setCartsNum';
elseif (isset($_GET['coupon']))
    $type = 'coupon';
elseif (isset($_GET['delCartRow']))
    $type = 'delCartRow';
elseif (isset($_GET['moveToConcerns']))//移动到关注
    $type = 'moveToConcerns';
elseif (isset($_GET['delHomeCartRow']))
    $type = 'delHomeCartRow';
elseif (isset($_GET['moveToHomeConcerns']))
    $type = 'moveToHomeConcerns';

elseif (isset($_GET['setIncOvCarts']))
    $type = 'setIncOvCarts';
elseif (isset($_GET['setDecOvCarts']))
    $type = 'setDecOvCarts';
elseif (isset($_GET['setOvCartsNum']))
    $type = 'setOvCartsNum';
elseif (isset($_GET['delOvCartRow']))
    $type = 'delOvCartRow';

define("ACTION", "CartController.".$type);
$act = explode("." , ACTION);

if (!is_file(CLASS_DIR .'/'. $act[0] . '.class.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR .'/'.$act[0].".class.php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();