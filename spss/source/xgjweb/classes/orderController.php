<?php
require_once(WWW_DIR."/model/order_model.php");
require_once(WWW_DIR."/model/userModel.php");
require_once(WWW_DIR."/libs/page.php");
class orderController{
	/**
	 * 加入购物车
	 */
	function addcart(){

		$color=trim($_GET['color']);
		
		$class_id=intval($_GET['class_id']);
		
		$goods_id=intval($_GET['goods_id']);
		
		$num=intval($_GET['num']);
		
	    $goodsOb=new ordermodel();
	    
		$goods=$goodsOb->getGooddetailById($class_id,$goods_id);
		
		if(empty($_COOKIE['cart'])){
			
			$string = $color.'|'.$num.'|'.implode("|", array_values($goods));
			
		}else{
			
            $str=$color.'|'.$num.'|'.implode("|", array_values($goods));
            $string = $_COOKIE['cart'].';'.$str;
            
		}
		
        setcookie("cart",$string,time()+3600*7*24);
        
        header("location:?cartlist&goods_id=$goods_id&num=$num"); exit;      
   }
   /**
    * 购物车列表
    */
   function cartlist(){
   		$tpl=get_smarty();
   		
   		if(!empty($_COOKIE['cart'])){
   			
   			$array=explode(";", $_COOKIE['cart']);
   			
   			foreach ($array as $v){
   				
   				$cart[] = explode("|", $v);
   				
   			}
   			
		   	$total=0;
		   	
		   	$priv=0;
		   	
		   	$num=0;
		   	
		   	foreach ($cart as $v){
		   		
		   		$num+=$v[1];
		   		
		   		$total+=$v[6]*$v[1];
		   		
		   		$priv+=$v[5]*$v[1]-$v[6]*$v[1];
		   		
		   	}
		   	
		   	$cartnum=count($cart);
		   	
		   	setcookie("cartnum",$cartnum,time()+3600*7*24);
		   	
		   	$tpl->assign('num',$num);
		   	
		   	$tpl->assign('total',$total);
		   	
		   	$tpl->assign('priv',$priv);
		   	
	   		$tpl->assign('cart',@$cart);
	   		
   		}else{
   			
   			setcookie("cartnum",'',time()-3600);
   			
   		}
   		
	   	$tpl->display("cart.tpl.html");
   }
	
	/**
	 * 删除购物车商品
	 */
	function del(){
		$id=intval($_GET['id']);
		
		$string=explode(";", $_COOKIE['cart']);
		
		foreach ($string as $v){
			
			$carts[] = explode("|", $v);
			
		}
		
		unset($carts[$id]);
		
		$cart=arr2str($carts,"|",";");
		
		setcookie("cart",$cart,time()+3600*7*24);
		
		header("location:?cartlist"); exit;
	}
	
	//显示支付结算页面
	public function showPayPage(){
		$tpl=get_smarty();
		//验证用户是否已经登录
		if (!isset($_SESSION['userName'])){
			echo "<script type='text/javascript'>";
			echo "alert('您还没有登录，请您先登录~');";
			echo "history.back()";
			echo "</script>";
			return false;
		}
		//通过用户名查出收货地址
		$userName = $_SESSION['userName'];
		$orderAddress = new ordermodel();
		$res = $orderAddress->showAddress($userName);//获得用户在地址数组
		
		//配送清单
		if (empty($_COOKIE['cart'])){
			echo "<script type='text/javascript'>confirm('购物车内没有宝贝，请添加宝贝后再结算');window.history.back();</script>";
		}else {
			/*
			 * $_COOKIE['cart']中数据类型如下
			 * （ [cart] => |1|28|0003|GUCCI包包|499.00|399.00|5694738215806.jpg|56948c4161075.jpg|56948b7d5b1e1.jpg|56948b632160d.jpg|5694738215806.jpg;
			 * |1|28|0003|GUCCI包包|499.00|399.00|5694738215806.jpg|56948c4161075.jpg|56948b7d5b1e1.jpg|56948b632160d.jpg|5694738215806.jpg;
			 * |1|28|0003|GUCCI包包|499.00|399.00|5694738215806.jpg|56948c4161075.jpg|56948b7d5b1e1.jpg|56948b632160d.jpg|5694738215806.jpg;|）
			 */
			$arr = explode(";", $_COOKIE['cart']);
			foreach ($arr as $v){
				$cart[] = explode('|', $v);
			}
			
			$tpl->assign('cart',$cart);
			$totalNum = 0;		//商品总件数
			$totalPrice = 0;	//商品总价格
			$discount = 0;		//商品折扣
			foreach ($cart as $k=>$v){
				$totalNum += $v[1];					//$v[1] 单件商品购买数量
				$totalPrice += $v[1]*$v[6];			//$v[6] 商品的现价
				$discount += ($v[5]-$v[6])*$v[1];	//$v[5] 商品的原价
			}
			$tpl->assign('totalNum',$totalNum);
			$tpl->assign('totalPrice',$totalPrice);
			$tpl->assign('discount',$discount);
		}
		
		
		$tpl->assign('res',$res);
		
		$tpl->display("payment.tpl.html");	//payment.tpl.html为支付结算页面
	}
	//返回购物车页面修改信息
	public function cartBack(){
		header("location:?cartlist"); exit;
	}
	
	//设置结算页面默认收货地址
	public function addrDefaultSet(){
		$addrId = $_GET["addrId"];	//收货地址(主键)ID
		$addrDefaultSet = new userModel();	//实例化userModel类
		$addrDefaultResult = $addrDefaultSet->addrDefaultSet($addrId,$_SESSION["userId"]);

		if($addrDefaultResult > 0){			//默认地址设置成功回到支付结算页面
			header("Location:order.php?showPayPage");
		}else{
			echo "<SCRIPT type='text/javascript'>alert('设置为默认地址失败,请重试!!!');history.back();</SCRIPT>";
			exit();
		}
	}
	
	
	/**
	 * 显示添加收货地址页面
	 */
	public function addrInfoAddShow(){

		$tpl = get_smarty();

		$tpl->display('order_addrAdd.tpl.html');

	}

	/**
	 * 实行收货地址添加
	 */
	public function doAddrInfoAdd(){

		if($_POST){
			$userId = $_SESSION["userId"];	//用户ID
			$receivingName = $_POST["receivingName"];	//收货人姓名
			$mobile = $_POST["mobile"];	//收货人手机
			$phone = $_POST["phone"];	//收货人手机
			$email = $_POST["email"];	//收货人邮箱
			$addr = $_POST["addr"];	//收货人详细地址
			$doAddrInfoAdd = new userModel();
			$doAddrInfoAddResult = $doAddrInfoAdd->doAddrInfoAdd($userId, $receivingName, $mobile, $phone, $email, $addr);

			if($doAddrInfoAddResult > 0){
				header("Location:order.php?showPayPage");
			}else{
				echo "<SCRIPT type='text/javascript'>alert('添加地址失败,请重试!!!');history.back();</SCRIPT>";
				exit();
			}
		}

	}
	
	/**
	 * 显示修改收货地址
	 */
	public function addrInfoEditShow(){

		$tpl = get_smarty();

		$addrId = $_GET["addrId"];
		$addrInfoSelforEdit = new userModel();
		$addrInfoSelList = $addrInfoSelforEdit->addrInfoSelOne($addrId);

		$tpl->assign("addrInfoSelList",$addrInfoSelList);
		$tpl->display('order_addrEdit.tpl.html');

	}

	/**
	 * 实行修改收货地址
	 */
	public function doAddrInfoEdit(){

		if($_POST){

			$addrId = $_POST["addrId"];	//地址(主键)ID
			$receivingName = $_POST["receivingName"];	//收货人姓名
			$mobile = $_POST["mobile"];	//收货人手机
			$phone = $_POST["phone"];	//固定电话
			$email = $_POST["email"];	//收货人邮箱
			$addr = $_POST["addr"];	//收货人详细地址
			$doAddrInfoAdd = new userModel();
			$doAddrInfoEditResult = $doAddrInfoAdd->doAddrInfoEdit($addrId, $receivingName, $mobile, $phone, $email, $addr);

			if($doAddrInfoEditResult > 0){
				header("Location:order.php?showPayPage");
			}else{
				echo "<SCRIPT type='text/javascript'>alert('修改地址失败,请重试!!!');history.back();</SCRIPT>";
				exit();
			}

		}

	}

	/**
	 * addrInfoDel	删除收货地址
	 */
	public function addrInfoDel(){
		$addrId = $_GET["addrId"];
		$addrInfoDel = new userModel();
		$addrInfoDelResult = $addrInfoDel->addrInfoDel($addrId);

		if($addrInfoDelResult > 0){
			header("Location:order.php?showPayPage");//地址删除成功回到支付结算页面
		}else{
			echo "<SCRIPT type='text/javascript'>alert('删除地址失败,请重试!!!');history.back();</SCRIPT>";
			exit();
		}
	}
	
	
	
	
}
