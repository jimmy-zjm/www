<?php
/**
 * Created by PhpStorm.
 * User: 唐文权
 * Date: 2015/12/21
 * Time: 16:29
 */

use Think\Template\Driver\Smart;
require_once(WWW_DIR."/libs/db.php");
require_once(WWW_DIR."/model/userModel.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
class userController{

	/**
	 * center	显示个人中心
	 */
	function center()
	{
		//var_dump(substr('201604051808016555f2',0,18));exit;
		//调用判断是否登录的方法
		user_check_logon();

		//查出用户信息
		$user = new userModel();
		$userInfo = $user->centerSelInfo($_COOKIE["userId"]);
		
		//查询可用优惠券总金额
		$couponAvailable = $user->couponStatusCount("WHERE `user_id` = '{$_COOKIE["userId"]}' AND `c_status` = '0'");

		$tpl = get_smarty();

		//给变量赋值
		$tpl->assign("userId", $userInfo["user_id"]);		//用户ID
		$tpl->assign("userName", $userInfo["user_name"]);	//用户名
		$tpl->assign("userFace", $userInfo["face"]);		//用户头像
		$tpl->assign("userMoney", $userInfo["user_money"]);	//用户余额
		$tpl->assign("userPayPoints", $userInfo["pay_points"]);	//用户暖豆(积分)
		$tpl->assign("userCoupon", $couponAvailable["statusCount"]);	//用户优惠券


	    //未付款数量
		$notPayCount = $user->waitPayCountdata($_COOKIE["userId"]);
		$tpl->assign("orderNotPay", $notPayCount["state"]);

		//待收货数量
		$waitReceivCount = $user->waitReceivCountdata($_COOKIE["userId"]);		
		$tpl->assign("orderWaitReceiv", $waitReceivCount["state"]);

		//待评价数量
		$waitAssessCount = $user->waitAssessCountdata($_COOKIE["userId"]);
		$tpl->assign("orderWaitAssess", $waitAssessCount["state"]);
		


		$tpl->display('user_center.tpl.html');
	}


	/**
	 * order	我的订单  《欧洲团购，德国母婴》
	 */
	function order(){

		user_check_logon();

		$order = new userModel();

		$tpl = get_smarty();
		
		//未付款数量
		$notPayCount = $order->waitPayCountdata($_COOKIE["userId"]);
		$tpl->assign("orderNotPay", $notPayCount["state"]);

		//待收货数量
		$waitReceivCount = $order->waitReceivCountdata($_COOKIE["userId"]);		
		$tpl->assign("orderWaitReceiv", $waitReceivCount["state"]);

		//待评价数量
		$waitAssessCount = $order->waitAssessCountdata($_COOKIE["userId"]);
		$tpl->assign("orderWaitAssess", $waitAssessCount["state"]);

		//订单总数量
		$countAll = $order->countAlldata($_COOKIE["userId"]);
			
		$tpl->assign("orderAll", $countAll["state"]);
		
	
		
		
		
		//全部订单鼠标悬浮信息
		$orderInfoAllList = $order->orderInfoAll("all");
       
	    $tpl->assign("orderInfoSelResult", $orderInfoAllList["dataSelResult"]); //查询到的数据列表
	    $tpl->assign("page", $orderInfoAllList["pageInfo"]["page"]);	//当前页
	    $tpl->assign("pageCount", $orderInfoAllList["pageInfo"]["pageCount"]);	//总页数

		//待付款订单鼠标悬浮信息
		if(isset($_GET["month"])) { echo "<script> alert(); </script>";}
		$orderInfoWaitPayList = $order->orderInfoAll("waitPay");
		$tpl->assign("orderInfoWaitPayList", $orderInfoWaitPayList);

		//待收货(已发货)订单鼠标悬浮信息
		$orderInfoWaitReceivList = $order->orderInfoAll("waitReceiv");
		$tpl->assign("orderInfoWaitReceivList", $orderInfoWaitReceivList);

		//待评价订单鼠标悬浮信息
		$orderInfoWaitAssessList = $order->orderInfoAll("waitAssess");
		$tpl->assign("orderInfoWaitAssessList", $orderInfoWaitAssessList);
		$tpl->display('user_order.tpl.html');
	}


	/**
	 * 关注的商品
	 */
	
	function concernGoods(){

		user_check_logon();

		$tpl = get_smarty();

		$concern = new userModel();

		$concernGoodsList = $concern->concernGoodsList($_COOKIE["userId"], 8);
		$tpl->assign("concernGoodsList", $concernGoodsList["dataSelResult"]);	//查询到的数据列表
		$tpl->assign("page", $concernGoodsList["pageInfo"]["page"]);	        //当前页
		$tpl->assign("pageCount", $concernGoodsList["pageInfo"]["pageCount"]);	//总页数

		$tpl->display('user_gzdsp.tpl.html');

	}
	
	/**
	 *  取消关注
	 * 
	 */
	function cancergoods(){
		$deletedata=new userModel();
	
		if (isset($_GET["arr"])){
			
			$arr=$_GET['arr']; 
			$arr=explode(",", $arr);
			
		  foreach ($arr  as  $value)
		  {
		    $id=substr($value, 3,2);
		       
		    $deletedata->deletegoods($id);
		   
		  }	
			
		}
	
	   echo  "<script>window.location.href='user.php?concernGoods';</script>";
		
	}
  
      
	/**
	 * 读取个人信息
	 */
	function userInfo(){

		//调用判断是否登录的方法
		user_check_logon();

		$tpl = get_smarty();

		//查询出信息
		$userInfo = new userModel();
		$infoList = $userInfo->userInfoList($_COOKIE["userId"]);
      	$tpl->assign("infoId", $infoList["user_id"]);	//ID  
		$tpl->assign("infoEmail", $infoList["email"]);	//邮箱
		$tpl->assign("infoUserName", $infoList["user_name"]);	//用户名
		$tpl->assign("infoFace", $infoList["face"]);	//头像
		$tpl->assign("infoAlias", $infoList["alias"]);	//昵称
		$tpl->assign("infoMobilePhone", $infoList["mobile_phone"]);	//手机
		$tpl->assign("infoAddr", $infoList["addr"]);	//地址
		$tpl->assign("infoIdentityCard", $infoList["identity_card"]);	//身份证
		//性别
		if($infoList["sex"] == 0){
			$tpl->assign("infoSex", '<input type="radio" name="sex" class="jdradio" value="0" checked="checked" /><label class="mr10">男</label><input type="radio" name="sex" class="jdradio" value="1"><label class="mr10" />女</label><input type="radio" name="sex" class="jdradio" value="2" /><label class="mr10">保密</label>');
		}elseif($infoList["sex"] == 1){
			$tpl->assign("infoSex", '<input type="radio" name="sex" class="jdradio" value="0" /><label class="mr10">男</label><input type="radio" name="sex" class="jdradio" value="1" checked="checked" /><label class="mr10">女</label><input type="radio" name="sex" class="jdradio" value="2" /><label class="mr10">保密</label>');
		}elseif($infoList["sex"] == 2){
			$tpl->assign("infoSex", '<input type="radio" name="sex" class="jdradio" value="0" /><label class="mr10">男</label><input type="radio" name="sex" class="jdradio" value="1" /><label class="mr10">女</label><input type="radio" name="sex" class="jdradio" value="2"  checked="checked" /><label class="mr10">保密</label>');
		}

		//月收入
		if($infoList["monthly_profit"] == "1万以上"){
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option selected="selected">1万以上</option><option>8千——1万</option><option>6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "8千——1万"){
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option selected="selected">8千——1万</option><option>6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "6千——8千"){
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option >8千——1万</option><option selected="selected">6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "4千——6千"){
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option>8千——1万</option><option>6千——8千</option><option selected="selected">4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "4千以下"){
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option>8千——1万</option><option>6千——8千</option><option>4千——6千</option><option selected="selected">4千以下</option></select>');
		}else{
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option selected="selected">请选择</option><option>1万以上</option><option>8千——1万</option><option>6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}

		//教育程度
		if($infoList["education_status"] == "初中"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option selected="selected">初中</option><option>高中</option><option>大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "高中"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option selected="selected">高中</option><option>大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "大学"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option selected="selected">大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "硕士"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option>大学</option><option selected="selected">硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "博士"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option>大学</option><option>硕士</option><option selected="selected">博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "其他"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option>大学</option><option>硕士</option><option>博士</option><option selected="selected">其他</option></select>');
		}else{
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option selected="selected">请选择</option><option>初中</option><option>高中</option><option>大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
		}

		//生日(年份)
		$birthdayArray = explode("-",$infoList["birthday"]);
		$birYearList = $birthdayArray[0];

		$birYear = "<select id='selectmenu1' name='birYearSelName'>";
		foreach (range(99,30) as $year){
			if ($birYearList == "19".$year){
				$birYear .= "<option selected='selected'>19".$year."</option>";
			}else {
				$birYear .= "<option>19".$year."</option>";
			}
		}
		$birYear .= "</select>";

		//生日(月份)
		$birMonthList = $birthdayArray[1];

		$birMonth = "<select id='selectmenu2' name='birMonthSelName'>";
		foreach (range(1,12) as $month){
			if ($birMonthList == $month){
				$birMonth .= "<option selected='selected'>".$month."</option>";
			}else {
				$birMonth .= "<option>".$month."</option>";
			}
		}
		$birMonth .= "</select>";

		//生日(日期)
		$birDateList = $birthdayArray[2];

		$birDate = "<select id='selectmenu3' name='birDateSelName'>";
		foreach (range(1,31) as $date){
			if ($birDateList == $date){
				$birDate .= "<option selected='selected'>".$date."</option>";
			}else {
				$birDate .= "<option>".$date."</option>";
			}
		}
		$birDate .= "</select>";

		$tpl->assign("infoBirthdayYear",$birYear);
		$tpl->assign("infoBirthdayMonth",$birMonth);
		$tpl->assign("infoBirthdayDate",$birDate);

		$tpl->display('user_info.tpl.html');

	}


	/**
	 * 修改个人信息
	 */
	function userInfoEdit(){
		//默认是失败状态
         $re=0;  $rs=0;
		//调用判断是否登录的方法
		user_check_logon();

		if($_POST){

			$userId = $_COOKIE["userId"];
			//检测上传图片是否存在
			if(isset($_FILES)){
				//实例化UploadFile类
				$uploadOb=new UploadFile();
				//指定允许的大小
				$uploadOb->maxSize=2000000;
				//指定允许的类型
				$uploadOb->allowType=array('image/jpeg','image/jpg','image/gif','image/png');
				//指定保存路径
				$uploadOb->savePath='./pictures/face/upload/';
				//调用上传所有文件的方法upload
				$result=$uploadOb->upload();
				//判断是否保存成功
				if ($result) {
					//获取上传图片的信息
					$arr=$uploadOb->getUploadFileInfo();
					$userFace=$arr[0]['savename'];
					$editInfo = new userModel();
					$re=$editInfo->editFieldOne("xgj_users", "face", "{$userFace}", "WHERE `user_id` = '{$userId}'");
					//判断成功就删除旧图片
					if ($re) {
						$oldName=$_POST['oldimg'];
						// 删除单张图片
						if(!empty($oldName)){
							@unlink(WWW_DIR."/pictures/face/upload/$oldName");
						}
					}
				}else{
					$str=$uploadOb->getErrorMsg();
				//	echo $str;exit;
				}
			}
			$infoAlias = $_POST["infoAlias"];	//昵称
			$infoMobilePhone = $_POST["infoMobilePhone"];	//手机
			$infoEmail = $_POST["infoEmail"];	//邮箱
			//$infoAddr = $_POST["infoAddr"];	//地址
			
			$infoIdentityCard = $_POST["infoIdentityCard"];	//身份证
			$birYear = $_POST["birYearSelName"];	//生日(年份)
			$birMonth = $_POST["birMonthSelName"];	//生日(月份)
			$birDate = $_POST["birDateSelName"];	//生日(日期)
			$birthday = $birYear."-".$birMonth."-".$birDate;	//生日
			$sex = $_POST["sex"];	//性别
			$infoMonthlyProfit = $_POST["infoMonthlyProfitSelName"];	//月收入
			$educationStatus = $_POST["educationStatusSelName"];   //教育程度
			if (isset($_POST["occupation "]))  { $occupation = $_POST["occupation "]; } //所在行业
            else { $occupation=""; };
			$userEdit = new userModel();
			
			$rs = $userEdit->userInfoEdit($infoAlias,$infoMobilePhone,$infoEmail,$infoIdentityCard,$birthday,$sex,$infoMonthlyProfit,$educationStatus,$occupation,$userId); 
             
			if($rs==1||$re==1){
				echo "<SCRIPT type='text/javascript'>alert('修改成功!!!');history.back();</SCRIPT>";
			}else{
				echo "<SCRIPT type='text/javascript'>alert('修改失败,请重试!!!');history.back();</SCRIPT>";
				exit();
			}

		}

	}


	/**
	 * 收货地址查询
	 */
	function addr(){
		
		$tpl = get_smarty();

		$addrInfo = new userModel();
		$addrInfoList = $addrInfo->addrInfoSel($_COOKIE["userId"]);
		$addrCount = $addrInfo->addrCount($_COOKIE["userId"]);
       
		$tpl->assign("addrInfoList",$addrInfoList);
		$tpl->assign("addrCount",$addrCount["addrCount"]);

		$tpl->display('user_address.tpl.html');
	}


	/**
	 * 默认地址
	 */
	function addrDefaultSet(){
		$addrId = $_GET["addrId"];
		$addrDefaultSet = new userModel();
		$addrDefaultResult = $addrDefaultSet->addrDefaultSet($addrId,$_COOKIE["userId"]);

		if($addrDefaultResult > 0){
			header("Location:user.php?addr");
		}else{
			echo "<SCRIPT type='text/javascript'>alert('设置为默认地址失败,请重试!!!');history.back();</SCRIPT>";
			exit();
		}
	}


	/**
	 * 显示添加收货地址页面
	 */
	function addrInfoAddShow(){

		$tpl = get_smarty();

		$tpl->display('user_addrAdd.tpl.html');

	}

	/**
	 * 实行收货地址添加
	 */
	function doAddrInfoAdd(){

		if($_POST){
			$userId = $_COOKIE["userId"];	//用户ID
			$receivingName = $_POST["receivingName"];	//收货人姓名
			$mobile = $_POST["mobile"];	//收货人手机
			$phone = $_POST["phone"];	//收货人手机
			$email = $_POST["email"];	//收货人邮箱
			$addr = $_POST["addr"];	//收货人详细地址
			$doAddrInfoAdd = new userModel();
			$doAddrInfoAddResult = $doAddrInfoAdd->doAddrInfoAdd($userId, $receivingName, $mobile, $phone, $email, $addr);

			if($doAddrInfoAddResult > 0){
				header("Location:user.php?addr");
			}else{
				echo "<SCRIPT type='text/javascript'>alert('添加地址失败,请重试!!!');history.back();</SCRIPT>";
				exit();
			}
		}

	}


	/**
	 * 显示修改收货地址
	 */
	function addrInfoEditShow(){

		$tpl = get_smarty();

		$addrId = $_GET["addrId"];
		$addrInfoSelforEdit = new userModel();
		$addrInfoSelList = $addrInfoSelforEdit->addrInfoSelOne($addrId);

		$tpl->assign("addrInfoSelList",$addrInfoSelList);
		$tpl->display('user_addrEdit.tpl.html');

	}

	/**
	 * 实行修改收货地址
	 */
	function doAddrInfoEdit(){

		if($_POST){

			$addrId = $_POST["addrId"];	//地址(主键)ID
			$receivingName = $_POST["receivingName"];	//收货人姓名
			$mobile = $_POST["mobile"];	//收货人手机
			$phone = $_POST["phone"];	//固定电话
			$email = $_POST["email"];	//收货人邮箱
			$province=$_POST["province"];//省
			$city=$_POST["city"];//市
			$xian=$_POST["xian"];//县
			$addr=$province.$city.$xian;
			//$addr = $_POST["addr"];	//收货人详细地址
			$doAddrInfoAdd = new userModel();
			$doAddrInfoEditResult = $doAddrInfoAdd->doAddrInfoEdit($addrId, $receivingName, $mobile, $phone, $email, $addr);

			if($doAddrInfoEditResult > 0){
				header("Location:user.php?addr");
			}else{
				echo "<SCRIPT type='text/javascript'>alert('修改地址失败,请重试!!!');history.back();</SCRIPT>";
				exit();
			}

		}

	}


	/**
	 * addrInfoDel	删除收货地址
	 */
	function addrInfoDel(){
		$addrId = $_GET["addrId"];
		$addrInfoDel = new userModel();
		$addrInfoDelResult = $addrInfoDel->addrInfoDel($addrId);

		if($addrInfoDelResult > 0){
			header("Location:user.php?addr");
		}else{
			echo "<SCRIPT type='text/javascript'>alert('删除地址失败,请重试!!!');history.back();</SCRIPT>";
			exit();
		}
	}


	/**
	 * register	显示注册页面
	 */
	function register()
	{

		$tpl = get_smarty();

		$tpl->display('register.tpl.html');
	}


	/**
	 * checkRegister     表示验证用户名的方法
	 * @param	null
	 * @return	void
	 */
	function checkRegister(){
		
		$user = new userModel();
		if(isset($_POST["usermobile_phone"]))
		{
		
		  $user->registerSelCheckmobile_phone($_POST["usermobile_phone"]);
		
		}
	     if(isset($_POST["userName"]))
	     {
		   $user->registerSelCheckName($_POST["userName"]);
	     }

	     if(isset($_POST["rollinto"])&&isset($_POST["rollintopassword"]))
	     {
	     	$status=$user->registerSelCheckroll($_POST["rollinto"],$_POST["rollintopassword"]);
	     	
	     }
	     
	   
		
	}
	
	
   
	/**
	 * dohousedata   表示添加房屋信息的方法
	 * 
	 * 
	 */

	function  dohousedata(){
		if (!empty($_POST)) {
			$_SESSION['price_post']=$_POST;
			$_SESSION['redirect_url'] = 'user.php?dohousedata';
		}
		if (empty($_COOKIE['userName'])) {
			header("Location:user.php?login");exit;
		}

		if (!empty($_SESSION['price_post'])) {
			$_POST = $_SESSION['price_post'];
			$_GET['cid'] = $_SESSION['price_post']['cid'];
		}

		

	   $userhouse = new userModel();

	   // echo '<pre>';
	   // var_dump($_POST);exit;

	   if ($_POST['house'] == 1 || $_POST['house'] == 2) {
	       $_POST['type6'] = '';
	       $_POST['type7'] = '';
	       $_POST['gelou'] = '';
	       $_POST['database'] = '';
	   }

	   if(!empty($_GET['cid']))
	    {
	   		$cid=$_GET['cid'];
	    }


	   if($_POST)
	   {  
	   	
	   	$province="";
	   	$city="";
	   	$town="";
	   	$detail="";
	   	$housetype=""; 	
	   	$area1="";
	   	$area2="";
	   	$area3="";
	   	$area4="";
	   	$area5="";
	   	$area6="";
	   	$area7="";
	   	$type1="";
	   	$type2="";
	   	$type3="";
	   	$type4="";
	   	$type5="";
	   	$type6="";
	   	$type7="";
	   	
	   	
	    	//省级
	   	   if(!empty($_POST['cho_Province']))
	   	   {
	   	      $province=$_POST['cho_Province'];
	   	   }
	   	  //市级
	   	  if (!empty($_POST['cho_City']))
	   	  {
              $city=$_POST['cho_City'];
	   	  }
	   	  //县 区
	   	  if(!empty($_POST['cho_Area']))
	   	  {
              $town=$_POST['cho_Area'];
	   	  }
	   	   //详细地址
	   	   if(!empty($_POST['diqu']))
	   	   {
            $detail=$_POST['diqu'];
            if($_POST['diqu']=="请输入地址")
            {
            	$detail="";
            }
	   	   }
	   	   //房屋类型
	   	   if(!empty($_POST['house']))
	   	   {
            $housetype=$_POST['house'];
	   	   }
	   	   
	
	   	   // 室
	   	    if(!empty($_POST['type1']))
	   	    {
               $type1=$_POST['type1'];
	   	    }else{
	   	    	$type1=0;
	   	    }
	   	    // 厅
	       if(!empty($_POST['type2']))
	   	    {
               $type2=$_POST['type2'];
	   	    }else{
	   	    	$type2=0;
	   	    }
	   	    // 厨
	      if(!empty($_POST['type3']))
	   	    {
               $type3=$_POST['type3'];
	   	    }else{
	   	    	$type3=0;
	   	    }
	   	    // 卫
	      if(!empty($_POST['type4']))
	   	    {
               $type4=$_POST['type4'];
	   	    }else{
	   	    	$type4=0;
	   	    }
	   	    // 阳台
	      if(!empty($_POST['type5']))
	   	    {
               $type5=$_POST['type5'];

	   	    }else{
	   	    	$type5=0;
	   	    }

	   	 
	   	    // 阁楼
	      if(!empty($_POST['type6']))
	   	    {
               $type6=$_POST['type6'];
	   	    }else{
	   	    	$type6='0';
	   	    }
	   	    
	   	    // 地下室
	   	    
	      if(!empty($_POST['type7']))
	   	    {
               $type7=$_POST['type7'];
	   	    }else{
	   	    	$type7=0;
	   	    }
          	    
          	 if(empty($type1))
          	 {
          	 	$type1="0";
          	 }
          	    
           	 $sum=$type1+$type6+$type7;
           	 
             $str=$sum;

         
              
          	 if(!empty($type2))
          	 {
        	 	 $str.=",".$type2;
          	 
          	 }else{
          	 	$str.=",0";
          	 }

          	 if(!empty($type3))
          	 {
          	 	$str.=",".$type3;
          	 }else{
          	 	$str.=",0";
          	 }

          	 if(!empty($type4))
          	 {
          	 	$str.=",".$type4;
          	 }else{
          	 	$str.=",0";
          	 }
        
          	 if(!empty($type5))
          	 {
          	 	$str.=",".$type5;
          	 }else{
          	 	$str.=",0";
          	 }
          	 
             
             
	   	    
	   	     // 全屋面积
	   	     if(!empty($_POST['area']))
	   	     {
	             $area=$_POST['area'];
	   	     }

	   	     

	   	     // if (empty($area)) {
	   	     // 	echo "<script>alert('请填写完整再提交，谢谢！')</script>";
	   	     // 	header("Location:price.php?cid=$cid");exit;
	   	     // }
	   	     
	   	     
	   	     
	   	     //室面积
	   	     if(!empty($_POST['bedroom']))
	   	     {
	   	       $arr1=$_POST['bedroom'];
	   	     }else{
	   	     	$arr1=0;
	   	     }

	   	     if(!empty($arr1))
	   	     {  
	   	        $area1=implode(",", $arr1);
	   	        $area_1=$area1;
	   	        $area_1_1 = $area_1;
	   	     }
	 
	   	     //厅面积
	   	     if(!empty($_POST['liveroom']))
	   	     {
	   	        $arr2=$_POST['liveroom'];  
	   	     }else{
	   	     	$arr2=0;
	   	     }
	   	       
	   	     //厨房面积
	   	     if(!empty($_POST['kitchen']))
	   	     {
	   	      $arr3=$_POST['kitchen'];
	   	     }else{
	   	     	$arr3=0;
	   	     }
	   	     //浴室面积
	   	     if (!empty($_POST['bathroom']))
	   	     {
	   	      $arr4=$_POST['bathroom'];
	   	     }else{
	   	     	$arr4=0;
	   	     }
	   	     //阳台面积
	   	     if(!empty($_POST['balcony']))
	   	     {
	   	       $arr5=$_POST['balcony'];
	   	     }else{
	   	     	$arr5=0;
	   	     }
	   	     //阁楼面积
	   	     if(!empty($_POST['gelou']))
	   	     {
	   	      $arr6=$_POST['gelou'];
	   	     }else{
	   	     	$arr6=0;
	   	     }
	   	     if(!empty($_POST['database']))
	   	     {
	   	     //地下室面积
	   	      $arr7=$_POST['database'];
	   	     }else{
	   	     	$arr7=0;
	   	     }

	   	     if ($arr1==0) {
           	$area1 =0;
           }

           if ($arr2==0) {
           	$area2 =0;
           }

           if ($arr3==0) {
           	$area3 =0;
           }
           if ($arr4==0) {
           	$area4 =0;
           }
           if ($arr5==0) {
           	$area5 =0;
           }
           if ($arr6==0) {
           	$area6 =0;
           }
           if ($arr7==0) {
           	$area7 =0;
           }

	   	    

	   	     // if(!empty($arr2))
	   	     // {	
	   	     //     $area2=implode(",", $arr2);

	   	         
	   	         
	   	     //     if(!empty($arr3) or !empty($arr4) or!empty($arr5))
	   	     //     {
	   	     //   	     $area2.="|";
	   	       	    
	   	     //     }
              
	   	     // }
	   	   if(!empty($arr2) || $arr2==0)
           {
           	if ($arr2!=0) {
           		 $area2=implode(",", $arr2);
           	}
           	 $area2.="|";
         
           }
	   	     
	   	     
           
           if(!empty($arr3) || $arr3==0)
           {
           	if ($arr3!=0) {
           		 $area3=implode(",", $arr3);
           	}
           	 $area3.="|";
         
           }
           
  

         
           if(!empty($arr4) || $arr4==0)
           {
           	if ($arr4!=0) {
	            $area4=implode(",", $arr4);
	        }
            
             $area4.="|";
            
           }
            
           
             if(!empty($arr5))
            {
               $area5=implode(",", $arr5);
               
            } 
            
            
            if(!empty($arr6) && empty($arr7))
            {	
              $area6=implode(",", $arr6);
              $area1.=",".$area6.',0';
        
            } 
           
           if(!empty($arr7) && empty($arr6))
           {
           	
              $area7=implode(",", $arr7);
             
              $area1.=",0,".$area7;
            
           }
           if(!empty($arr7) && !empty($arr6))
           {
           	
              $area7=implode(",", $arr7);
              $area6=implode(",", $arr6);
             
              $area1.=",".$area6.",".$area7;
            
           }
            if(empty($arr7) && empty($arr6))
           {
           	
           $area1.=",0,0";
            
           }

           

          //   echo $area1;echo '<br>';
	   	     // echo $area2;echo '<br>';
	   	     // echo $area3;echo '<br>';
	   	     // echo $area4;echo '<br>';
	   	     // echo $area5;echo '<br>';
	   	     // echo $area6;echo '<br>';
	   	     // echo $area7;echo '<br>';

	   	    
           
        if(!empty($area2)or!empty($area3)or!empty($area4)or!empty($area5))
            {
              $str2=$area1."|"."".$area2."".$area3."".$area4."".$area5;
            }
            
	   	else{
	   	  	    $str2=$area1."".$area2."".$area3."".$area4."".$area5;
	   	     }
	       
	    }

	    
	  
	  	// echo $str2;exit;
	    if ($area == '' || $housetype=='') {
	    	header("Location:price.php?cid=$cid&error=1");exit;
	    }
	    
	   
	    if (!empty($arr1)) {
	    	foreach ($arr1 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }  

	    }

	    if (!empty($arr2)) {
		    foreach ($arr2 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
		}

	    if (!empty($arr3)) {
		    foreach ($arr3 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
	    }

	    if (!empty($arr4)) {
		    foreach ($arr4 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
	    }

	    if (!empty($arr5)) {
		    foreach ($arr5 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
	    }

	    if (!empty($arr6)) {
		    foreach ($arr6 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
		}

	    if (!empty($arr7)) {
		    foreach ($arr7 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
		}

	    $attic = $type6;
	    $basement = $type7;
	    $attic_area = $area6;
		$basement_area = $area7;

	    $house_data = $userhouse->select_data($_COOKIE["userId"]);
	    
	    if (empty($house_data)) {

	    	$houseid=$userhouse->dohousedata($_COOKIE["userId"],$province,$city,$town,$detail,$housetype,$str,$area,$str2,$attic,$basement,$attic_area,$basement_area,$area_1);

	    //如果成功，获取系统id
		    if($houseid==true){
		    	unset($_SESSION['price_post']);
	    		header("Location:price.php?cid=$cid");exit;
		    }
		}else{


	    	
	  //   	$house_data = $userhouse->select_data($_COOKIE["userId"]);
		 //    $house_layout = explode('|', $house_data['0']['type_area']);

			// $area_1 = $house_data['0']['area'];
			// $attic_area = $house_data['0']['attic_area'];
		 //    $basement_area = $house_data['0']['basement_area'];

		 //    if (!empty($arr1) && empty($arr6) && empty($arr7)) {
		 //    	$house_layout['0'] = $area_1_1.','.$attic_area.','.$basement_area;
		 //    	$area_1 = $area_1_1;
		 //    }
		 //    if (!empty($arr6) && empty($arr6) && empty($arr7)) {
		 //    	$house_layout['0'] = $area_1.','.$area6.','.$basement_area;
		 //    	$attic_area = $area6;
		 //    }
		 //    if (!empty($arr7) && empty($arr6) && empty($arr7)) {
		 //    	$house_layout['0'] = $area_1.','.$attic_area.','.$area7;
		 //    	$basement_area = $area7;
		 //    }

		 //    if (!empty($arr1) && !empty($arr6) && empty($arr7)) {
		 //    	$house_layout['0'] = $area_1_1.','.$area6.','.$basement_area;
		 //    	$area_1 = $area_1_1;
		 //    	$attic_area = $area6;
		 //    }

		 //    if (!empty($arr1) && !empty($arr7) && empty($arr6)) {
		 //    	$house_layout['0'] = $area_1_1.','.$attic_area.','.$area7;
		 //    	$area_1 = $area_1_1;
		 //    	$basement_area = $area7;
		 //    }

		 //    if (!empty($arr6) && !empty($arr7) && empty($arr1)) {
		 //    	$house_layout['0'] = $area_1.','.$area6.','.$area7;
		 //    	$attic_area = $area6;
		 //    	$basement_area = $area7;
		 //    }

		 //    if (!empty($arr1) && !empty($arr6) && !empty($arr7)) {
		    	
		 //    	$house_layout['0'] = $area_1_1.','.$area6.','.$area7;
		 //    	$area_1 = $area_1_1;
		 //    	$attic_area = $area6;
		 //    	$basement_area = $area7;
		 //    }
		   	
		 //   	if (!empty($arr2)) {
		 //   		$house_layout['1'] = trim($area2,'|');
		 //   	}
		 //   	if (!empty($arr3)) {
		 //   		$house_layout['2'] = trim($area3,'|');
		 //   	}
		 //   	if (!empty($arr4)) {
		 //   		$house_layout['3'] = trim($area4,'|');
		 //   	}
		 //   	if (!empty($arr5)) {
		 //   		$house_layout['4'] = trim($area5,'|');
		 //   	}

		 //   	echo $str2;echo "<br>";

		   	// $str2 = $house_layout['0'].'|'.$house_layout['1'].'|'.$house_layout['2'].'|'.$house_layout['3'].'|'.$house_layout['4'];
		   	// echo $area6;exit;
		   	// echo $area7;exit;
		   	// echo $str2;exit;

		   	$attic = $type6;
		   	$basement = $type7;
		   	$attic_area = $area6;
		   	$basement_area = $area7;

	    	$return = $userhouse->updata_data($_COOKIE["userId"],$province,$city,$town,$detail,$housetype,$str,$area,$str2,$attic,$basement,$attic_area,$basement_area,$area_1);
	    	unset($_SESSION['price_post']);
    		
	    	// $house_data = $userhouse->select_data($_COOKIE["userId"]);
    		// $_SESSION['house_data'] = $house_data;

    		header("Location:price.php?cid=$cid");exit;

	    }	
	
	   	  
	  
	  
	}
	// function  dohousedata(){	
		
	//    $userhouse = new userModel();
	
	   
	//    if($_POST)
	//    {  
	   	
	//    	$province="";
	//    	$city="";
	//    	$town="";
	//    	$detail="";
	//    	$housetype=""; 	
	//    	$area1="";
	//    	$area2="";
	//    	$area3="";
	//    	$area4="";
	//    	$area5="";
	//    	$area6="";
	//    	$area7="";
	//    	$type1="";
	//    	$type2="";
	//    	$type3="";
	//    	$type4="";
	//    	$type5="";
	//    	$type6="";
	//    	$type7="";
	   	
	   	
	//     	//省级
	//    	   if(isset($_POST['cho_Province']))
	//    	   {
	//    	      $province=$_POST['cho_Province'];
	//    	   }
	//    	  //市级
	//    	  if (isset($_POST['cho_City']))
	//    	  {
 //              $city=$_POST['cho_City'];
	//    	  }
	//    	  //县 区
	//    	  if(isset($_POST['cho_Area']))
	//    	  {
 //              $town=$_POST['cho_Area'];
	//    	  }
	//    	   //详细地址
	//    	   if(isset($_POST['diqu']))
	//    	   {
 //            $detail=$_POST['diqu'];
 //            if($_POST['diqu']=="请输入地址")
 //            {
 //            	$detail="";
 //            }
	//    	   }
	//    	   //房屋类型
	//    	   if(isset($_POST['house']))
	//    	   {
 //            $housetype=$_POST['house'];
	//    	   }
	   	   
	
	//    	   // 室
	//    	    if(isset($_POST['type1']))
	//    	    {
 //               $type1=$_POST['type1'];
	//    	    }
	//    	    // 厅
	//        if(isset($_POST['type2']))
	//    	    {
 //               $type2=$_POST['type2'];
	//    	    }
	//    	    // 厨
	//       if(isset($_POST['type3']))
	//    	    {
 //               $type3=$_POST['type3'];
	//    	    }
	//    	    // 卫
	//       if(isset($_POST['type4']))
	//    	    {
 //               $type4=$_POST['type4'];
	//    	    }
	//    	    // 阳台
	//       if(isset($_POST['type5']))
	//    	    {
 //               $type5=$_POST['type5'];
	//    	    }
               
	//    	    // 阁楼
	//       if(isset($_POST['type6']))
	//    	    {
 //               $type6=$_POST['type6'];
	//    	    }
	//    	    // 地下室
	   	    
	//       if(isset($_POST['type7']))
	//    	    {
 //               $type7=$_POST['type7'];
	//    	    }
          	    
          	    
 //           	 $sum=$type1+$type6+$type7;
           	 
 //             $str=$sum;
              
 //          	 if(!empty($type2))
 //          	 {
 //        	 	 $str.=",".$type2;
          	 
 //          	 }
 //          	 if(!empty($type3))
 //          	 {
 //          	 	$str.=",".$type3;
 //          	 }
 //          	 if(!empty($type4))
 //          	 {
 //          	 	$str.=",".$type4;
 //          	 }
        
 //          	 if(!empty($type5))
 //          	 {
 //          	 	$str.=",".$type5;
 //          	 }
             
             
	   	    
	//    	     // 全屋面积
	//    	     if(isset($_POST['area']))
	//    	     {
	//              $area=$_POST['area'];
	//    	     }
	   	     
	   	     
	   	     
	//    	     //室面积
	//    	     if(isset($_POST['bedroom']))
	//    	     {
	//    	       $arr1=$_POST['bedroom'];
	//    	     }
	//    	     if(!empty($arr1))
	//    	     {  
	//    	        $area1=implode(",", $arr1);
	//    	     }
	   	 
	//    	     //厅面积
	//    	     if(isset($_POST['liveroom']))
	//    	     {
	//    	        $arr2=$_POST['liveroom'];  
	//    	     }
	   	       
	//    	     //厨房面积
	//    	     if(isset($_POST['kitchen']))
	//    	     {
	//    	      $arr3=$_POST['kitchen'];
	//    	     }
	//    	     //浴室面积
	//    	     if (isset($_POST['bathroom']))
	//    	     {
	//    	      $arr4=$_POST['bathroom'];
	//    	     }
	//    	     //阳台面积
	//    	     if(isset($_POST['balcony']))
	//    	     {
	//    	       $arr5=$_POST['balcony'];
	//    	     }
	//    	     //阁楼面积
	//    	     if(isset($_POST['gelou']))
	//    	     {
	//    	      $arr6=$_POST['gelou'];
	//    	     }
	//    	     if(isset($_POST['database']))
	//    	     {
	//    	     //地下室面积
	//    	      $arr7=$_POST['database'];
	//    	     }
	//    	     if(!empty($arr2))
	//    	     {	
	//    	         $area2=implode(",", $arr2);
	   	         
	//    	         if(!empty($arr3) or !empty($arr4) or!empty($arr5))
	//    	         {
	//    	       	     $area2.="|";
	   	       	  
	   	       	    
	//    	         }
              
	//    	     }
	   	     
	   	     
           
 //           if(!empty($arr3))
 //           {
 //             $area3=implode(",", $arr3);
 //             if(!empty($arr4) or !empty($arr5))
 //             { 
 //             	$area3.="|";
 //             }
 //           }
           
          
 //           if(!empty($arr4))
 //           {
 //             $area4=implode(",", $arr4);
 //             if(!empty($arr5))
 //             {
 //             	$area4.="|";
 //             }
 //           }
            
           
 //             if(!empty($arr5))
 //            {
 //               $area5=implode(",", $arr5);
               
               
 //            }  
            
            
 //            if(!empty($arr6))
 //            {	
 //              $area6=implode(",", $arr6);
 //              $area1.=",".$area6;
        
 //            } 
           
 //           if(!empty($arr7))
 //           {
           	
 //              $area7=implode(",", $arr7);
             
 //              $area1.=",".$area7;
            
 //           }
           
 //            if(!empty($arr2)or!empty($arr3)or!empty($arr4)or!empty($arr5))
 //            {
 //              $str2=$area1."|"."".$area2."".$area3."".$area4."".$area5;
 //            }
            
	//    	else{
	//    	  	    $str2=$area1."".$area2."".$area3."".$area4."".$area5;
	//    	     }
	       
	//     }

	//     if(isset($_GET['cid']))
	//     {
	//    		$cid=$_GET['cid'];
	//     }

	//     $house_data = $userhouse->select_data($_COOKIE["userId"]);

	//     if (empty($house_data)) {
	//     	 $houseid=$userhouse->dohousedata($_COOKIE["userId"],$province,$city,$town,$detail,$housetype,$str,$area,$str2);

	//      //如果成功，获取系统id

	//      if($houseid==true)
	//      {
	     	
	//          $house=$userhouse->dohouseid($_GET['cid']);
	         
	//          $houseid=$house['house_id'];
	          
	//         echo "<script>window.location.href='price.php?get_price&house_id= ".$houseid." &cat_id= ".$cid." ';</script>";
	        
	     	
	//      }

	//      if($houseid==true)
	//      {
	     	
	//          $house=$userhouse->dohouseid($_GET['cid']);
	         
	//          $houseid=$house['house_id'];
	         
	//        echo "<script>window.location.href=' price.php?get_price&house_id= ".$houseid." &cat_id= ".$cid." ';</script>";
	      
	     	
	//      }
	// 	    if($houseid==true)
	// 	    {
	// 	        $house=$userhouse->dohouseid($_GET['cid']);
		         
	// 	        $houseid=$house['house_id'];
		         
	// 	        echo "<script>window.location.href=' price.php?get_price&house_id= ".$houseid." &cat_id= ".$cid." ';</script>";
	// 	    }
	//     }else{
	//     	$return = $userhouse->updata_data($_COOKIE["userId"],$province,$city,$town,$detail,$housetype,$str,$area,$str2);



	//     	if ($return == 1) {
	    		
	//     		$house_data = $userhouse->select_data($_COOKIE["userId"]);

	//     		$_SESSION['house_data'] = $house_data;

	//     		header("Location:price.php?cid=$cid");exit;

	//     	}

	//     }	
	
	   	  

	  
	  
	// }
	
	/**
	 * 显示健康家居房屋信息
	 * homeoffer
	 * 
	 */
	function homeoffer(){
		   $housedata=new userModel();
		  
		   $tpl=get_smarty(); 
		   
		   $home= $housedata->dohomedata($_COOKIE["userId"]);
		   
		   //给变量赋值
		   $tpl->assign("province",$home['province']);
		   $tpl->assign("city",$home['city']);
		   $tpl->assign("district",$home['district']);
		   $tpl->assign("address",$home['address']);
		   $tpl->assign("house_type",$home['house_type']);
		 
		   //房屋户型
		   $str1=explode(",", $home['house_layout']);
		  
		   //室
		   $tpl->assign("room1",$str1[0]);
		   //厅
		   $tpl->assign("room2",$str1[1]);
		   //厨
		   $tpl->assign("room3",$str1[2]);
		   //浴
		   $tpl->assign("room4",$str1[3]);
		   //阳台
		   $tpl->assign("room5",$str1[4]);

          
		     
		   //房屋总面积
		   $tpl->assign("total_area",$home['total_area']);
		
		   $tpl->display("homeoffer.tpl.html");
		
	}
 
	/**
	 * login	表示加载登录模板
	 */
	function login()
	{
		$tpl =get_smarty();
		/************************grass 2016-4-7**************************/
		if(isset($_SESSION['userId'])){
			//已经登陆
			if(isset($_SERVER['HTTP_REFERER'])){
				header('Location:'.$_SERVER['HTTP_REFERER']);
			}else{
				header('Location:index.php');
			}
		}

		/************************grass 2016-4-7**************************/
		 $tpl->display('login.tpl.html');
        
		
	}
     
     	
         
	/**
	 * doLogin	表示实行登录
	 * 
	 */
	function doLogin_old(){
         
		if($_POST){

			$userLogin = new userModel();

			$userData = $userLogin->doLogin(mysql_real_escape_string($_POST["userName"]), mysql_real_escape_string($_POST["passWord"]));

			//如果登录成功 修改IP，与登录时间 , 在把存入的商品放到数据库里
			if($userData)	
			{   //获取客户端ip地址
				$ip = $_SERVER["REMOTE_ADDR"];
			
				$userLogin->updateLogin($ip);
				
				
				//把购物车的商品放到数据库里
				if(!empty($_COOKIE["cart_id"]))
				{
				     $userLogin->goods();
				}
				
				else {
					echo "<script>window.location.href='index.php';</script>";
					
				}
				
			}
		   
		 }                                                                       

	}

/*************************************grass***2016-4-7*************************************/
	/**
	 * 执行登陆
	 */
	function doLogin(){
		if(!empty($_POST)){
			/*接受数据*/
			$username = isset($_POST['userName'])?$_POST['userName']:'';
			$password = isset($_POST['passWord'])?$_POST['passWord']:'';

			/*验证用户名或者密码不能为空*/
			if(empty($username) || empty($password)){
				die("<script>alert('用户名或者密码不能为空!!!');history.back();</script>");
			}

			/*验证用户名*/
			$username = mysql_real_escape_string($username);
			$password = md5($password);
			$sql      = "SELECT user_id,user_name,password FROM xgj_users WHERE user_name='{$username}'";
			$info     = mysql_fetch_assoc(mysql_query($sql));
			if(!$info){
				//用户名不存在
				die("<script>alert('用户名不存在!!!');history.back();</script>");
			}

			/*验证密码*/
			if($info['password'] !== $password){
				die("<script>alert('密码错误!!!');history.back();</script>");
			}

			/*保存用户信息*/
			$_SESSION['userId']   = $info['user_id'];
			$_SESSION['userName'] = $info['user_name'];
			if(isset($_POST['auto_login'])){
			    //自动登陆, 保存登陆信息一个礼拜
			    setcookie(session_name(),session_id(),time()+86400*7,'/');
			}

			/*登陆成功之后*/
			//将本地购物车商品保存到数据库中
			if(!empty($_SESSION['cart'])){
				$user_id = $_SESSION['userId'];
				$cart    = $_SESSION['cart'];
				foreach ($cart as $k=>$v) {
					$tmp           = explode('-', $k);
					$goods_id      = (int)$tmp[0];
					$goods_attr_id = mysql_real_escape_string($tmp[1]);
					$goods_num     = (int)$v;
				    
				    $sql = "SELECT * FROM xgj_eu_cart WHERE user_id={$user_id} AND goods_id={$goods_id} AND goods_attr_id='{$goods_attr_id}'";
				    if($result = mysql_query($sql)){
				    	if($row = mysql_fetch_assoc($result)){
				        	//已经存在相同的商品
				    		$sql = "UPDATE xgj_eu_cart SET goods_num=goods_num+{$goods_num} WHERE id={$row['id']}";
				    	}else{
				    		$sql = "INSERT INTO xgj_eu_cart (user_id,goods_id,goods_attr_id,goods_num) VALUES ('{$user_id}','{$goods_id}','{$goods_attr_id}','{$goods_num}')";
				    	}
				    	mysql_query($sql);
				    }
				}
			}

			//执行跳转
			if (isset($_SESSION['redirect_url'])) {
			    header("Location:".$_SESSION['redirect_url']);die;
			}else{
			    header("Location:index.php");die;       
			}
		}
	}

/*************************************grass***2016-4-7*************************************/


        

	/**
	 * userQuit	退出
	 */
	function userQuit(){
		  
		// setcookie("userId","",time()-10);
		// setcookie("userName","",time()-10);
		unset($_SESSION['userId']);
		unset($_SESSION['userName']);


		unset($_SESSION['cat_id']);
		unset($_SESSION['furnish_quote']);
		unset($_SESSION['discount_amount']);
		unset($_SESSION['quote_gift']);
		unset($_SESSION['quote_name']);
		unset($_SESSION['house_datas']);
		unset($_SESSION['house_data']);
		unset($_SESSION['homebill_data']);
		unset($_SESSION['quote_gift']);
		unset($_SESSION['homebill_num']);
		unset($_SESSION['homebill_aaa']);
		unset($_SESSION['homebill']);
		unset($_SESSION['error']);
		unset($_SESSION['nonono']);
		unset($_SESSION['price_post']);
		 
		header("Location:index.php");
		 
	}


	/**
	 * 显示找回密码页面
	 */
	function findpassword(){
		$tpl = get_smarty();
   

		$tpl->display('findpassword.tpl.html');
	}
  
	/**
	 *   dofindpassword  执行找回密码操作
	 * 
	 */
	
	public function dofindpassword(){
		
		$findpassword= new userModel();
		if ($_POST['number_sj'] && $_POST['pass'] && $_POST['rpass']){
			if ($_POST['pass']!=$_POST['rpass']){
				echo "<script type='text/javascript'>alert('2次密码输入不一致，请重新输入密码！');history.back();</script>";
				exit();
			}
			$iPhonenum=mysql_real_escape_string($_POST["number_sj"]);
			$password=mysql_real_escape_string($_POST["rpass"]);
			$difindpassword=$findpassword->findpassworddata($iPhonenum,$password);
			
		}else {
			echo "<script type='text/javascript'>alert('请把数据项填写完整');history.back();</script>";
		}
		
	}

	/**
	 * 显示修改密码页面
	 */
	function passWordModifyShow(){

		user_check_logon();

		$tpl = get_smarty();

		$tpl->display('userPassWordModify .tpl.html');

	}
	
	/**
	 *    doregister     执行注册 
	 * 
	 */
	function doregister(){
		//var_dump($_POST);exit;
		if($_POST){
			if($_POST['password']!=$_POST['password02']){
				echo jump(2,'两次密码不一致！',"user.php?register");
				header("refresh:2;url='user.php?register'" );exit;
			}
			$userdata= new userModel();
			if(15121117257==$_POST['msg']){
				if($_POST['type']==1){
					
		             //无卷注册
					if(isset($_POST["mobile_phone"]) && !empty($_POST["mobile_phone"]))
					{

					  $nregister=$userdata->doRegister($_POST["mobile_phone"],$_POST["username"],$_POST["password"]);	
					  //$nregister=$userdata->add111($_POST);		
					}
					//var_dump($nregister);exit;
					if($nregister==2){
						echo jump(2,'用户名已存在！',"user.php?register");
						header("refresh:2;url='user.php?register'" );exit;
					}else{
						echo jump(1,'注册成功！',"user.php?login");
						header("refresh:2;url='user.php?login'" );exit;
					}

				}elseif($_POST['type']==2){
					//有卷注册
					if(isset($_POST["mobile_phone_num02"]))
					{
						$hregister=$userdata->doRegister($_POST["mobile_phone_num02"],$_POST["userName_num02"],$_POST["password"]);
					}
					//如果注册成功  状态需要改变  (status)
					if($hregister==true)
					{
						$userdata->updatestatus($_SESSION["userId"]);
					
					}
					echo jump(1,'注册成功！',"user.php?login");
					header("refresh:2;url='user.php?login'" );
				}
			}else{
				echo jump(2,'验证码错误，请重新填写！',"user.php?register");
				header("refresh:2;url='user.php?register'" );
			}
			
		}		  
		
	}
    

	/**
	 * ajaxCheckPassWord	使用ajax检测原始密码是否正确
	 */
	function ajaxCheckPassWord(){
		//调用判断是否登录的方法
		user_check_logon();

		//实例化Model类
		$ajaxCheckPassWord = new userModel();

		//判断原始密码是否正确
		$checkResult = $ajaxCheckPassWord->checkPassWordToModify($_COOKIE["userId"], md5($_POST["oldPassWord"]));

		if($checkResult > 1){
			echo 1;
		}else{
			echo 0;
		}
	}


	/**
	 * doPassWordModify	执行修改密码
	 */
	function doPassWordModify(){

		//调用判断是否登录的方法
		user_check_logon();

		//判断是否有POST数据提交
		if(!empty($_POST)){
          
			$oldPassWord = md5($_POST["oldPassWordName"]);	//原始密码
			$newPassWord = md5($_POST["newPassWordName"]);	//新密码
             
			
			//实例化Model类
			$passWordModify = new userModel();

			//判断原始密码是否正确
			$checkResult = $passWordModify->checkPassWordToModify($_COOKIE["userId"], $oldPassWord);

			if(!empty($checkResult)){	//如果原始密码正确
				$passWordModifyResult = $passWordModify->PassWordToModify($_COOKIE["userId"], $oldPassWord, $newPassWord);

				if($passWordModifyResult > 0){
					echo "<SCRIPT type='text/javascript'>alert('修改密码成功!');window.location.href='user.php?center';</SCRIPT>";
					exit();
				}else{
					echo "<SCRIPT type='text/javascript'>alert('修改密码失败,请重试!');history.back();</SCRIPT>";
					exit();
				}
			}else{	//如果原始密码错误
				echo "<SCRIPT type='text/javascript'>alert('原始密码错误,请重试!');history.back();</SCRIPT>";
				exit();
			}

		}

	}


	/**
	 * 评价页面数据显示
	 */
	function evaluateShow(){

		//调用判断是否登录的方法
		user_check_logon();

		$tpl = get_smarty();

		$evaluateShow = new userModel();

		//待评价商品数量 《健康舒适家， 欧洲团代购， 德国母婴》
		
		//《健康舒适家》
		$waitAssessCount = $evaluateShow->waitAssessCount($_COOKIE["userId"]);
		
		
		//《欧洲团代购， 德国母婴》
		$RwaitAssessCount = $evaluateShow->waitAssessCountdata($_COOKIE["userId"]);
		//总评价商品数量
		
		
        $waitEvaluateCount=$waitAssessCount["state"] + $RwaitAssessCount["state"];
		//已评价数量  《健康舒适家， 欧洲团代购， 德国母婴》
		//已评价数量  《健康舒适家》
		$evaluatehome =  $evaluateShow->evaluatedCounthome($_COOKIE["userId"]);
       
		//已评价数量  《 欧洲团代购， 德国母婴》
		$evaluateEurope = $evaluateShow->evaluatedCountEurope($_COOKIE["userId"]);
		
		//$evaluatedCount = $evaluateShow->evaluatedCount($_COOKIE["userId"]);
		
	
		$evaluatedCount=$evaluatehome["dataCount"]+$evaluateEurope["dataCount"];
	
		
		
		//待评价订单信息  《健康舒适家， 欧洲团代购， 德国母婴》
		//待评价订单信息   《 欧洲团代购， 德国母婴》
		$orderInfoWaitAssessList = $evaluateShow->orderInfoAll("waitAssess");
		

	    //待评价订单信息 《健康舒适家》
	    $RorderInfoWaitAssessList = $evaluateShow->ordweInfoAlldata($_COOKIE["userId"]);
	  

		//已评论 信息   《健康舒适家， 欧洲团代购， 德国母婴》
		//已评论 信息   《 欧洲团代购， 德国母婴》
		$orderInfoEuropdata =$evaluateShow->evaluatedEurope($_COOKIE["userId"]); 
		$tpl->assign('orderInfoEuropdata',$orderInfoEuropdata);
         
		
		//已评论 信息  《健康舒适家》
		$orderInfohomedata =$evaluateShow->evaluatedhome($_COOKIE["userId"]);
		
		
		$tpl->assign('orderInfohomedata',$orderInfohomedata);
         
		
		
		$tpl->assign("waitEvaluateCount",$waitEvaluateCount);	//待评价数量
		$tpl->assign("evaluatedCount",$evaluatedCount);	//三个月内已评价内数量
		$tpl->assign("orderInfoWaitAssessList",$orderInfoWaitAssessList);	  //待评论信息   《 欧洲团代购， 德国母婴》
		$tpl->assign("RorderInfoWaitAssessList",$RorderInfoWaitAssessList);   //待评价订单信息 《健康舒适家
		

		$tpl->display('user_evaluate.tpl.html');
	}


	/**
	 * doEvaluate	执行评论(修改)操作
	 */
	function doEvaluate(){

		//调用判断是否登录的方法
		user_check_logon();

		//如果有POST方式数据提交
		if(!empty($_POST)){
	         $doEvaluate = new userModel();
			//健康舒适家评论
            if(isset($_GET["fu_id"]))   {
             	
            
			 $doEvaluateResult = $doEvaluate->doEvaluate($_GET["fu_id"], $_POST["content"]);

			if($doEvaluateResult > 0){
				echo "<SCRIPT type='text/javascript'>alert('评论成功!');window.location.href='user.php?evaluateShow';</SCRIPT>";
				exit();
			}else{
				echo "<SCRIPT type='text/javascript'>alert('评论失败,请重试!');history.back();</SCRIPT>";
				exit();
			 }
		
             }

            //欧洲团代购 ，德国母婴 评论
             if(isset($_GET["eu_id"]))   {
             
             	$EvaluateResult = $doEvaluate->europe($_GET["eu_id"], $_POST["content"]);

             	if($EvaluateResult > 0){
             		echo "<SCRIPT type='text/javascript'>alert('评论成功!');window.location.href='user.php?evaluateShow';</SCRIPT>";
             		exit();
             	}else{
             		echo "<SCRIPT type='text/javascript'>alert('评论失败,请重试!');history.back();</SCRIPT>";
             		exit();
             	}
             
             }
             
           
           }
		
	}


	/**
	 * 显示取消订单界面
	 * 
	 */
	function cancelOrderShow(){

		//调用判断是否登录的方法
		user_check_logon();

		$cancel = new userModel();
		

		//调用model层里面查询取消订单详情的列表
		$cancelOrderInfo = $cancel->cancelOrderShow($_COOKIE["userId"]);

		$tpl = get_smarty();

		$tpl->assign("cancelOrderInfo",$cancelOrderInfo);	//取消订单详情
		$tpl->display('user_cancelOrder.tpl.html');
         
	}


	/**
	 * returnShow	显示返修退换货列表页界面
	 */
	
	function returnShow(){
		//调用判断是否登录的方法
		user_check_logon();
	
		$returnGoods = new userModel();
	
		//可退货(已付款)的订单
		
		$returnGoodsList = $returnGoods->returnShow($_COOKIE["userId"], 7, 6);
		//退货的订单记录
		$returnedList = $returnGoods->returnedList($_COOKIE["userId"]);
		$tpl = get_smarty();
	  
		$tpl->assign("returnGoodsList", $returnGoodsList["dataSelResult"]);	//查询到的可退货信息数据列表
		$tpl->assign("page", $returnGoodsList["pageInfo"]["page"]);	//当前页
		$tpl->assign("pageCount", $returnGoodsList["pageInfo"]["pageCount"]);	//总页数
	
		$tpl->assign("returnedList", $returnedList);	//查询到的退货的信息数据列表
		
		
	
		$tpl->display('user_return.tpl.html');
	}
	

	/**
	 * returnApplicationShow	显示返修退换货操作(详情页)界面
	 */
	function returnApplicationShow(){
		//调用判断是否登录的方法
		user_check_logon();

		$tpl = get_smarty();

		$apply = new userModel();

		$applyInfo = $apply->applyInfo($_COOKIE["userId"], $_GET["classId"], $_GET["goodsId"]);

		$tpl->assign("applyInfo", $applyInfo);	//查询到的申请返修退换货的数据信息

		$tpl->display('user_returnapplication.tpl.html');
	}


	/**
	 * 执行返修退换货操作
	 */
	function doApply(){
		//调用判断是否登录的方法
		user_check_logon();
	
        //商品编号
		$order_sn= $_GET["order_sn"];
		// 服务类型 
		$returnType = $_POST["returnType"];
		//提交数量
		$goodsNumber = $_POST["qty_item_1"];		
		//客户姓名
		$username=$_POST["username"];
		//手机
		$mobile = $_POST["mobile"];
		//收货地址
		$address = $_POST["addess"];
		//问题描述 
		$returnRemark = $_POST["returnRemark"];

		$apply = new userModel();

		$applyResult = $apply->doApply($_COOKIE["userId"], $order_sn, $returnType, $username, $mobile, $address, $returnRemark);

		if($applyResult > 0){
			echo "<SCRIPT type='text/javascript'>alert('操作成功,请等待审核!');window.location.href='user.php?returnShow';</SCRIPT>";
			exit();
		}else{
			echo "<SCRIPT type='text/javascript'>alert('操作失败,请重试!');history.back();</SCRIPT>";
			exit();
		}
	}

	/**
	 * 显示个人资产(余额)页面
	 */
	function assetShow(){
		//调用判断是否登录的方法
		user_check_logon();

		$tpl = get_smarty();
		              
		//用户资产
		$userasset =new  userModel();
		
		//分页显示
		$dataasset = $userasset->assetinformation($_COOKIE["userId"], 3);
		$tpl->assign("moneyList", $dataasset["dataSelResult"]);	//查询到的数据列表
		$tpl->assign("page", $dataasset["pageInfo"]["page"]);	//当前页
		$tpl->assign("pageCount", $dataasset["pageInfo"]["pageCount"]);	//总页数
		
		$tpl->display('user_asset.tpl.html');
	}


	/**
	 * couponShow	显示优惠券页面
	 */
	function couponShow(){
		//调用判断是否登录的方法
		user_check_logon();
        
		$tpl = get_smarty();

		$couponList = new userModel();

		//查询优惠券总金额 
		$couponSum = $couponList->couponStatusCount("WHERE `user_id` = '{$_COOKIE["userId"]}'");

		//查询可用优惠券总金额
		$couponAvailable = $couponList->couponStatusCount("WHERE `user_id` = '{$_COOKIE["userId"]}' AND `c_status` = '0'");

		//查询优惠券信息
		$couponInfo = $couponList->couponInfoList($_COOKIE["userId"]);
		
	

		$tpl->assign("couponInfo",$couponInfo);
		$tpl->assign("couponSum",$couponSum["statusCount"]);	//所以优惠券总金额
		$tpl->assign("couponAvailable",$couponAvailable["statusCount"]);//所以未使用(可使用)的优惠券总金额
		$tpl->display('user_coupon.tpl.html');
	}

   
	
	/**
	 * 显示家居订单页面
	 */
	function homeOrderShow(){
		//调用判断是否登录的方法
		user_check_logon();

		$tpl = get_smarty();
		$order=new userModel();
			
        //全部订单
        $contentdata = $order->goodsdata($_COOKIE["userId"]);
    
        $order_id=$contentdata["order_id"];
        //施工系统
        $countPlan =$order->countPlan($_COOKIE["userId"]);
     
        //施工计划
        if ($countPlan){
        foreach ($countPlan  as $k=>$v){
        
         $Plan[]=$dataPlan = $order->constructPlan($_COOKIE["userId"],$v["quote_id"]);
         
        }
        
        //质量审核
        foreach ($countPlan as $k=>$v)
        {
          $Mgcheck[]=$checkData = $order->zhilPlan($_COOKIE["userId"],$v["quote_id"]);
        }
        //文件区域
        $dealerOrderFile = $order->file($_COOKIE["userId"]);
     
        //产品手册
        foreach ($countPlan as $k=>$v)
        {
        	
          $Product[]= $Producttxt =$order->Productfile($_COOKIE["userId"],$v["quote_id"]);
	    }

	    //施工计划
	    $tpl->assign('Plan',$Plan);
	    //质量审核
	    $tpl->assign('Mgcheck',$Mgcheck);
	    //文件区域
	    $tpl->assign('dealerOrderFile',$dealerOrderFile);
	    //产品手册
	    $tpl->assign('Producttxt',$Product);
	    
        }
        //订单信息
        $tpl->assign("orderInfo",$contentdata );
        //系统名称
        $tpl->assign('countPlan',$countPlan);
        
		$tpl->display('user_homeorder.tpl.html');
	}
	  
	
	/**
	 *  downLoad  文件下载
	 */
    
	function downLoad(){
		 
		header("Content-type:text/html;charset=utf-8");
		$file_name = $_GET['file_name'];
	
		//用以解决中文不能显示出来的问题
		$file_name=iconv("utf-8","gb2312",$file_name);
	
		$file_sub_path=$_SERVER['DOCUMENT_ROOT']."/xgj/source/xgjweb/pictures/file/upload/";
		$file_path=$file_sub_path."$file_name";
	
		//首先要判断给定的文件存在与否
		if(!file_exists($file_path)){
			echo "<script>alert('没有该文件');</script>";
			echo "<script>window.location.href='user.php?homeOrderShow';</script>";
			return ;
		}
		$fp=fopen($file_path,"r");
		$file_size=filesize($file_path);
		//下载文件需要用到的头
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length:".$file_size);
		Header("Content-Disposition: attachment; filename=".$file_name);
		$buffer=1024;
		$file_count=0;
		ob_clean();
		 
		//向浏览器返回数据
		while(!feof($fp) && $file_count<$file_size){
			$file_con=fread($fp,$buffer);
			$file_count+=$buffer;
			ob_flush();
			echo $file_con;
		}
		fclose($fp);
	
	}
	
	/**
	 * 显示房屋例表页面
	 */
	function user_house(){
		
		//调用判断是否登录的方法
        user_check_logon();

        $user_house = new userModel();
		$house_info=$user_house->show_house_info($_SESSION['userId']);
		
		//print_r($house_info);

		if($house_info == null){
			header("Location:users.php/house_add");
		}else{

           $tpl = get_smarty();
		   $tpl->assign('cateid',$_GET['cateid']);
		   $tpl->assign('house_info',$house_info);
		   $tpl->display('user_house.tpl.html');
		}

	}
	
	 /**
	  * 显示房屋修改信息页面
	  * 
	  */
/*	  function  houseinformation(){
	  	
	  	user_check_logon();
	  	
	  	$tpl = get_smarty();
	  	
	  	$coupondata=new userModel();
	  	
	  	$data=$coupondata->docoupondata($_COOKIE["userId"]);
	  	
	  	$jifen=$data['coupon']/2;
	  
	  	$tpl->assign('coupon',$data['coupon']);
	  	$tpl->assign('jifen',$jifen);
	  	$tpl->display("houseinformation.tpl.html");
	  	
	  	
	  }*/
    
	function house_add(){
		

        user_check_logon();
        
        if(isset($_POST) && $_POST!=null)
		{   
			$house_info=array();
			$house_info['house_name']=trim($_POST['house_name']);
			$house_info['province']=$_POST['provinces'];
			$house_info['city']=$_POST['citys'];
			$house_info['district']=$_POST['town'];
			$house_info['user_id']=$_SESSION['userId'];
			$house_info['total_area']=$_POST['total_area'];
		    $user_house = new userModel();
		    $result=$user_house->add_house_info($house_info);
			header("Location:user.php?house");
			
		}
	 	$str=file_get_contents('js/area_data.js');
		$place_data=json_decode($str);
        
        $tpl = get_smarty();
		$tpl->assign('place_data',$place_data);
		$tpl->display('user_house_add.tpl.html');
		

	}

	//获取短信验证码
	function message(){

		//var_dump($_POST['tel']);exit;
		$tel=$_POST['tel'];
		$rest=getMessage($tel);
		//var_dump($rest);exit;
		if($rest['success']===true){
			echo 1;
			exit;
		}else{
			echo -1;
			exit;
		}
		//var_dump($rest);exit;
	}
}
?>
