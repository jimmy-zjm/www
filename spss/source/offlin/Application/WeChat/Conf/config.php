<?php
/*
网站配置文件
 */
return array(
    'LAYOUT_ON'=>false,
	'DEFAULT_CONTROLLER' => 'User', // 默认控制器名称
	'DEFAULT_ACTION' => 'index', // 默认操作名称
    //当前页面不保存在URL内的方法名
    'URL_NO_RECORD' =>array('doLogin','register','doRegister','changePass','login','verify'),
);