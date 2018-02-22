<?php
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");
if (empty($_GET)|| isset($_GET['p']))
	$type='autoputaway_list';
elseif (isset($_GET['tab']))
	$type='autoputaway_list';
elseif (isset($_GET['goods_edit_all']))
	$type='goods_edit_all';
elseif (isset($_GET['quote_edit_all']))
	$type='quote_edit_all';

define("ACTION", "autoputaway.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

