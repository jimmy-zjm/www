<?php
header('Content-Type:text/html;charset=utf-8 ');

define ("WWW_DIR" , dirname(dirname(dirname(dirname(__FILE__)))));
require WWW_DIR.'/libs/common/initialize.php';
require_once WWW_DIR.'/chinanopay/util/Settings_INI.php';
$settings = new Settings_INI();
$settings->load(WWW_DIR. "/chinanopay/config/path.properties");
$pay_url = $settings->get("pay_url");
//var_dump($_SESSION,'sdgfsdg');
?>

	<form name="payment" action="<?php echo $pay_url ?>" method="POST">
		<?php
$params = "TranReserved;MerId;MerOrderNo;OrderAmt;CurryNo;TranDate;SplitMethod;BusiType;MerPageUrl;MerBgUrl;SplitType;MerSplitMsg;PayTimeOut;MerResv;Version;BankInstNo;CommodityMsg;Signature;AccessType;AcqCode;OrderExpiryTime;TranType;RemoteAddr;Referred;TranTime;TimeStamp;CardTranData";
foreach ($_SESSION['chinanopay'] as $k => $v) {
    if (strstr($params, $k)) {
        echo "<input type='hidden' name = '" . $k . "' value ='" . $v . "'/>";
    }
}
?>
		 
	</form>
	<script language=JavaScript>
	document.payment.submit();
</script>
</body>
</html>
<?php
unset($_SESSION['chinanopay']);
?>