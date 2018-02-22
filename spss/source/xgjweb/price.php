<?php
require( 'conf/config.inc.php' );

$type='get_price';



if (isset($_GET['xiazai'])) {
	define("ACTION", "pricesController.".$type);
}else if (isset($_GET['padPrice'])) {
	define("ACTION", "padPriceController.".$type);
}else{
	define("ACTION", "priceController.".$type);
}
	

$act = explode("." , ACTION);

if (!is_file(CLASS_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();
	

?>