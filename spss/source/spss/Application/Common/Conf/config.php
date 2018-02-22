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
    'LOAD_EXT_CONFIG'       => 'Alipay', 
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
    'DEFAULT_MODULE'        =>  'Admin',  // 默认模块, 开发完毕,将这条删除
    'DEFAULT_FILTER'        =>  'strip_tags,htmlspecialchars,trim', // 默认参数过滤方法 用于I函数...加入了trim过滤方法
    'DEFAULT_ACTION'        =>  'index', 
    'APP_USE_NAMESPACE'     =>  true,
    'AUTOLOAD_NAMESPACE' => array(
        'Extend'     => APP_PATH.'Extend',
    ),


	'TP_APP_URL'=>'http://www.b.com/xgjtp',
    'WECHAT_URL' =>'http://www.b.com/offlin',

    /*支付宝信息配置*/
    'AP_ORDER_NAME'              =>  '上品上生订单支付',//订单名称
    'AP_ORDER_DESC'              =>  '订单描述',//订单描述
    'AP_PARTNER'                 =>  '2088421472310701',




    /**************************自定义配置*********************************/
);