<?php
/*
初始化文件
 */
header('content-type:text/html;charset=utf-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

session_start();
date_default_timezone_set('PRC');

/*定义常量*/
define('WWW_DIR', str_replace('\\','/', dirname(dirname(dirname(__FILE__)))));
define('CLASS_DIR', WWW_DIR . '/classes');
define('IS_POST', (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'));

/*导入文件*/
$GLOBALS['conf'] = require WWW_DIR.'/libs/common/config.php';
require WWW_DIR.'/libs/common/ErrorAndException.class.php';
ErrorAndException::start(null,$GLOBALS['conf']['APP_DEBUG']);
require WWW_DIR.'/libs/common/function.php';
require WWW_DIR.'/libs/common/Mysql.class.php';
require WWW_DIR.'/libs/common/Model.class.php';
require WWW_DIR.'/libs/Smarty/Smarty.class.php';
require WWW_DIR.'/libs/common/Controller.class.php';