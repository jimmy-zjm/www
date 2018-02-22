<?php
require_once(WWW_DIR."/model/FurnishCartModel.php");
require_once(WWW_DIR."/model/userModel.php");
require_once(WWW_DIR."/libs/page.php");
class FurnishCartController{
	/**
	 * 加入购物车
	 */
	function add_cart(){
		//var_dump($_SESSION);exit;
		if (empty($_SESSION['userId'])) {
			header("Location:user.php?login");exit;
		}else{
			$model = new FurnishCartModel();
			$rs=$model->check_order($_SESSION['userId']);
			//var_dump($rs);exit;
			if ($rs==1) {
				echo jump(1,'您的健康舒适家购物车内有未支付的订单，为确定不重复购买，请到个人中心继续完成购买或取消订单重新购买',"{$_SERVER['HTTP_REFERER']}");
				header("refresh:3;url={$_SERVER['HTTP_REFERER']}" );
				exit;
			}
		}

		if ($_POST['yy']!='ok') {
			header("index.php");exit;
		}
		//系统的总金额
		// $price=$_POST['money'];
		//该系统的可用抵扣券
		$discount_amount=$_SESSION['discount_amount'];
		//系统名称
		$quote_name=$_SESSION['quote_name'];

		$user_id = $_SESSION['userId'];

		$cat_id = $_SESSION['cat_id'];

		$material = $_SESSION['homebill_aaa'];

		$homebill_num=$_POST['number'];

		if ($cat_id == '31' || $cat_id == '27' || $cat_id == '28') {
			if ($_POST['number']==1) {
				$type = '舒适明装型';
			}else if ($_POST['number']==2) {
				$type = '舒适暗装型';
			}else if ($_POST['number']==3) {
				$type = '豪华明装型';
			}else if ($_POST['number']==4) {
				$type = '豪华暗装型';
			}else{
				echo jump(2,'此产品不存在',"{$_SERVER['HTTP_REFERER']}");
				header("refresh:3;url={$_SERVER['HTTP_REFERER']}" );
				exit;
			}
		}else{
			if ($_POST['number']==1) {
				$type = '经济型';
			}else if ($_POST['number']==2) {
				$type = '舒适型';
			}else if ($_POST['number']==3) {
				$type = '豪华型';
			}else{
				echo jump(2,'此产品不存在',"{$_SERVER['HTTP_REFERER']}");
				header("refresh:3;url={$_SERVER['HTTP_REFERER']}" );
				exit;
			}
		}

		//安装费
		$cost = $_SESSION['homebill_cost'][$_POST['number']];
		//系统的总金额
		$price = $_SESSION['homebill_money'][$_POST['number']-1];

		// switch ($_POST['number']) {
		// 	case 1:
		// 		$type='经济型';
		// 		break;
		// 	case 2:
		// 		$type='舒适型';
		// 		break;
		// 	case 3:
		// 		$type='豪华型';
		// 		break;
		// }

		$data = explode('|', $material);

		if (!empty($_POST['number'])) {
			if ($_POST['number']==1) {
				$material = $data['0'];
			}else if ($_POST['number']==2) {
				$material = $data['1'];
			}else if ($_POST['number']==3) {
				$material = $data['2'];
			}else if ($_POST['number']==4) {
				$material = $data['3'];
			}
		}

		$model = new FurnishCartModel();
		$data= array(
			'user_id'=>$user_id,
			'cat_id'=>$cat_id,
			'material'=>$material,
			'discount_amount'=>$discount_amount,
			'price'=>$price,
			'shop_name'=>$quote_name.$type,
			'house_id'=>$_SESSION['house_data'][0]['house_id'],
			'homebill_num'=>$homebill_num,
			'cost'=>$cost
		);
		
		$rs=$model->check_cart($user_id,$cat_id);
		if ($rs>1) {
			echo jump(1,'购物车已添加该系统',"{$_SERVER['HTTP_REFERER']}");
			header("refresh:1;url={$_SERVER['HTTP_REFERER']}" );
			exit;
		}
		$add = $model->add_cart($data);
		if (!empty($add)) {
			echo jump(1,'加入购物车成功',"{$_SERVER['HTTP_REFERER']}");
			header("refresh:1;url={$_SERVER['HTTP_REFERER']}" );
			exit;
		}else{
			echo jump(2,'加入购物车失败',"{$_SERVER['HTTP_REFERER']}");
			header("refresh:1;url={$_SERVER['HTTP_REFERER']}");
			exit;
		}

	}
	/**
	*产品清单
	*/
	function homebill(){
		//var_dump($_SESSION);exit;
		if (empty($_SESSION['userId'])) {
			header("Location:user.php?login");exit;
		}

		$model = new FurnishCartModel();
		$cartlist=$model->getCart();
		if(empty($cartlist)){
			$cartlist=array();
		}else{
			$total_price=0;
			$amount=0;
			foreach ($cartlist as $k=>$v){
				$total_price+=$v['price'];
				$amount+=$v['discount_amount'];
			}
			$cartlist[0]['total_price']=$total_price;
			$cartlist[0]['amount']=$amount;
		}
		
		

		$homebill_data = $_SESSION['homebill_data'];
		$num = $_SESSION['homebill_num'];
		$cat_id = $_SESSION['cat_id'];



		if (!empty($_GET['homebill'])) {
			$_SESSION['homebill']['homebill'] = $_GET['homebill'];
		}

		if(empty($homebill_data[$_SESSION['homebill']['homebill']])){
			echo '<script>alert("没有您要查询的清单，请重试！");history.go(-1)</script>';exit;
		}


		if ($_SESSION['homebill']['homebill'] == 1) {
			$money=$_SESSION['homebill_money'][0];
			$homebill_data = $homebill_data['1'];
		}else if ($_SESSION['homebill']['homebill'] == 2) {
			$homebill_data = $homebill_data['2'];
			$money=$_SESSION['homebill_money'][1];
		}else if ($_SESSION['homebill']['homebill'] == 3) {
			$homebill_data = $homebill_data['3'];
			$money=$_SESSION['homebill_money'][2];
		}else if ($_SESSION['homebill']['homebill'] == 4) {
			if ($cat_id != '27' && $cat_id != '28' && $cat_id != '31') {
				echo '<script>alert("没有您要查询的清单，请重试！");history.go(-1)</script>';exit;
			}
			$homebill_data = $homebill_data['4'];
			$money=$_SESSION['homebill_money'][3];
		}else{
			echo '<script>alert("没有您要查询的清单，请重试！");history.go(-1)</script>';exit;
		}


		$material = explode('|', $_SESSION['homebill_aaa']);

		if ($cat_id == '31' || $cat_id == '27' || $cat_id == '28') {
			if ($_SESSION['homebill']['homebill']==1) {
				$type = '舒适明装型';
				$material = explode(';', $material['0']);
			}else if ($_SESSION['homebill']['homebill']==2) {
				$type = '舒适暗装型';
				$material = explode(';', $material['1']);
			}else if ($_SESSION['homebill']['homebill']==3) {
				$type = '豪华明装型';
				$material = explode(';', $material['2']);
			}else if ($_SESSION['homebill']['homebill']==4) {
				$type = '豪华暗装型';
				$material = explode(';', $material['3']);
			}
		}else{
			if ($_SESSION['homebill']['homebill']==1) {
				$type = '经济型';
				$material = explode(';', $material['0']);
			}else if ($_SESSION['homebill']['homebill']==2) {
				$type = '舒适型';
				$material = explode(';', $material['1']);
			}else if ($_SESSION['homebill']['homebill']==3) {
				$type = '豪华型';
				$material = explode(';', $material['2']);
			}
		}

		$number = explode(',', $material['1']);

		foreach ($homebill_data as $value) {
			$data[] = $value;
		}

		foreach ($number as $key => $value) {
			if ($value > 0) {
				$numbers[] = $value;
			}
		}

		foreach ($numbers as $key => $value) {
			$data[$key]['number'] = $value;
		}
		// echo '<pre>';
		// var_dump($data);exit;
		//显示主材
		foreach ($data as $key => $value) {
			if (!empty($value['lv']) && $value['lv'] == '1') {
				$datas[] = $value;
			}
		}

		$data = $datas;

		/**************************/
		//排序
		$sort = array(  
	        'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
	        'field'     => 'sort',       //排序字段  
		);  
		$arrSort = array();  
		foreach($data AS $uniqid => $row){  
		    foreach($row AS $key=>$value){  
		        $arrSort[$key][$uniqid] = $value;  
		    }  
		}  
		if($sort['direction']){  
		    array_multisort($arrSort[$sort['field']], constant($sort['direction']), $data);  
		}  

		/**************************/
		  

		foreach ($data as $value) {
			$aaa[] = explode('/', $value['brand']);
		}

		
		for ($i=0; $i <count($aaa) ; $i++) { 
			if (empty($aaa[$i]['1'])) {
				$data[$i]['brand'] = '';
				$data[$i]['country'] = $aaa[$i]['0'];
			}else{
				$data[$i]['brand'] = $aaa[$i]['0'];
				$data[$i]['country'] = $aaa[$i]['1'];
			}
		}
		

		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}

		//显示列表内容
		if ($page == 1) {
			foreach ($data as $key=>$value) {
				if ($key > 7*$page) {
					unset($data[$key]);
				}
			}
		}else if ($page == 1*$page) {
			foreach ($data as $key=>$value) {
				if ($key > 7*$page+($page-1) || $key <= 7*($page-1)+($page-2)) {
					unset($data[$key]);
				}
			}
		}

		if (empty($_GET['homebill'])) {
			$_GET['homebill']='';
		}

		//实例化分页类
		$t = new Page(8, count($aaa), $page, 5, "furnishcart.php?homebill=".$_GET['homebill']."&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式
		//模板传值
		
		$tpl = get_smarty();

		if (!empty($_GET['error'])) {
			$tpl->assign("error",1);
		}

		require_once(WWW_DIR."/model/xgj_furnish.php");
		$pn= new home();
		$eu_tree=$pn->category(1);
		$tpl->assign("eu_tree", $eu_tree);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);

	require_once(WWW_DIR."/classes/test.php");

		$tpl->assign('cartlist',$cartlist);
		$tpl->assign('money',$money);
		$tpl->assign('type',$type);
		$tpl->assign("page",$page);
		$tpl->assign('data',$data);
		$tpl->display('homebill.tpl.html');
	}

	
	/**
	 * 删除购物车商品
	 */
	function del(){
		$cart_id=intval($_GET['cart_id']);
		$FurnishCartOb=new FurnishCartModel();
		$rs=$FurnishCartOb->del_cart($cart_id);
		if ($rs) {
			echo jump(1,'删除成功',"{$_SERVER['HTTP_REFERER']}");
			header("refresh:1;url={$_SERVER['HTTP_REFERER']}" );
			exit;
		}else{
			echo jump(2,'删除失败',"{$_SERVER['HTTP_REFERER']}");
			header("refresh:1;url={$_SERVER['HTTP_REFERER']}" );
			exit;
		}				
	}

	/**
	 * 删除购物车商品
	 */
	function delAll(){
		if(empty($_GET['cart_id'])){
			echo jump(2,'请选择删除的信息',"{$_SERVER['HTTP_REFERER']}");
			header("refresh:1;url={$_SERVER['HTTP_REFERER']}");
			exit;
			die;
		}else{
			$cart_id=$_GET['cart_id'];
		}
		$FurnishCartOb=new FurnishCartModel();
		$str='';
		foreach ($cart_id as $key => $value) {
			$str.=$value.',';
		}
		$where=rtrim($str,',');
		$rs=$FurnishCartOb->delAllCart($where);
		if ($rs) {
			echo jump(1,'全部删除成功',"{$_SERVER['HTTP_REFERER']}");
			header("refresh:1;url={$_SERVER['HTTP_REFERER']}" );
			exit;
		}else{
			echo jump(2,'全部删除失败',"{$_SERVER['HTTP_REFERER']}");
			header("refresh:1;url={$_SERVER['HTTP_REFERER']}" );
			exit;
		}				
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
	
	/**
	 * add_concern	添加收藏
	 */
	public function addConcern(){
		
		if (empty($_SESSION['userId'])) {
			header("Location:user.php?login");exit;
		}

		// var_dump($_GET);exit;

		$model = new userModel();

		$uid=$_SESSION['userId'];

		if ($_SESSION['furnish_quote']['quote_id'] == '31' || $_SESSION['furnish_quote']['quote_id'] == '27' || $_SESSION['furnish_quote']['quote_id'] == '28') {
			if ($_GET['type']==1) {
				$type = '舒适明装型';
			}else if ($_GET['type']==2) {
				$type = '舒适暗装型';
			}else if ($_GET['type']==3) {
				$type = '豪华明装型';
			}else if ($_GET['type']==4) {
				$type = '豪华暗装型';
			}
		}else{
			if ($_GET['type']==1) {
				$type = '经济型';
			}else if ($_GET['type']==2) {
				$type = '舒适型';
			}else if ($_GET['type']==3) {
				$type = '豪华型';
			}
		}
		


		$select = $model->selectConcern($uid);

		foreach ($select as $key => $value) {
			if ($value['class_id'] == 1 && $value['c_goodsname'] == $_SESSION['furnish_quote']['quote_name'].$type) {
				echo "1";exit;
			}
		}

		$data = array(
			'class_id'=>1,
			'user_id'=>$uid,
			'goods_id'=>$_SESSION['furnish_quote']['quote_id'],
			'c_images'=>$_SESSION['furnish_quote']['img'],
			'c_goodsname'=>$_SESSION['furnish_quote']['quote_name'].$type,
			'c_goodsprice'=>$_GET['price']
		);

		
		$return = $model->addConcern($data);
		
		if (!empty($return)) {
			$id = $_SESSION['cat_id'];
			echo "2";exit;
			// header("Location:price.php?cid=$id");exit;
		}else{
			echo "3";exit;
		}

	}
	
	
}
