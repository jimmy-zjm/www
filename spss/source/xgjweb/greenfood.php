<?php
require( 'conf/config.inc.php' );
if (empty($_GET))
	$type='greenfoodindex';
elseif(isset($_GET['cat_id']))
	$type='foodslist';
elseif(isset($_GET['goods_id']))
	$type='foodsinfo';
elseif(isset($_GET['know']))
	$type='know';
elseif(isset($_GET['strategy']))
	$type='strategy';
define("ACTION", "greenfood.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>