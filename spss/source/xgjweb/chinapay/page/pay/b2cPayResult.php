<?php
    header( 'Content-Type:text/html;charset=utf-8 ');
    session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="../css/style.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>交易结果</title>
</head>
<body>
<table border="1" cellpadding="2" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111">
			<tr>
				<td>
					<font color=red>*</font>商户号
				</td>

				<td>
				    <?php echo $_SESSION['MerId']?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>订单号
				</td>

				<td>
				<?php echo $_SESSION['MerOrderNo']?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>商户日期
				</td>

				<td>
				<?php echo $_SESSION['TranDate']?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>商户时间
				</td>

				<td>
				<?php echo $_SESSION['TranTime']?>
                </td>
			</tr>
			
			<tr>
				<td>
					<font color=red>*</font>交易类型
				</td>

				<td>
				<?php echo $_SESSION['TranType']?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>业务类型
				</td>

				<td>
				<?php echo $_SESSION['BusiType']?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>交易币种
				</td>

				<td>
				<?php echo $_SESSION['CurryNo']?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>订单金额
				</td>

				<td>
				<?php echo $_SESSION['OrderAmt']?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>订单状态
				</td>

				<td>
				<?php echo $_SESSION['OrderStatus']?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>版本号
				</td>

				<td>
				<?php echo $_SESSION['Version']?>
                </td>
			</tr>
			<tr>
				<td>
					分账类型
				</td>

				<td>
				<?php echo $_SESSION['SplitType']?>
                </td>
			</tr>
			<tr>
				<td>
					分账金额拆分方式
				</td>

				<td>
				<?php echo $_SESSION['SplitMethod']?>
                </td>
			</tr>
			
			<tr>
				<td>
					分账公式
				</td>

				<td>
				<?php echo $_SESSION['MerSplitMsg']?>
                </td>
			</tr>
			
			<tr>
				<td>
					<font color=red>*</font>支付机构号
				</td>

				<td>
				<?php echo $_SESSION['BankInstNo']?>
                </td>
			</tr>
			<tr>
				<td>
					商品信息描述
				</td>

				<td>
				<?php echo $_SESSION['CommodityMsg']?>
                </td>
			</tr>
			<tr>
				<td>
					商户私有域
				</td>

				<td>
				<?php echo $_SESSION['MerResv']?>
                </td>
			</tr>
			<tr>
				<td>
					交易扩展域
				</td>

				<td>
				<?php echo $_SESSION['TranReserved']?>
                </td>
			</tr>
			<tr>
				<td>
					支付超时时间
				</td>

				<td>
				<?php echo $_SESSION['PayTimeOut']?>
                </td>
			</tr>
			<tr>
				<td>
					商户时间戳
				</td>

				<td>
				<?php echo $_SESSION['TimeStamp']?>
                </td>
			</tr>
			<tr>
				<td>
					持卡人IP信息
				</td>

				<td>
				<?php echo $_SESSION['RemoteAddr']?>
                </td>
			</tr>
			<tr>
				<td>
					收单流水号
				</td>

				<td>
				<?php echo $_SESSION['MerId']?>
                </td>
			</tr>
			<tr>
				<td>
					收单日期
				</td>

				<td>
				<?php echo $_SESSION['AcqSeqId']?>
                </td>
			</tr>
			<tr>
				<td>
					交易完成日期
				</td>

				<td>
				<?php echo $_SESSION['CompleteDate']?>
                </td>
			</tr>
			<tr>
				<td>
					交易完成时间
				</td>

				<td>
				<?php echo $_SESSION['CompleteTime']?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>ChinaPay数字签名
				</td>

				<td width="800">
                     <pre><?php echo $_SESSION['Signature']?></pre>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>签名验证信息
				</td>

				<td width="800">
                     <pre><?php echo $_SESSION['VERIFY_KEY']?></pre>
                </td>
			</tr>
		</table>	

	<hr>
</body>
</html>
<?php
   // session_destroy();
?>