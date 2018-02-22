<?php
require( 'conf/config.inc.php' );
if (isset($_GET['add_cart']))
	$type='add_cart';
elseif ( isset($_GET['del'])  && isset($_GET['cart_id']))
	$type='del';
elseif ( isset($_GET['cartlist']))
	$type='cartlist';
elseif (isset($_GET['homebill']) || isset($_GET['p']))
     $type="homebill";
elseif (isset($_GET['delAll']))
	$type='delAll';

elseif ( isset($_GET['showPayPage']))
$type = 'showPayPage';
elseif ( isset($_GET['addrDefaultSet']))
$type = 'addrDefaultSet';
elseif ( isset($_GET['addrInfoAddShow']))
$type = 'addrInfoAddShow';
elseif ( isset($_GET['doAddrInfoAdd']))
$type = 'doAddrInfoAdd';
elseif ( isset($_GET['addrInfoEditShow']))
$type = 'addrInfoEditShow';
elseif ( isset($_GET['doAddrInfoEdit']))
$type = 'doAddrInfoEdit';
elseif ( isset($_GET['addrInfoDel']))
$type = 'addrInfoDel';
elseif ( isset($_GET['cartBack']))
$type = 'cartBack';
elseif ( isset($_GET['addConcern']))
$type = 'addConcern';

define("ACTION", "FurnishCartController.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>