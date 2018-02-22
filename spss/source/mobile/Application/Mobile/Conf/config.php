<?php
/*
网站配置文件
 */
return array(
	// 'LAYOUT_ON'=>true,
	// 'LAYOUT_NAME'=>'layout',
	//用户注册加密
    'MD5_PASSWORD'=>'*asw94s4q40-325StjaKF>>?M;{|',
    'IMG_maxSize'           => '300M',
    'IMG_exts'                => array('jpg','gif','jpeg','png','pjpeg','bmp',),
	'DEALER_img'          =>array('jpg','gif','jpeg','png','pjpeg','dwg','doc','wps','xls','ppt','txt','rar','htm','pdf','exe','swf','mp4','avi'),
    'IMG_rootPath'          => './Public/Uploads/',
     //个人中心头像
    'IMG_THUMB_FACE' => array(
            array(240,240),
    ),

    //需要跳转登录页面的URL
    //如果里面数组内第一个是all那么这个控制器下面所有的方法都会跳转登录页面
    'IS_LOGIN' => array(
        'Order'    =>array('index','pay'),
        'User'     =>array('find',),
        'Furnish'  =>array('info',),
        ),

);