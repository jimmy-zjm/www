<?php
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");
if (empty($_GET) || isset($_GET['p']))
	$type='eu_recommend_list';
elseif (isset($_GET['add']))
	$type='eu_recommend_add';
elseif (isset($_GET['add_save']))
    $type='eu_recommend_add_save';
elseif (isset($_GET['edit']))
	$type='eu_recommend_edit';
elseif (isset($_GET['edit_save']))
    $type='eu_recommend_edit_save';
elseif (isset($_GET['del']))
	$type='eu_recommend_del';
elseif (isset($_GET['recommend_list_add']))
	$type='recommend_list_add';
elseif (isset($_GET['recommend_list_del']))
	$type='recommend_list_del';

define("ACTION", "eu_recommend.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();