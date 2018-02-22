<?php
/*
网站配置文件
 */
return array(
	'LAYOUT_ON'=>true,
	'LAYOUT_NAME'=>'layout',
	//用户注册加密
    'MD5_PASSWORD'=>'*asw94s4q40-325StjaKF>>?M;{|',
    //平板报价用户密文
    'MD5_PAD_PSD'=>'@%$@#$^&g15yi>}|{+——5~M4KK5L5KI?',
    'IMG_maxSize'           => '300M',
    'IMG_exts'                => array('jpg','gif','jpeg','png','pjpeg','bmp',),
	'DEALER_img'          =>array('jpg','gif','jpeg','png','pjpeg','dwg','doc','wps','xls','ppt','txt','rar','htm','pdf','exe','swf','mp4','avi'),
    'IMG_rootPath'          => './Public/Uploads/',
     //个人中心头像
    'IMG_THUMB_FACE' => array(
            array(240,240),
    ),


    /* 邮箱配置 */
    'MAIL_FROM'     =>'xue.liang@365xgj.com',   //发送邮件的邮箱
    'MAIL_FROMNAME' =>'上品上生',               //发件人昵称或姓名
    'MAIL_HOST'     =>'mail.365xgj.com',        //设置SMTP服务器
    'MAIL_NAME'     =>'xue.liang@365xgj.com',   //邮箱用户名
    'MAIL_PASS'     =>'xgj@10050',              //邮箱密码
    'MAIL_SMTPAUTH' =>'true',                   //SMTP服务器是否需要验证，true为需要，false为不需要

    //需要跳转登录页面的URL
    //如果里面数组内第一个是all那么这个控制器下面所有的方法都会跳转登录页面
    'IS_LOGIN' => array(

        'Cart'     =>array('index',),
        'Order'    =>array('index','process'),
        'Customer' =>array('maintain','addCart'),
        'User'     =>array('index','address','info','changePsd','concern','actcou','actCoulist','coupon','integral','integralin','integralout','homeOrder','homeOrderShow','orderfildown','hEvaluateList','hEvaluate','dohEvaluate','hEvaluation','euOrderList','theGoods','cancel','delOrder','euOrder','euEvaluateList','euEvaluate','addEuEvaluate','euEvaluation','AddCart','maintain','upkeep','consumable','wxOrder','wxOrderdetails','wxProcess','wxLook','wxListdetails'),
        ),

    //当前页面不保存在URL内的方法名
    'URL_NO_RECORD' =>array('doLogin','register','doRegister','changePass','login','verify'),

    //文章类别ID
    'HELP_CENTER_CAT_ID'=> 1,//帮助中心类别ID
    'AFTER_SERVICE_CAT_ID'=> 2,//售后服务类别ID
    //文章ID
    'BUY_GUIDE_ACTIVE_ID'=>52,//购物指南ID
	'BUY_FLOW_ACTIVE_ID'=>53,//购物流程ID
    'PAYMENT_METHOD_ACTIVE_ID'=>55,//支付方式ID
    'DISTRIBUTION_POLICY_ACTIVE_ID'=>139,//配送政策ID
    'AFTER_SERVICE_ACTIVE_ONE'=>9,//公司简介ID
    'AFTER_SERVICE_ACTIVE_TWO'=>10,//联系我们ID
    'AFTER_SERVICE_ACTIVE_THREE'=>11,//加入我们ID
);