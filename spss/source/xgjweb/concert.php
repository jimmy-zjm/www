<?php
require( 'conf/config.inc.php' );

//关注商品
if (isset($_GET['concertGoods']))
	$type='concertGoods';

define("ACTION", "concertController.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>