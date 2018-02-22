<?php
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");
if (empty($_GET) || isset($_GET['p']))
	$type='video_list';
elseif (isset($_GET['add']))
	$type='video_add';
elseif (isset($_GET['add_save']))
	$type='video_add_save';
elseif (isset($_GET['edit']))
	$type='video_edit';
elseif (isset($_GET['edit_save']))
	$type='video_edit_save';
elseif (isset($_GET['del']))
	$type='video_del';


define("ACTION", "video.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

