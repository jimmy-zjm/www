<?php
return array(
	//'配置项'=>'配置值'
    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '192.168.100.238', // 服务器地址
    'DB_NAME'               =>  'nyy',          // 数据库名
    'DB_USER'               =>  'xgj',      // 用户名
    'DB_PWD'                =>  'xgj',      // 密码
    'DB_PORT'               =>  '',         // 端口
    'DB_PREFIX'             =>  '',         // 数据库表前缀
    'DB_PARAMS'             =>  array(),    // 数据库连接参数
    'DB_DEBUG'              =>  TRUE,       // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  true,       // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',     // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        =>  0,          // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        =>  false,      // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         =>  1,          // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           =>  '',         // 指定从服务器序号
    /*模板引擎设置*/
    'TMPL_DENY_PHP'         =>  false, // 默认模板引擎是否禁用PHP原生代码
    'TMPL_L_DELIM'          =>  '<{',            // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'          =>  '}>',           // 模板引擎普通标签结束标记

    /*url设置*/
    'URL_CASE_INSENSITIVE'  =>  true,       // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             =>  1,          // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式

    /*调试配置*/
    'SHOW_PAGE_TRACE'       => false,

    /*控制器设置*/
    'CONTROLLER_LEVEL'      =>  1,//控制器层级,

    /*默认设置*/
    //'MODULE_ALLOW_LIST'    =>    array('Home','Admin','User'),
   // 'DEFAULT_MODULE'        =>  'Admin',  // 默认模块, 开发完毕,将这条删除
    'DEFAULT_FILTER'        =>  'strip_tags,htmlspecialchars,trim', // 默认参数过滤方法 用于I函数...加入了trim过滤方法
    'DEFAULT_ACTION'        =>  'index', 
    'APP_USE_NAMESPACE'     =>  true,
    'AUTOLOAD_NAMESPACE' => array(
        'Extend'     => APP_PATH.'Extend',
    ),

	'LAYOUT_ON'=>true,
	'LAYOUT_NAME'=>'layout',



    'TP_APP_URL'=>'http://www.b.com/xgjtp',
	'SPSS_URL' =>'http://www.b.com/spss',
    //需要跳转登录页面的URL
    //如果里面数组内第一个是all那么这个控制器下面所有的方法都会跳转登录页面
    // 'IS_LOGIN' => array(

    //     'Index'     =>array('index',),
    //     'Order'    =>array('index','process'),
    //     'Customer' =>array('maintain','addCart'),
    //     'User'     =>array('index','address','info','changePsd','concern','actcou','actCoulist','coupon','integral','integralin','integralout','homeOrder','homeOrderShow','orderfildown','hEvaluateList','hEvaluate','dohEvaluate','hEvaluation','euOrderList','theGoods','cancel','delOrder','euOrder','euEvaluateList','euEvaluate','addEuEvaluate','euEvaluation','AddCart','maintain','upkeep','consumable'),
    //     ),
         
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


    'Staff_PAGE_SIZE' => 10, //Staff控制器分页数
    'Order_PAGE_SIZE' => 5, //Order控制器订单分页数
    'Order_LIST_PAGE_SIZE' => 10, //Order控制器清单分页数
    /**************************自定义配置*********************************/
);