<?php
session_start();
// convert /sns/product/conf to /sns/product
//require_once (substr(dirname(__FILE__), 0, -5) . "/conf/common.inc.php");

define ("WWW_DIR" , substr(dirname(__FILE__), 0 , -5));
define('TP_APP_URL', 'http://localhost/xgj/source/xgjtp/');
define ("CLASS_DIR", WWW_DIR."/classes");//前台文件
define ("CLASS_ADMIN_DIR", WWW_DIR."/admin/classes");//后台文件
define ("EU_PIC","pictures/eugroup/upload/");//欧团图片目录
define('IS_POST', (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'));
require_once(substr(dirname(__FILE__), 0, -5) . "/libs/Smarty/Smarty.class.php");
require_once(substr(dirname(__FILE__), 0, -5) . "/libs/global.func.php");
require_once(substr(dirname(__FILE__), 0, -5) . "/libs/db.php");
define ("IMG_maxSize", '300');//图片上传大小
define ("IMG_rootPath", dirname(dirname(dirname(__FILE__))).'/xgjtp/Public/Uploads/');//图片上传路径
//图片后缀
define ("IMG_exts", "'jpg','gif','jpeg','png','pjpeg','dwg','doc','wps','xls','ppt','txt','rar','htm','pdf','exe','swf','mp4','avi'");
     //'IMG_exts'              => array('jpg','gif','jpeg','png','pjpeg','bmp',),
    // 'IMG_rootPath'          => ,
    // 'FILE_exts'             => array('jpg','gif','jpeg','png','pjpeg','bmp','dwg','doc','wps','xls','ppt','txt','rar','htm','pdf','exe','swf','mp4','avi'),
    // 'VIDEO_exts'            => array('flv','swf','mkv','avi','rm','rmvb','mpeg','mpg','ogg','ogv','mov','wmv','mp4','webm','mp3','wav','mid','dwg')
//定义数据库常量
define('DB_NAME', 'nyy');
define('DB_HOST','192.168.100.238');
define('DB_USER', 'xgj');
define('DB_PASSWORD','xgj');
define('DB_CHARSET','utf8');
define('BASE_DIR','');
define('MD5_PASSWORD', '*asw94s4q40-325StjaKF>>?M;{|');
define('MD5_PAD_PSD', '@%$@#$^&g15yi>}|{+——5~M4KK5L5KI?');
function get_smarty()
{
	$tpl = new Smarty();
	$tpl->settemplatedir(WWW_DIR ."/templates/");
	$tpl->setCompileDir(WWW_DIR ."/templates_c/");
	$tpl->setConfigDir(WWW_DIR . "/conf/Smarty/");
	$tpl->left_delimiter	= '{:';
	$tpl->right_delimiter	= ':}';
	$tpl->debugging   = false;

	return $tpl;
}

function get_admin_smarty()
{
	$tpl = new Smarty();
	$tpl->settemplatedir(WWW_DIR ."/admin/templates/");
	$tpl->setCompileDir(WWW_DIR ."/admin/templates_c/");
	$tpl->left_delimiter	= '{:';
	$tpl->right_delimiter	= ':}';
	$tpl->debugging   =   false;

	return $tpl;
}


?>


