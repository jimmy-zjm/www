<?php
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");
if (empty($_GET) || isset($_GET['p']) || isset($_GET['district']))
	$type='dealer_list';
elseif (isset($_GET['add']))
	$type='dealer_add';
elseif (isset($_GET['add_save']))
    $type='dealer_add_save';
elseif (isset($_GET['edit']))
	$type='dealer_edit';
elseif (isset($_GET['edit_save']))
    $type='dealer_edit_save';
elseif (isset($_GET['del']))
	$type='dealer_del';	
elseif (isset($_GET['info']))
	$type='dealer_info';

define("ACTION", "dealer.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>