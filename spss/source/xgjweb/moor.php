<?php
/**
 * 7moor接口对接
 * @date 2016-10-25
 */
require './libs/common/initialize.php';

if (empty($_GET))
    $type='index';
elseif (isset($_GET['originCallNo']))
    $type='originCallNo';
elseif (isset($_GET['name']))
    $type='name';
elseif (isset($_GET['qimoClientId']))
    $type='qimoClientId';
else
    $type='index';



define("ACTION", "MoorController.".$type);
$act = explode("." , ACTION);

if (!is_file(CLASS_DIR .'/'. $act[0] . '.class.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR .'/'.$act[0].".class.php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();