<?php
/**
 * ajax请求专用入口
 * @date 2016-3-16
 * @author grass <14712905@qq.com>
 */
require './libs/common/initialize.php';
/***************grass********************/
if(empty($_GET))
    $type = 'index';
elseif (isset($_GET['eu'])||isset($_GET['cid'])||isset($_GET['bid']))
    $type = 'index';
elseif (isset($_GET['k'])||isset($_GET['caid'])||isset($_GET['cuid'])||isset($_GET['brid']))
    $type = 'overseas';
elseif (isset($_GET['seachall']))
    $type = 'seachAll';

define("ACTION", "SearchController.".$type);
$act = explode("." , ACTION);

if (!is_file(CLASS_DIR .'/'. $act[0] . '.class.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR .'/'.$act[0].".class.php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();
