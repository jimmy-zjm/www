<?php
/**
 * 商品入口
 * @date 2016-3-23
 * @author grass <14712905@qq.com>
 */
require './libs/common/initialize.php';

if (empty($_GET))
    $type='index';
elseif (isset($_GET['giveaway']))
    $type='giveaway';
elseif (isset($_GET['id']))
    $type='detail';




define("ACTION", "GoodsController.".$type);
$act = explode("." , ACTION);

if (!is_file(CLASS_DIR .'/'. $act[0] . '.class.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR .'/'.$act[0].".class.php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();