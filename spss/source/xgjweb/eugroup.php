<?php
/**
 * 欧洲团购入口文件
 * @date 2016-3-11
 * @author grass <14712905@qq.com>
 */
require './libs/common/initialize.php';
/***************grass********************/
if (empty($_GET))
	$type='index';
elseif (isset($_GET['index']))//首页
	$type='index';
elseif (isset($_GET['zp']))//首页
	$type='index';
elseif (isset($_GET['list']))//商品列表页面
	$type='lst';
elseif (isset($_GET['id']))//商品详情页面
    $type='detail';
elseif (isset($_GET['raiders']))//全球代购
	$type='raiders';
elseif (isset($_GET['giveaway']))//赠品专区
	$type='giveaway';
elseif (isset($_GET['euIndex']))//欧团知识首页
	$type='euIndex';
elseif (isset($_GET['developer']))//欧团品牌案例页
	$type='developer';

define("ACTION", "EugroupController.".$type);
$act = explode("." , ACTION);

if (!is_file(CLASS_DIR .'/'. $act[0] . '.class.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR .'/'.$act[0].".class.php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();