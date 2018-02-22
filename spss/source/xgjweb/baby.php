<?php
/**
 * 母婴入口文件
 * @date 2016-3-21
 * @author grass <14712905@qq.com>
 */
require './libs/common/initialize.php';

if (empty($_GET))
    $type='index';
elseif (isset($_GET['index']))
    $type='index';
elseif (isset($_GET['list']))
    $type='lst';
elseif (isset($_GET['detail']))
    $type='detail';
elseif (isset($_GET['brand']))
    $type='getbrand';
elseif (isset($_GET['sck']))
    $type='search';


define("ACTION", "BabyController.".$type);
$act = explode("." , ACTION);

if (!is_file(CLASS_DIR .'/'. $act[0] . '.class.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR .'/'.$act[0].".class.php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();