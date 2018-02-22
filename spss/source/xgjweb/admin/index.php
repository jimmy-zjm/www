<?php
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");
if (empty($_GET))
	$type='admin_index';
elseif (isset($_GET['top']))
	$type='admin_top';
elseif (isset($_GET['left']))
	$type='admin_left';
elseif (isset($_GET['right']))
	$type='admin_right';
elseif (isset($_GET['footer']))
	$type='admin_footer';
elseif (isset($_GET['login']))
	$type='admin_login';
elseif (isset($_GET['doLogin']))
    $type='doLogin';
elseif (isset($_GET['furnish_goods']))
	$type='furnish_goods';
elseif (isset($_GET['logout']))
	$type='logout';	
elseif (isset($_GET['permission']))
	$type='permission_list';
elseif (isset($_GET['add']))
	$type='permission_add';
elseif (isset($_GET['add_save']))
	$type='permission_add_save';
elseif (isset($_GET['edit']))
	$type='permission_edit';
elseif (isset($_GET['edit_save']))
	$type='permission_edit_save';
elseif (isset($_GET['info']))
	$type='permission_info';
elseif (isset($_GET['info_save']))
	$type='permission_info_save';
elseif (isset($_GET['del']))
	$type='permission_del';

define("ACTION", "admin_user.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>