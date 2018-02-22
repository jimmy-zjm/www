<?php
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");
if (empty($_GET))
	$type='eu_cat_list';
elseif (isset($_GET['cat_add']))
	$type='eu_cat_add';
elseif (isset($_GET['cat_add_save']))
    $type='eu_cat_add_save';
elseif (isset($_GET['cat_edit']))
	$type='eu_cat_edit';
elseif (isset($_GET['cat_edit_save']))
    $type='eu_cat_edit_save';
elseif (isset($_GET['cat_del']))
	$type='eu_cat_del';

define("ACTION", "eu_cat.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();