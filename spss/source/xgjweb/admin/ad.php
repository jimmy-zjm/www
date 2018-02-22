<?php
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");
if (empty($_GET)|| isset($_GET['p']))
	$type='ad_list';
elseif (isset($_GET['tab']))
	$type='ad_list';
elseif (isset($_GET['nav_add']))
	$type='nav_add';
elseif (isset($_GET['nav_add_save']))
	$type='nav_add_save';
elseif (isset($_GET['nav_edit']))
	$type='nav_edit';
elseif (isset($_GET['nav_edit_save']))
	$type='nav_edit_save';
elseif (isset($_GET['nav_del']))
	$type='nav_del';
elseif (isset($_GET['custom_add']))
	$type='custom_add';
elseif (isset($_GET['custom_add_save']))
	$type='custom_add_save';
elseif (isset($_GET['custom_edit']))
	$type='custom_edit';
elseif (isset($_GET['custom_edit_save']))
	$type='custom_edit_save';
elseif (isset($_GET['custom_del']))
	$type='custom_del';

define("ACTION", "ad.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

