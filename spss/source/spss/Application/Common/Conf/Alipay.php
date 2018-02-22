<?php
return array(
	'alipay'=>array(
		//合作身份者id，以2088开头的16位纯数字
		'partner'		=>'2088421472310701',

		//收款支付宝账号，一般情况下收款账号就是签约账号
		'seller_email'		=>'caiwu@myspss.com',
		'seller_id'		=>'2088421472310701',

		//安全检验码，以数字和字母组成的32位字符
		'key'		=>'cigndi2hwd7be75ulcoqoe5qswbnmmw3',


		//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


		//签名方式 不需修改
		'sign_type'		=>strtoupper('MD5'),

		'input_charset'		=>strtolower('utf-8'),

		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		'cacert'		=>getcwd()."/Application/Home/Conf/cacert.pem",

		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		'transport'		=>'http',
		),
	);

/*支付宝信息配置*/

    