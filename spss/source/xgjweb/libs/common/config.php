<?php
/*
网站配置文件
 */
return array(
    
    /*临时配置*/
    'TP_APP_URL'                 =>  'http://localhost/xgj/source/xgjtp/',//TP应用的url
    
    /*网站配置*/
    'WWW_URL'                    =>  'http://localhost/xgj/source/xgjweb/',
    
    /*数据库配置*/
    'DB_HOST'                    =>  '192.168.100.238', // 服务器地址
    'DB_NAME'                    =>  'nyy',          // 数据库名
    'DB_USER'                    =>  'xgj',          // 用户名
    'DB_PWD'                     =>  'xgj',          // 密码
    'DB_PREFIX'                  =>  '',             // 数据库表前缀
    'DB_CHARSET'                 =>  'UTF8',         // 数据库表前缀
    
    /*调试模式配置*/
    'APP_DEBUG'                  =>  true,//是否开启调试模式
    
    /*默认设置*/
    'DEFAULT_FILTER'             =>  'htmlspecialchars,trim,addslashes', // 默认参数过滤方法 用于I函数...加入了trim过滤方法
    
    /*模板引擎设置*/
    'TMPL_L_DELIM'               =>  '{:',            // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'               =>  ':}',            // 模板引擎普通标签结束标记
    
    /*支付宝信息配置*/
  /*  'AP_SELLER_EMAIL'            =>  'caiwu@365xgj.com',//收款的支付宝账号
    'AP_PARTNER'                 =>  '2088121869660227',//合作身份者id，以2088开头的16位纯数字
    'AP_KEY'                     =>  '6gwyu13oo666p6cl0b7cdxz0vsb1v04f',//安全检验码，以数字和字母组成的32位字符*/
	'AP_SELLER_EMAIL'            =>  'caiwu@myspss.com',//收款的支付宝账号
    'AP_PARTNER'                 =>  '2088421472310701',//合作身份者id，以2088开头的16位纯数字
    'AP_KEY'                     =>  'cigndi2hwd7be75ulcoqoe5qswbnmmw3',//安全检验码，以数字和字母组成的32位字符
    'AP_ORDER_NAME'              =>  '上品上生订单支付',//订单名称
    'AP_ORDER_DESC'              =>  '订单描述',//订单描述
    
    
    /*欧洲母婴配置*/
    'BABY_INDEX_BANNER_ADPOS_ID' =>  27,//首页banner广告位子id
    'BABY_LIST_BANNER_ADPOS_ID'  =>  31,//列表页banner广告位子id
    /*母婴首页楼层配置*/
    'BABY_CATE_ID2'              =>  37,//2F展示的分类id
    'BABY_CATE_ID3'              =>  47,//3F展示的分类id
    'BABY_CATE_ID4'              =>  37,//4F展示的分类id
    'BABY_CATE_ID5'              =>  47,//5F展示的分类id
    'BABY_CATE_ID6'              =>  37,//6F展示的分类id
    'BABY_CATE_ID7'              =>  47,//7F展示的分类id
    'BABY_CATE_ID8'              =>  37,//8F展示的分类id
    'BABY_CATE_ID9'              =>  47,//9F展示的分类id
    
    
    /*欧团配置*/
    'START_BUY_TIME'             =>  10,//开始抢购时间, 早上10点
    'EU_CATE_ID1'                =>  100,//1F展示的分类id
    'EU_CATE_ID2'                =>  101,//2F展示的分类id
    'EU_CATE_ID3'                =>  60,//3F展示的分类id
    
    
    /*前台分页配置*/
    'DETAIL_COMMENT_PAGE_SIZE'   =>  3,//商品详情页面评论分页大小
    
    /*页面配置*/
    'DEALER_PAGE_SIZE'           =>  12,//经销商展示页面分页大小
    /*欧团首页广告位置id*/
    'EU_AD_ID'                   =>2,
    );