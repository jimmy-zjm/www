<?php
return array(
    //'配置项'=>'配置值'
    'CONTROLLER_LEVEL'      =>  2,//控制器层级,
	'DEFAULT_CONTROLLER'    =>  'Index/Index', // 默认控制器名称
    //用户注册加密
    'MD5_PASSWORD'=>'*asw94s4q40-325StjaKF>>?M;{|',
    //平板报价用户密文
    'MD5_PAD_PSD'=>'@%$@#$^&g15yi>}|{+——5~M4KK5L5KI?',
    /************欧洲团代购 & 健康绿色食品************/

    /*分页设置*/
    'BRAND_PAGE_SIZE'   => 15,//品牌分页大小
    'TYPE_PAGE_SIZE'    => 15,//商品类型分页大小
    'ATTR_PAGE_SIZE'    => 15,//类型属性分页大小
    'GOODS_PAGE_SIZE'   => 8, //商品分页大小
    'IMAGE_PAGE_SIZE'   => 24,//图片空间分页大小
    'MANAGER_PAGE_SIZE' => 10,//后台管理员分页大小
    'AD_PAGE_SIZE'      => 10,//后台广告分页大小
    'ADPOS_PAGE_SIZE'   => 10,//后台广告位置分页大小
    'Article_PAGE_SIZE' => 10, //后台文章分页大小
    'VIDEO_PAGE_SIZE'   => 5,  //后台视频分页大小
    'ORDER_PAGE_SIZE'   => 5,   //订单分页大小

    /*商品设置*/
    'GOODS_SN_PREFIX'   => 'XGJ',// 商品编号前缀
    
    /************健康舒适家居************/
    /*分页*/
    'FURNISH_CAT_PAGE_SIZE'     =>15 ,//产品分类（非无限极）
    'FURNISH_GOODS_PAGE_SIZE'   =>15 , //商品分页大小
    'FURNISH_GOODSPUTAWAY_PAGE_SIZE'   =>15 , //商品分页大小
    'FURNISH_BRANG_PAGE_SIZE'   =>15 ,  //产品品牌分页显示条数
    'FURNISH_ORDER_PAGE_SIZE'   =>15 ,  //产品订单分页显示条数
    'FURNISH_COMMENT_PAGE_SIZE' =>15,  //健康舒适家居评论
    'CBRAND_PAGE_SIZE'=>15,//合作品牌显示条数
    'CBRANGINFO_PAGE_SIZE'=>15,//品牌应用显示条数

    /************服务商配置************/
    /*分页*/
    'DEALER_PAGE_SIZE'      =>15,//服务商页面
    'FURNISH_FINANCE_PAGE_SIZE'   =>15, //结算申请
    'FURNISH_FINANCE_INFO_PAGE_SIZE'=>15,//結算詳情
    'FURNISH_FINANCE_LOG_PAGE_SIZE' =>15,//結算歷史
    'FURNISH_FINANCE_REFUND_PAGE_SIZE'=>15, //结算退换货伙伴自购
    'DEALER_WITHDRAW_PAGE_SIZE'     =>15,   //提现管理分页大小
    /*广告设置*/
    //广告类型列表
    'AD_TYPE_LIST'=>array(
        1 => '文字广告',
        2 => '图片广告',
        3 => '视屏广告',
    ),

    /*其他设置*/
    'MD5_KEY'           => '#03294!*dka(0324!+d_f-no',//后台登陆的 md5混淆key, 不能随意修改, 否则将无法登陆
	    /**************************自定义配置*********************************/

    'IMG_maxSize'           => '300M',
    'IMG_exts'              => array('jpg','gif','jpeg','png','pjpeg','bmp',),
    'IMG_rootPath'          => './Public/Uploads/',
    'FILE_exts'             => array('jpg','gif','jpeg','png','pjpeg','bmp','dwg','doc','wps','xls','xlsx','ppt','txt','rar','htm','pdf','exe','swf','mp4','avi','zip'),
    'VIDEO_exts'            => array('flv','swf','mkv','avi','rm','rmvb','mpeg','mpg','ogg','ogv','mov','wmv','mp4','webm','mp3','wav','mid','dwg'),
    'Pad_rootPath'=>dirname(__ROOT__)."/offlin/Public/Uploads/",
    'Pads_rootPath'=>"../offlin/Public/Uploads/",
    //商品封面图片的缩略图尺寸
    'IMG_THUMB_FACE' => array(
            array(54,54),
            array(100,100),
            array(160,160),
            array(220,220),
            array(350,350),
            array(340,400),
     ),

    //商品相册图片的缩略图尺寸
    'IMG_THUMB' => array(
            array(54,54),
            array(350,350),
     ),

    // 品牌缩略图
    'IMG_THUMB_BRAND'=>array(
            array(102,36),
    ),
    //合作品牌缩略图
    'IMG_THUMB_CBRAND'=>array(
            array(115,95),
    ),
	//经销商地图
	'DEALER_D_MAP'=>array(
    		array(200,200),
    )
);