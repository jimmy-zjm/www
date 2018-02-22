<?php
require( '../conf/config.inc.php' );
require_once(WWW_DIR."/conf/mysql_db.php");

if (isset($_GET['probl'])){
	$type = 'probl';
}else if (isset($_GET['state'])){
	$type = 'state';
}else if (isset($_GET['note'])){
	$type = 'note';
}else if (isset($_GET['delete'])){
	$type = 'delete';
}else if (isset($_GET['prob_ok'])){
	$type = 'prob_ok';
}else if (isset($_GET['state_ok'])){
	$type = 'state_ok';
}else if (isset($_GET['note_ok'])){
	$type = 'note_ok';
}else if (isset($_GET['delete_ok'])){
	$type = 'delete_ok';
}else if (isset($_GET['prob_no'])){
	$type = 'prob_no';
}else if (isset($_GET['state_no'])){
	$type = 'state_no';
}else if (isset($_GET['note_no'])){
	$type = 'note_no';
}else if (isset($_GET['delete_no'])){
	$type = 'delete_no';
}

define("ACTION", "problem.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_ADMIN_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_ADMIN_DIR . "/".$act[0].".php" );

$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>