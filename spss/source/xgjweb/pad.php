<?php
require( 'conf/config.inc.php' );

if(empty($_GET) || isset($_GET['login']))//默认为 登录
    $type = 'login';
elseif (isset($_GET['manage']))//客户管理
    $type = 'manage';
elseif (isset($_GET['index']))//选择系统
    $type = 'index';
elseif (isset($_GET['details']))//系统详情
    $type = 'details';
elseif (isset($_GET['offer']))//产生报价页面
    $type = 'offer';
elseif (isset($_GET['setup']))//设置
    $type = 'setup';
elseif (isset($_GET['selectHouse']))//查询客户房屋信息
    $type = 'selectHouse';
elseif (isset($_GET['gotos']))//导出
    $type = 'gotos';
elseif (isset($_GET['email']))//邮件
    $type = 'email';
elseif (isset($_GET['addCustomerQuote']))//添加客户已查询系统信息意向表
    $type = 'addCustomerQuote';
elseif (isset($_GET['info']))//修改信息
    $type = 'info';
elseif (isset($_GET['doInfo']))//修改信息
    $type = 'doInfo';
elseif (isset($_GET['psd']))//修改密码
    $type = 'psd';
elseif (isset($_GET['doPsd']))//修改密码
    $type = 'doPsd';
elseif (isset($_GET['check']))//客户管理查看
    $type = 'check';
elseif (isset($_GET['checkinfo']))//客户管理查看备注信息
    $type = 'checkinfo';
elseif (isset($_GET['addRemark']))//客户管理添加备注信息
    $type = 'addRemark';
elseif (isset($_GET['checkPage']))//客户管理查看分页请求
    $type = 'checkPage';
elseif (isset($_GET['doLogin']))//登录操作
    $type = 'doLogin';
elseif (isset($_GET['managePage']))//客户管理列表分页
    $type = 'managePage';
elseif (isset($_GET['goOut']))//登录操作
    $type = 'goOut';
elseif (isset($_GET['staff']))//员工管理
    $type = 'staff';
elseif (isset($_GET['userOpen']))//设置用户子账号是否开启
    $type = 'userOpen';
elseif (isset($_GET['staffInfo']))//员工信息
    $type = 'staffInfo';
elseif (isset($_GET['staffCheck']))//员工信息查看
    $type = 'staffCheck';
elseif (isset($_GET['calDays']))//查询日
    $type = 'calDays';
elseif (isset($_GET['updateUser']))//修改用户子账号信息
    $type = 'updateUser';
elseif (isset($_GET['staffinfos']))//修改用户子账号信息
    $type = 'staffinfos';
elseif (isset($_GET['sprov']))//修改用户子账号信息
    $type = 'sprov';
elseif (isset($_GET['scity']))//修改用户子账号信息
    $type = 'scity';
elseif (isset($_GET['del']))//删除客户管理系统
    $type = 'del';    
elseif (isset($_GET['more']))//客户管理数据回填
    $type = 'more';    
elseif (isset($_GET['delSession']))//客户管理数据回填
    $type = 'delSession';     
elseif (isset($_GET['changeArea']))//三级联动
    $type = 'changeArea';     
elseif (isset($_GET['padPostArea']))//三级联动
    $type = 'padPostArea';  
elseif (isset($_GET['order']))//提交订单
    $type = 'order';    
define("ACTION", "padController.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();











