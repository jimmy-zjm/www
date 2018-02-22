<?php
// require 'SecssFileService.class.php';
require 'SecssUtil.class.php';
$securityPropFile="D:/Zend/workspaces/DefaultWorkspace12/chinapaySECSS_PHP/security.properties";
// $secssUtil = new SecssFileService();
$secssUtil = new SecssUtil();
$secssUtil->init($securityPropFile);
$file="D:/Zend/workspaces/DefaultWorkspace12/ChinaPaySECSS_PHP/测试文件.txt";

echo "sign start time".date('y-m-d h:i:s',time())."\r\n";
$secssUtil->signFile($file);
if("00"===$secssUtil->getErrCode()){
    echo "文件签名成功，errcode=[" .$secssUtil->getErrCode()."]\r\n";
}else{
    echo "文件签名失败，errcode=[" .$secssUtil->getErrCode()."]\r\n";
}
echo "sign end time".date('y-m-d h:i:s',time())."\r\n";
echo "--------------------------------------\r\n";

echo "verify start time".date('y-m-d h:i:s',time())."\r\n";
$secssUtil->verifyFile($file);
if("00"===$secssUtil->getErrCode()){
    echo "文件验签成功，errcode=[" .$secssUtil->getErrCode()."]\r\n";
}else{
    echo "文件验签失败，errcode=[" .$secssUtil->getErrCode()."]\r\n";
}
echo "verify end time".date('y-m-d h:i:s',time())."\r\n";
?>