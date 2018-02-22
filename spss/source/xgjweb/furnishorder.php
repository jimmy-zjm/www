<?php
/**
 * 订单入口文件
 * 适用于欧洲团购&德国母婴
 * @date 2016-3-11
 * @author grass <14712905@qq.com>
 */
require './libs/common/initialize.php';

/***************grass********************/
if(empty($_GET))
    $type = 'show';
elseif (isset($_GET['show']))//显示下订单的页面
    $type = 'show';
elseif (isset($_GET['setDefaultAddr']))//设置默认地址
    $type = 'setDefaultAddr';
elseif (isset($_GET['delAddr']))//删除地址
    $type = 'delAddr';
elseif (isset($_GET['process']))//下订单,处理订单
    $type = 'process';
elseif (isset($_GET['orderPay']))//订单页面再次支付,处理订单
    $type = 'orderPay';
elseif (isset($_GET['orderPayAll']))//订单页面一次支付,
    $type = 'orderPayAll';
elseif (isset($_GET['ajaxGetArea']))//获取城市
    $type = 'ajaxGetArea';
elseif (isset($_GET['cancel']))//订单页面取消订单,
    $type = 'cancel';
elseif (isset($_GET['coupon']))//订单页面取消订单,
    $type = 'coupon';
    

define("ACTION", "FurnishOrderController.".$type);
$act = explode("." , ACTION);

if (!is_file(CLASS_DIR .'/'. $act[0] . '.class.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR .'/'.$act[0].".class.php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();