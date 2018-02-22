<?php
$config = array (	
		//应用ID,您的APPID。
        'app_id' => "2016080301701015",

        //商户私钥，您的原始格式RSA私钥
        'merchant_private_key' => "MIIEowIBAAKCAQEAxYjCFilPcBaVW4mU7mCbHAJeoGiUIlxb6/4m/6bk8blR1JvX7SsP0/iAfGe190KdgrIWpwOWpFp6/AHHq8otO2SmxmVuIkgNVL/vzOGFAeCRlSL6RG/nsFlh4MJsMGgsR132hGUnCkVWYh30WQ6x+UlVt3AaG3mZcF0tZ3GU7xErEYXimSGdNXpuJ60HjgDl4SXJ7N2D9e8PvzwFoSj2hzDyJS8jckJJ36kgdRhGGyeObxVNeblQ2mgZFf40o69HLPAlACxSrdlNQF5S/BtRtxUp/m2FSqb7puzXJIT6CJScb3KleWb9PEfBPPc4eKBHyRDg4Xjmp07pfGsyO558nwIDAQABAoIBAEnZ5WZPr4liiBifCXVN3O/N2v7x2mA2U4+Zh48RIItXE8b/NO1Qqiw/vhnE1H1LBIR2fI2Yg9uSkGRjbflGLz8DVG7cQR7D/WfwEoFw1z9qbENrvlGT3PVLlZJoXfaDG/raoG3DO6NVZQRt2orpOZTP3CIm4TvgbINb8ru39UyjX2ExxG1U/JFepciT/JdjFOzCRXl/E0wpcz4Nd5ftLFzwLf9YFW4YfBj9UZgdiqZNg/HBCpU8V/5pdh3vn8OgWtKTRmvKey6FwmlBlTXrlShktjhx3uc+mM5c9jynpt7XP5r3g8nab0ipq27sf2zXpH+Xeqh8uf2dPYlEO3AP80ECgYEA+xG0A1ej4Mh1FYkZpuyPKWpWLp9LKXwD/Fmo/ydy6iGLRanfLgajKoUhThAXGv81IdS2AMOQryZBmxtaAwzdUQjHiMhvKgLInnkFy3rGd8emAPoF0C/37GgV4lftLsuwhWkbvq5iNSl5skmFQ44qnkaEWh6fQR68XN1StISoN68CgYEAyWnl6mHNijVciMBovKS1H5Oioq6Loy6pvUnbxNNjzDk3T5T42uFaGaIwnZ3UnA4XTbSA0FAyOyhsPP999OoMd1/2cuULAhVQEnwpQdJGdqL0jjSnUou83xco/RlYlnLiuh4mRJ+yyyzvqJhL4U0ZhD0xytIRr3SlPUtDJ2OiVhECgYBge4gi9K38duxPr1UVSbkmYD/oQLzgn1DkzAveqelGQkfEKKikhlZzw59t4U8Px91CN/0NRBo4xMnXcmZf37S5LFeJoATgLxurkrSAYpkcdLO9kldjjtuGlOU8CzARmKMoUaEg4ODKrzqeIhS9RkfodQ7tlvD6VHZsdX0P9B607wKBgFtBRK894r9Uz/kRnCKkSX/mBxDIHnIvyS3/EbiKwjOcVMgUye95nLXyey5efR67b6mAN8CqJSgSd3/njizyAfXwH5wM1ED8YQwO/y5YVB6aVE094/WNZGR7fNG3syBvKvSChnofFQXi9S6g2fhZU09L3oxzQxoIsnkj6WgmRz3RAoGBAOqc2jTZAYmOP/6UA6XmOYoQYFAZDcPy+Yr679P5G0POX5tphWYmaTfnOjdcUbM+Cvj1UchNFDHXlhYCDmxCFutVk/7YOhZ2T5ifX6zM7nfg90nfW7Zj9z8dZWJ6cEWKeQAEvylDb5c+54/NRTls6SwP9qVYi/5paSwmjmU1UzyG",

        // 'merchant_private_key' => "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDIs/g+mk/vy9tlCyA1ya99hQYHnOx997zBtgu+4Ha0rc20BYzVEITEPeuesUe1KWtDuttfbm7aw4Bag4B0uXZktsypnLaPOWHD0ghFglARuZ67LshIrhajsE2BEj0c7eI7KgU2Yl6TCY1Do6E1hlWYKrlfKg/R2HECbWf7kwz7owIDAQAB",
        
        //异步通知地址
        'notify_url' => 'http://'.$_SERVER['HTTP_HOST'].U('Order/notifyurl'),
        
        //同步跳转
        'return_url' => 'http://'.$_SERVER['HTTP_HOST'].U('Order/returnurl'),

        //编码格式
        'charset' => "UTF-8",

        //签名方式
        'sign_type'=>"RSA2",

        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

        //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArwsvlVJob72Y13NBQhYXiLd7k9uFzR89Eppt5XkcHlh0D4ee0QFptxLAtP4lOqHiRXKPIgwoaViCFzl26Bg1wpQv9dPFFqDSS1uF+YXXeq+TWzwSmEedMf9fjqZtrSlOUxN1+smNcsTsvQyYsjAbACQ6T/QTKZwBrXEVcKudat82f+OlstCDYMNbUD8yO5woIKsh6vAbGRFvHeYPze2b2I8Nb4Al9uxpqrMaEaAk1o6DIBCtThXNhtnmVuvxRAFLadfsH+eCcacbivJkWE8+RvSy+4opKk/AHjGLSUdagw1KQ7KrHJNcc4k6iwWPxnxm7QYVOZyim7j1M5fb/Ajm2QIDAQAB",

        // 'alipay_public_key' => "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB",

);