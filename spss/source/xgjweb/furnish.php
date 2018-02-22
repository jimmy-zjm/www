<?php
require( 'conf/config.inc.php' );
/*if (empty($_GET))
	$type='furnishindex';
else*/if (isset($_GET['strategy']))
	$type='strategy';
// elseif (isset($_GET['cat_id']))
// 	$type='category'; //可以删
elseif (isset($_GET['cate_id']))
	$type='get_price';
elseif (isset($_GET['quote_id']))
	$type='quote';
elseif (isset($_GET['quote1']))
	$type='quote1';
elseif (isset($_GET['aid']))
	$type='area_select';
elseif (isset($_GET['pre_order']))
	$type='pre_order';
elseif (isset($_GET['pre_order_list']))
	$type='pre_order_list';
elseif (isset($_GET['key']))
	$type='del';
elseif (isset($_GET['content']))
	$type='content';
elseif (isset($_GET['id']))
	$type='furnishList';
else 
	$type='error';
	
define("ACTION", "furnish.".$type);
$act = explode("." , ACTION);

if (!is_file(CLASS_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>