<?php
require( 'conf/config.inc.php' );
if (empty($_GET))
	$type='index';
elseif (isset($_GET['parameter']))
	$type='parameter';
elseif (isset($_GET['standard']))
	$type='standard';
elseif (isset($_GET['advantage']))
	$type='advantage';
elseif (isset($_GET['summary']))
	$type='summary';
elseif (isset($_GET['principle']))
	$type='principle';
elseif (isset($_GET['service']))
	$type='service';
else
    $type='error';


define("ACTION", "test.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>
