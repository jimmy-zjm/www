<?php
    header( 'Content-Type:text/html;charset=utf-8 ');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="../css/style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Insert title here</title>
</head>
<body>
<table border="1" cellpadding="2" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111">
			<tr>
				<td>
					<font color=red>*</font>respCode
				</td>

				<td>
				<?php echo $_SESSION['respCode'];?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>respMsg
				</td>

				<td>
				    <?php echo $_SESSION['respMsg'];?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>�̻���
				</td>

				<td>
				    <?php echo $_SESSION['MerId'];?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>������
				</td>

				<td>
				    <?php echo $_SESSION['MerOrderNo'];?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>�̻�����
				</td>

				<td>
				    <?php echo $_SESSION['TranDate'];?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>���ױ���
				</td>

				<td>
				    <?php echo $_SESSION['CurryNo'];?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>�������
				</td>

				<td>
				    <?php echo $_SESSION['OrderAmt'];?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>����״̬
				</td>

				<td>
				    <?php echo $_SESSION['OrderStatus'];?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>�汾��
				</td>

				<td>
				    <?php echo $_SESSION['Version'];?>
                </td>
			</tr>
			<tr>
				<td>
					��������
				</td>

				<td>
				    <?php echo $_SESSION['SplitType'];?>
                </td>
			</tr>
			<tr>
				<td>
					���˽���ַ�ʽ
				</td>

				<td>
				    <?php echo $_SESSION['SplitMethod'];?>
                </td>
			</tr>
			
			<tr>
				<td>
					���˹�ʽ
				</td>

				<td>
				    <?php echo $_SESSION['MerSplitMsg'];?>
                </td>
			</tr>
			<tr>
				<td>
					�̻�˽����
				</td>

				<td>
				    <?php echo $_SESSION['MerResv'];?>
                </td>
			</tr>
			<tr>
				<td>
					������չ��
				</td>

				<td>
				    <?php echo $_SESSION['TranReserved'];?>
                </td>
			</tr>
			<tr>
				<td>
					�ۻ��˿���
				</td>

				<td>
				    <?php echo $_SESSION['RefundSumAmount'];?>
                </td>
			</tr>
			<tr>
				<td>
					�յ���ˮ��
				</td>

				<td>
				    <?php echo $_SESSION['AcqSeqId'];?>
                </td>
			</tr>
			<tr>
				<td>
					�յ�������
				</td>

				<td>
				    <?php echo $_SESSION['AcqCode'];?>
                </td>
			</tr>
			<tr>
				<td>
					�յ�����
				</td>

				<td>
				    <?php echo $_SESSION['AcqDate'];?>
                </td>
			</tr>
			<tr>
				<td>
					�����������
				</td>

				<td>
				    <?php echo $_SESSION['CompleteDate'];?>
                </td>
			</tr>
			<tr>
				<td>
					�������ʱ��
				</td>

				<td>
				    <?php echo $_SESSION['CompleteTime'];?>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>ChinaPay����ǩ��
				</td>

				<td width="800">
                     <pre><?php echo $_SESSION['Signature'];?></pre>
                </td>
			</tr>
			<tr>
				<td>
					<font color=red>*</font>ǩ����֤��Ϣ
				</td>

				<td>
                     <pre><?php echo $_SESSION['VERIFY_KEY'];?></pre>
                </td>
			</tr>
		</table>	

	<hr>
</body>
</html>