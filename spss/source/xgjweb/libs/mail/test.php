<?php
/**
 * @author Tissot.Cai
 * @copyright 厦门帝网信息科技有限公司
 * @version 1.0
 */
include_once 'mail.php';

if (Mail::send($subject, $body, $from, $from_name, $to)) {
    echo '发送成功！';
}
else{
    echo Mail::$msg;
}
?>
