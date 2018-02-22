<?php
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");
if (empty($_GET) || isset($_GET['p']) || isset($_GET['order_code']) || isset($_GET['tab']))
	$type='finance_list';
elseif (isset($_GET['info']))
	$type='finance_info';
elseif (isset($_GET['msg']))
	$type='finance_msg';
elseif (isset($_GET['pay']))
    $type='finance_pay';
elseif (isset($_GET['log']))
	$type='finance_log';
elseif (isset($_GET['refund_info']))
    $type='finance_refund_info';
elseif (isset($_GET['back']))
	$type='finance_back';	


define("ACTION", "finance.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>