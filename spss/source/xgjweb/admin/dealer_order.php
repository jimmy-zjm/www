<?php
//header("Content-type:text/html;charset=utf-8");
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");
if (empty($_GET) || isset($_GET['p']) || isset($_GET['order_code']) || isset($_GET['tab']))
	$type='dealer_order_list';
elseif (isset($_GET['allot']))
	$type='dealer_order_allot';
elseif (isset($_GET['order_statistics']))
    $type='dealer_order_statistics';
elseif (isset($_GET['edit']))
	$type='dealer_order_edit';
elseif (isset($_GET['edit_save']))
    $type='dealer_order_edit_save';
elseif (isset($_GET['del']))
	$type='dealer_order_del';	
elseif (isset($_GET['info']))
	$type='dealer_order_info';
elseif (isset($_GET['stuff_list']))
	$type='dealer_order_stuff_list';
elseif (isset($_GET['construct_check']))
	$type='dealer_order_construct_check';
elseif (isset($_GET['down']))
	$type='dealer_order_down';
elseif (isset($_GET['file']))
	$type='dealer_order_file';
elseif (isset($_GET['adjust']))
	$type='dealer_order_adjust';
elseif (isset($_GET['audit']))
	$type='dealer_order_audit';

define("ACTION", "dealer_order.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>