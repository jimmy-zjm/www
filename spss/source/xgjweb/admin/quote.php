<?php
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");


if (empty($_GET) || isset($_GET['p']))
	$type='quote_list';
elseif (isset($_GET['add']))
	$type='quote_add';
elseif (isset($_GET['add_save']))
    $type='quote_add_save';
elseif (isset($_GET['edit']))
	$type='quote_edit';
elseif (isset($_GET['edit_save']))
    $type='quote_edit_save';
elseif (isset($_GET['del']))
	$type='quote_del';
elseif (isset($_GET['goods_sn']))
	$type='goods_sn';
elseif (isset($_GET['level']) && isset($_GET['quote_id']))
	$type='quote_ini_list';
elseif (isset($_GET['keyword']))
	$type='quote_ini_list';
elseif (isset($_GET['child_list_add']))
	$type='child_list_add';
elseif (isset($_GET['child_list_del']))
	$type='child_list_del';
elseif (isset($_GET['child_list_change']))
	$type='child_list_change';	
elseif (isset($_GET['parent_formula']))
	$type='parent_formula';	


define("ACTION", "quote.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();