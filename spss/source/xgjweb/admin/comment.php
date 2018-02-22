<?php
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");
if (isset($_GET['furnish']) || isset($_GET['p']))
	$type='furnish_comment_list';
elseif (isset($_GET['info']))
	$type='comment_info';
elseif (isset($_GET['change_status']))
    $type='comment_change_status';
elseif (isset($_GET['del_status']))
	$type='comment_del_status';
elseif (isset($_GET['edit_save']))
    $type='comment_edit_save';
elseif (isset($_GET['del']))
	$type='comment_del';	

define("ACTION", "comment.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>