<?php
header('Content-Type:text/html;charset=utf-8 ');
session_start();
require_once '../../util/Settings_INI.php';
$settings = new Settings_INI();
$settings->load($_SERVER['DOCUMENT_ROOT'] . "/chinapay_demo/config/path.properties");
$refund_url = $settings->get("refund_url");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="../css/style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>b2c demo 退款交易</title>
</head>
<body>
	<form name="payment" action="<?php echo $refund_url;?>" method="POST"
		target="_blank">
		<table border="1" cellpadding="2" cellspacing="0"
			style="border-collapse: collapse" bordercolor="#111111">
			<tr>
				<td><font color=red>*</font>商户号</td>

				<td>
	            <?php echo $_SESSION['MerId']?>
	        </td>
			</tr>
			<tr>
				<td><font color=red>*</font>退款订单号</td>

				<td>
	                   <?php echo $_SESSION['MerOrderNo']?>
	        </td>
			</tr>
			<tr>
				<td><font color=red>*</font>退款订单日期</td>

				<td>
	                  <?php echo $_SESSION['TranDate']?>
	        </td>
			</tr>
			<tr>
				<td><font color=red>*</font>退款订单时间</td>

				<td>
	                   <?php echo $_SESSION['TranTime']?>
	        </td>
			</tr>
			<tr>
				<td><font color=red>*</font>原支付交易订单号</td>

				<td>
			         <?php echo $_SESSION['OriOrderNo']?>
	        </td>
			</tr>
			<tr>
				<td><font color=red>*</font>原支付交易日期</td>

				<td>
			         <?php echo $_SESSION['OriTranDate']?>
	        </td>
			</tr>
			<tr>
				<td><font color=red>*</font>交易类型</td>

				<td>
			         <?php echo $_SESSION['TranType']?>
	        </td>
			</tr>

			<tr>
				<td><font color=red>*</font>业务类型</td>

				<td>
			     <?php echo $_SESSION['BusiType']?>
	           </td>
			</tr>

			<tr>
				<td><font color=red>*</font>版本号</td>

				<td>
	                  <?php echo $_SESSION['Version']?>
	           </td>
			</tr>
			<tr>
				<td>分账类型</td>

				<td>
	                  <?php echo $_SESSION['SplitType']?>
	           </td>
			</tr>
			<tr>
				<td>分账方式</td>

				<td>
	                  <?php echo $_SESSION['SplitMethod']?>
	           </td>
			</tr>

			<tr>
				<td>商户分账公式</td>

				<td>
	               <?php echo $_SESSION['MerSplitMsg']?>
	           </td>
			</tr>
			<tr>
				<td><font color=red></font>交易币种</td>

				<td>
	                <?php echo $_SESSION['CurryNo']?>
	           </td>
			</tr>
			<tr>
				<td>接入方式</td>

				<td>
	                  <?php echo $_SESSION['AccessType']?>
	           </td>
			</tr>
			<tr>
				<td><font color=red></font>商户后台接收地址</td>
				<td>
	                  <?php echo $_SESSION['MerBgUrl']?>
	           </td>
			</tr>

			<tr>
				<td>交易扩展域</td>

				<td>
	                <?php echo $_SESSION['TranReserved']?>
	        </td>
			</tr>
			<tr>
				<td><font color=red>*</font>签名信息</td>

				<td>
	                 <?php echo $_SESSION['Signature']?>
	        </td>
			</tr>
		</table>
		<hr>
<?php
$params = "TranReserved;MerBgUrl;BusiType;CurryNo;MerSplitMsg;MerId;AccessType;AcqCode;SplitType;Signature;TranDate;TranTime;OriTranDate;TranType;Version;MerResv;SplitMethod;MerOrderNo;OriOrderNo;RefundAmt";
foreach ($_SESSION as $k => $v) {
    if (strstr($params, $k)) {
        echo "<input type='hidden' name = '" . $k . "' value ='" . $v . "'/>";
    }
}
?>
	如果您的浏览器没有弹出支付页面，请点击按钮<input type='button' value='提交订单'
			onClick='document.payment.submit()'>再次提交。
	</form>

</body>
</html>
<?php
session_destroy();
?>