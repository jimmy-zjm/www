<?php
require '../libs/common/initialize.php';

if ($_POST) {
    
    /*  $dispatchMap = array(
        // ���ø�����������ת����ַ
        "0001" => "/chinapay/page/pay/b2cPayResult.php",
        // �����˿��ת����ַ
        "0401" => "/chinapay/page/refund/b2cRefundResult.php",
        // ���ò�ѯ����ת����ַ
        "0502" => "/chinapay/page/query/b2cQueryResult.php"
    ); */
    
    if (count($_POST) > 0) {
        /* if ($_POST['TranType'] && trim($_POST['TranType']) != "") {
            $dispatchUrl = $dispatchMap[$_POST['TranType']];
        } else {
            $dispatchUrl = $dispatchMap['0001'];
        } */
        
        include 'util/common.php';
        include 'util/SecssUtil.class.php';
        
        $secssUtil = new SecssUtil();
        $securityPropFile = $_SERVER['DOCUMENT_ROOT'] . "/chinapay/config/security.properties";
        $secssUtil->init($securityPropFile);

        if ($secssUtil->verify($_POST)) {
            $_SESSION['pay']['table']='xgj_chinapay';
			$_SESSION['pay']['order_id'] = date('Y',time()).$_POST['MerOrderNo'];
			$_SESSION['pay']['system_id'] = $_POST['MerResv'];
			header('location:../order.php?paySuccess');
			 die;
        } else {
            echo '֧��ʧ��';
            header("refresh:3;url='../order.php?payError'");
            die;
        }
        
    }
}


