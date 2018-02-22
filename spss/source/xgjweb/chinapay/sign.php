<?php
define('transResvered', "trans_");
define('cardResvered', "card_");
define('transResveredKey', "TranReserved");
define('signatureField', "Signature");
require '../libs/common/initialize.php';
include 'util/common.php';
include 'util/SecssUtil.class.php';

if ($_POST) {
    $dispatchMap = array(
        // 配置个人网银交易转发地址
        "0001" => "/chinapay/page/pay/b2cPaySend.php",
        "0004" => "/chinapay/page/pay/b2cPaySend.php",
        // 配置退款交易转发地址
        "0401" => "/chinapay/page/refund/b2cRefundSend.php",
        // 配置查询交易转发地址
        "0502" => "/chinapay/page/query/b2cQuerySend.php"
    );
    if (count($_POST) > 0) {
        if (isset($_POST['TranType']) && trim($_POST['TranType']) != "") {
            $dispatchUrl = $dispatchMap[$_POST['TranType']];
        } else {
            $dispatchUrl = $dispatchMap['0001'];
        }
        $transResvedJson = array();
        $cardInfoJson = array();
        $sendMap = array();
        foreach ($_POST as $key => $value) {
            if (isEmpty($value)) {
                continue;
            }
            if (startWith($key, transResvered)) {
                // 组装交易扩展域
                $key = substr($key, strlen(transResvered));
                $transResvedJson[$key] = $value;
            } else 
                if (startWith($key, cardResvered)) {
                    // 组装有卡交易信息域
                    $key = substr($key, strlen(cardResvered));
                    $cardInfoJson[$key] = $value;
                } else {
                    $sendMap[$key] = $value;
                }
        }
        $transResvedStr = null;
        $cardResvedStr = null;
        if (count($transResvedJson) > 0) {
            $transResvedStr = json_encode($transResvedJson);
        }
        if (count($cardInfoJson) > 0) {
            $cardResvedStr = json_encode($cardInfoJson);
        }
        
        $secssUtil = new SecssUtil();
		//var_dump($secssUtil);
        if (! isEmpty($transResvedStr)) {
            $transResvedStr = $secssUtil->decryptData($transResvedStr);
            $sendMap[transResveredKey] = $transResvedStr;
        }
        if (! isEmpty($cardResvedStr)) {
            $cardResvedStr = $secssUtil->decryptData($cardResvedStr);
            $sendMap[cardResveredKey] = $cardResvedStr;
        }
        
        $securityPropFile = $_SERVER['DOCUMENT_ROOT'] . "/chinapay/config/security.properties";
        $secssUtil->init($securityPropFile);
        $secssUtil->sign($sendMap);
        //var_dump($secssUtil->init($securityPropFile),$secssUtil->sign($sendMap));
        $sendMap[signatureField] = $secssUtil->getSign();
		//var_dump($secssUtil->getErrCode(),$secssUtil,$secssUtil->getErrMsg(),$sendMap);
		// $sendMap['userId']=$_SESSION['userId'];
		// $sendMap['userName']=$_SESSION['userName'];
		// $_SESSION = $sendMap;
        $_SESSION['chinapay'] = $sendMap;
		//var_dump($securityPropFile,$secssUtil->getSign(),$_SESSION);exit;
       // echo 'sss';echo $securityPropFile;exit;
        header("Location:" . $dispatchUrl);
    }
}