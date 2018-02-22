<?php
    header( 'Content-Type:text/html;charset=utf-8 ');
    ini_set('date.timezone','Asia/Shanghai');
    include '../../util/common.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="../css/style.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>B2C_DEMO 支付查询</title>
</head>
<body>
<br>
<form name="queryOrder" action="/chinapay_demo/sign.php" method="POST">
	<table>
		<tr>
			<td>
				<font color=red>*</font>商户号
			</td>

			<td>
                   <input type="text" name="MerId" value="000000000000001" maxlength="15"> &nbsp;(15位数字，由chinapay分配)
            </td>
		</tr>
		<tr>
			<td>
				<font color=red>*</font>订单号
			</td>

			<td>
                    <input type="text" name="MerOrderNo" value="" maxlength="16"> &nbsp;(16位数字,必填字段，且当天不能重复)
            </td>
		</tr>
		<tr>
			<td>
				<font color=red>*</font>交易日期
			</td>

			<td>
                    <input type="text" name="TranDate" value="<?php echo date('Ymd'); ?>" maxlength="8"> &nbsp;(8位数字，为订单提交日期)
            </td>
		</tr>
		<tr>
			<td>
				<font color=red>*</font>交易时间
			</td>

			<td>
                    <input type="text" name="TranTime" value="<?php echo date('Hms'); ?>" maxlength="6"> &nbsp;(6位数字，为订单提交时间)
            </td>
		</tr>
		<tr>
			<td>
				<font color=red>*</font>交易类型
			</td>

			<td>
                    <input type="text" name="TranType" value="0502" maxlength="4"> &nbsp;(4位数字，查询交易为0502)
            </td>
		</tr>
		
		<tr>
			<td>
				<font color=red>*</font>业务类型
			</td>

			<td>
                    <input type="text" name="BusiType" value="0001" maxlength="4"> &nbsp;(4位数字，固定值：0001)
            </td>
		</tr>
		<tr>
			<td>
				<font color=red>*</font>版本号
			</td>

			<td>
                    <input type="text" name="Version" value="20140728" maxlength="8"> &nbsp;(8位数字，支付接口版本号)
            </td>
		</tr>
		<tr>
			<td>
				业务ID
			</td>

			<td>
                <input type="text" name="trans_BusiId" value="" maxlength="8"> &nbsp;(可以为空,需要在chinapay开通业务Id编号)
            </td>
		</tr>
		<tr>
			<td>
				参数1
			</td>

			<td>
                <input type="text" name="trans_P1" value="" maxlength="512"> &nbsp;(可以为空,商户传输此业务下的参数值)
            </td>
		</tr>
		<tr>
			<td>
				参数2
			</td>

			<td>
                <input type="text" name="trans_P2" value="" maxlength="512"> &nbsp;(可以为空,商户传输此业务下的参数值)
            </td>
		</tr>
		<tr>
			<td>
				参数3
			</td>

			<td>
                <input type="text" name="trans_P3" value="" maxlength="512"> &nbsp;(可以为空,商户传输此业务下的参数值)
            </td>
		</tr>
		<tr>
			<td>
				参数4
			</td>

			<td>
                <input type="text" name="trans_P4" value="" maxlength="512"> &nbsp;(可以为空,商户传输此业务下的参数值)
            </td>
		</tr>
		<tr>
			<td>
				参数5
			</td>

			<td>
                <input type="text" name="trans_P5" value="" maxlength="512"> &nbsp;(可以为空,商户传输此业务下的参数值)
            </td>
		</tr>
		<tr>
			<td>
				参数6
			</td>

			<td>
                <input type="text" name="trans_P6" value="" maxlength="512"> &nbsp;(可以为空,商户传输此业务下的参数值)
            </td>
		</tr>
		<tr>
			<td>
				参数7
			</td>

			<td>
                <input type="text" name="trans_P7" value="" maxlength="512"> &nbsp;(可以为空,商户传输此业务下的参数值)
            </td>
		</tr>
		<tr>
			<td>
				参数8
			</td>

			<td>
                <input type="text" name="trans_P8" value="" maxlength="512"> &nbsp;(可以为空,商户传输此业务下的参数值)
            </td>
		</tr>
		<tr>
			<td>
				参数9
			</td>

			<td>
                <input type="text" name="trans_P9" value="" maxlength="512"> &nbsp;(可以为空,商户传输此业务下的参数值)
            </td>
		</tr>
		<tr>
			<td>
				参数10
			</td>

			<td>
                <input type="text" name="trans_P10" value="" maxlength="512"> &nbsp;(可以为空,商户传输此业务下的参数值)
            </td>
		</tr>
	</table>
	<input type='button' value='提交查询' onClick='document.queryOrder.submit()'>
</form>
</body>
</html>