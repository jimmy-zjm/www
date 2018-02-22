<?php
require( 'conf/config.inc.php' );
$type='';
if (empty($_GET))
	$type='dealer_login';
elseif (isset($_GET['login']))
	$type='login';
elseif (isset($_GET['order']))
    $type='order';
elseif (isset($_GET['password']))
    $type='password';
elseif (isset($_GET['apass']))
    $type='apass';
elseif (isset($_GET['center']))
    $type='center';
elseif (isset($_GET['aftersale']))
    $type='aftersale';
elseif (isset($_GET['account']))
    $type='account';
elseif (isset($_GET['advice']))
    $type='advice';
elseif (isset($_GET['answerAdvice']))
$type='answerAdvice';
elseif (isset($_GET['center_info']))
    $type='center_info';
elseif (isset($_GET['downLoad']))
$type='downLoad';
elseif (isset($_GET['detailDownLoad']))
$type='detailDownLoad';
elseif (isset($_GET['orderSearch']))
$type='orderSearch';
elseif (isset($_GET['orderDetail']))
$type='orderDetail';
elseif (isset($_GET['fuCaiList']))
$type='fuCaiList';
elseif (isset($_GET['zhuCaiList']))
$type='zhuCaiList';
elseif (isset($_GET['insertReplenish']))
$type='insertReplenish';
elseif (isset($_GET['addReplenish']))
$type='addReplenish';
elseif (isset($_GET['addRefund']))
$type='addRefund';
elseif (isset($_GET['addReplenishZhu']))
$type='addReplenishZhu';
elseif (isset($_GET['addRefundZhu']))
$type='addRefundZhu';
elseif (isset($_GET['selfBuy']))
$type='selfBuy';
elseif (isset($_GET['editPlan']))
$type='editPlan';
elseif (isset($_GET['editCheck']))
$type='editCheck';
elseif (isset($_GET['addPlan']))
$type='addPlan';
elseif (isset($_GET['uploadFile']))
$type='uploadFile';
elseif (isset($_GET['advancedSearch']))
$type='advancedSearch';
elseif (isset($_GET['editOrder']))
    $type='adjust_order';
elseif (isset($_GET['doEditOrder']))
    $type='do_adjust_order';
elseif (isset($_GET['chooseWork']))
$type='chooseWork';
elseif (isset($_GET['logOut']))
$type='logOut';
elseif (isset($_GET['getMoney']))
$type='getMoney';
elseif (isset($_GET['getaccount']))
$type='getaccount';
elseif (isset($_GET['aftersaleAjax']))
$type='aftersaleAjax';
elseif (isset($_GET['aftersaleAjaxPage']))
$type='aftersaleAjaxPage';
elseif (isset($_GET['aftersaleInfo']))
$type='aftersaleInfo';


define("ACTION", "dealerController.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>
