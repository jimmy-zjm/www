<?php
require( 'conf/config.inc.php' );
if (empty($_GET))
	$type='index';
elseif (isset($_GET['pay']))
	$type='pay';
elseif (isset($_GET['pay_detail']))
	$type='pay_detail';
elseif (isset($_GET['show']))
	$type='show';
elseif (isset($_GET['authorization']))
	$type='authorization';
elseif (isset($_GET['knowledge']))
	$type='knowledge';
elseif (isset($_GET['aboutus']))
	$type='aboutus';
elseif (isset($_GET['service']))
	$type='service';
elseif (isset($_GET['addressInfo']))//获取地址信息
    $type='addressInfo';
elseif (isset($_GET['downManual']))//下载产品手册
    $type='downManual';
elseif (isset($_GET['getManualInfo']))//获取产品手册
    $type='getManualInfo';
elseif (isset($_GET['faq']))//常见问题列表
	$type='faq';
elseif (isset($_GET['article']))//常见问题详情
	$type='article';
elseif (isset($_GET['repair']))//在线报修页面
    $type='repair';
elseif (isset($_GET['saveProblem']))//提交在线报修问题
    $type='saveProblem';
elseif (isset($_GET['service_state']))//服务状态
    $type='service_state';
elseif (isset($_GET['cbrandInfo']))//合作品牌详情页
    $type='cbrandInfo';
elseif (isset($_GET['ajaxCbrand']))//合作品牌ajax分页
    $type='ajaxCbrand';
elseif (isset($_GET['fedback']))//更多反馈
    $type='fedback';
elseif (isset($_GET['developer']))//合作商案例
    $type='developer';
elseif (isset($_GET['aid']))
	$type='area_select';
elseif (isset($_GET['video']))
	$type='video';
elseif (isset($_GET['videoinfo']))
    $type='videoinfo';
elseif (isset($_GET['add']))
	$type='add';
elseif (isset($_GET['homeindex']))
    $type='homeindex';
elseif (isset($_GET['share']))
    $type='share';
elseif (isset($_GET['qualitylife']))
    $type='qualitylife';
elseif (isset($_GET['integral']))
    $type='integral';

elseif (isset($_GET['quick']))
    $type='quick';
elseif (isset($_GET['contactus']))
    $type='contactus';
elseif (isset($_GET['joblist']))
    $type='joblist';
elseif (isset($_GET['join']))
    $type='join';
elseif (isset($_GET['cooperationlist']))
    $type='cooperationlist';
elseif (isset($_GET['jobinfo']))
    $type='jobinfo';
elseif (isset($_GET['cooperationinfo']))
    $type='cooperationinfo';
elseif (isset($_GET['province']))
    $type='province';
elseif (isset($_GET['join_add']))
    $type='join_add';
elseif (isset($_GET['furniture']))//家具建材
    $type='furniture';
elseif (isset($_GET['productCont']))//家具建材
    $type='productCont';

elseif (isset($_GET['video_row']))
    $type='video_row';
elseif (isset($_GET['quetion']))
    $type='quetion';
elseif (isset($_GET['videoSrc']))
    $type='videoSrc';
else
    $type='error';


define("ACTION", "welcome.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>
