<?php
/**
 * Created by PhpStorm.
 * User: 唐文权
 * Date: 2015/12/21
 * Time: 16:29123
 */


header("Content-type:text/html; charset=utf-8");
require_once(WWW_DIR."/conf/mysql_db.php");
require_once(WWW_DIR."/libs/T_Tool.class.php");
require_once (WWW_DIR . "/libs/db.php");
class userModel{

    private $m;
    public function __construct(){
        $this->m = new db();
    }

    /**
     * registerSelCheckName     表示验证用户名的方法
     * @param string $userName  表示用户输入的用户名
     */
    function registerSelCheckName($userName){
        $db=new db();
        $userNameSelSql = "SELECT `user_name` FROM `xgj_users` WHERE `user_name` = '{$userName}' LIMIT 1";
        $userNameSelResult =$db->getRow($userNameSelSql);
        if (empty($userNameSelResult["user_name"])) {
            echo 0;
            $_SESSION["userName"]=$userIdSelResult["user_name"];
            $_SESSION["userId"]=$userIdSelResult["user_id"];
        } else {
            echo 1;
        }
        exit();

    }
         
    /**
     *    registerSelCheckroll             表示验证入卷与密码的方法
     *    @param string $rollinto          表示用户输入的入卷号
     *    @param string $rollintopassword  表示用户输入的入卷密码
     */
    
    function   registerSelCheckroll($rollinto,$rollintopassword){
    	$db=new db();
    	$userrollSelSql ="SELECT `coupon_number` FROM `xgj_coupon` WHERE `coupon_number` = '{$rollinto}' AND `coupon_password` = '{$rollintopassword}' LIMIT 1";
    	$userrollSelResult=$db->getRow($userrollSelSql);
    	if(empty($userrollSelResult)){
    		echo 0; 		
    	}else {
    		echo 1; 
    		
    	} 		
    }
    
    /**
     *   updatestatus  表示如果注册成功 把 状态改变  
     * 
     */
     
     function  updatestatus($userid){
     	$db=new db();
     	$rollResult =$db->update('xgj_coupon',array('status'=>1),"user_id={$userid}");
     	if($rollResult) { echo "<script>window.location.href=\"user.php?index\"</script>";}
     	
     }
    
    /**
     * registerSelCkeckmobile_phone   表示验证用户手机号码的方法
     * @param string $mobile_phone    表示用户输入的手机号码
     */
    
    function  registerSelCheckmobile_phone($usermobile_phone){
        $db=new db();
    	$userPhoneSelSql = "SELECT `mobile_phone` FROM `xgj_users` WHERE `mobile_phone`='{$usermobile_phone}' LIMIT 1";
        $userPhoneSelResult=$db->getRow($userPhoneSelSql);
    	if(empty($userPhoneSelResult["mobile_phone"])) {
    		 echo 0;exit;
    	}else {
    		 echo 1;
    	}
    	exit();
    }
   

    /**
     * doRegister       表示实行注册的方法
     * @param   string  $userName   表示用户填写的用户名
     * @param   string  $passWord   表示用户填写的密码
     */



    function doRegister($mobile_phone,$userName,$passWord){
        $db=new db();
        $passWordMd5 = md5($passWord.MD5_PASSWORD);
        $re=$db->add('xgj_users',array('user_name'=>$userName,'password'=>$passWordMd5,'reg_time'=>time(),'mobile_phone'=>$mobile_phone));
        if($re){
            //设置保存时间
            $_SESSION['userId']=$re;
            $_SESSION['userName']=$userName;
            return 1;
        }else{
            return 0;
        }
    }

    function dowapreg(){
        
        if (empty($_POST['user_name']) || $_POST['user_name']=='输入昵称') {
            echo "请输入昵称";exit;
        }
        if(!preg_match("/^1[34578]\d{9}$/", trim($_POST['mobile_phone']))){
           echo "请输入正确的手机号码";exit;
        }
        if (empty($_SESSION['msg'])) {
            echo "请先获取验证码";exit;
        }
        if (empty($_POST['msg']) || $_POST['msg']=='输入验证码') {
            echo "请输入验证码";exit;
        }
        if ($_POST['msg']!=$_SESSION['msg']) {
            echo "您输入验证码不正确,请重试!";exit;
        }

        //if ($_POST['coupon_number']=='输入优惠劵号') $_POST['coupon_number'] = '';

        $userName     = trim($_POST['user_name']);
        $mobile_phone = trim($_POST['mobile_phone']);
        $passWordMd5  = md5(trim($mobile_phone).MD5_PASSWORD);
       // $coupon_number = trim($_POST['coupon_number']);

        $db=new db();

        //查询手机号码是否注册
        $sql = "SELECT `mobile_phone` FROM `xgj_users` WHERE `mobile_phone`='{$mobile_phone}'";
        $tel=$db->getRow($sql);
        if (!$tel) {
            //查询优惠券号码是否存在
            // if ($coupon_number!='') {
            //     $sql = "SELECT `status` FROM `xgj_coupon` WHERE `coupon_number`='{$coupon_number}' and status = '0'";
            //     $coupon=$db->getRow($sql);
            // }else{
            //     $coupon = true;
            // }
            // if ($coupon) {
            //     //查询优惠券号码是否注册
            //     $sql = "SELECT `coupon_number` FROM `xgj_users` WHERE `coupon_number`='{$coupon_number}'";
            //     $re=$db->getRow($sql);
            //     if (!$re) {
            //         $return=$db->add('xgj_users',array('user_name'=>$userName,'password'=>$passWordMd5,'reg_time'=>time(),'mobile_phone'=>$mobile_phone,'coupon_number'=>$coupon_number));
            //         if ($return) {
            //             unset($_SESSION['msg']);
            //             echo '1';
            //         }else{
            //             echo '抱歉，注册失败！';
            //         }
            //     }else{
            //         echo '抱歉，该优惠券号码已被注册！';
            //     }
            // }else{
            //     echo '抱歉，该优惠券号码不存在！';
            // }
            $return=$db->add('xgj_users',array('user_name'=>$userName,'password'=>$passWordMd5,'reg_time'=>time(),'mobile_phone'=>$mobile_phone));
                if ($return) {
                    unset($_SESSION['msg']);
                    echo '1';
                }else{
                    echo '抱歉，注册失败！';
                }
        }else{
            echo '抱歉，该手机已注册！';
        }

    }
    
    /**
     *   dohousedata     表示添加房屋信息的方法
     */

    function  dohousedata($userid,$province,$city,$town,$detail,$housetype,$str,$area,$str2,$attic,$basement,$attic_area,$basement_area,$area_1){

        $db=new db();
    	
       $houseSql="insert into `xgj_users_house`  (`user_id`,`province`,`city`,`district`,`address`,`house_type`,`house_layout`,`total_area`,`type_area`,`attic`,`basement`,`attic_area`,`basement_area`,`area`) VALUE ('{$userid}','{$province}','{$city}','{$town}','{$detail}','{$housetype}','{$str}','{$area}','{$str2}','{$attic}','{$basement}','{$attic_area}','{$basement_area}','{$area_1}')";
   
     
        $houseSqlRegReult = $db->query($houseSql);
        
        if($houseSqlRegReult){
        	
        	return true ;
            
        }
        else {

             echo "<script>alert('请稍后再试');history.back();</script>";
        }

    }
    
    /**
     * dohouseid 表示获取系统Id
     * 
     */
    function dohouseid($cid){
        $db=new db();

       $houseidSql="select `house_id` FROM `xgj_users_house` WHERE `user_id`='{$_SESSION["userId"]}' ORDER BY `house_id` DESC
                        LIMIT  1"; 
       
       $houseidResult=$db->getRow($houseidSql);
       
       	 return $houseidResult;
       	
       	 
    }
    
    /**
     *   dohomedata    表示查询房屋信息
     * 
     */
    function  dohomedata($userid){
    $db=new db();	
    $housedataSql="select *  from  `xgj_users_house` where  `user_id`= '{$userid}'";
    
    $housedataResult=$db->getRow($housedataSql);
    
    return  $housedataResult;	
    	
    }
    
    /**
     *   docoupondata  表示查询可用卷
     * 
     */
    function docoupondata($userid){
    	$db=new db();

    	$couponSql="SELECT `coupon` FROM `xgj_users` WHERE `user_id`='{$userid}'";
    	
    	$couponResult=$db->getRow($couponSql);

    	return  $couponResult;
    }

  /**
     *   docoupondata  表示查询可用卷
     * 
     */
    function dologinyz($username){
        $db=new db();

        $sql = "SELECT user_id,user_name,password FROM xgj_users WHERE mobile_phone='{$username}'";
        
        $couponResult=$db->getRow($sql);

        return  $couponResult;
    }

   //  /**
   //   * doLogin  表示实行登录{}
   //   * @param string $userName  用户提交的名户名
   //   * @param string $passWord  用户提交的密码
   //   * @return  void
   //   */
   //  function doLogin($userName, $passWord){

   //      //去除空格
   //      $passWordTrim = T_Tool::triMall($passWord);

   //      //MD5加密
   //      $passWordMd5 = md5($passWordTrim);

   //      $userNameLoginSql = "SELECT `user_id`,`user_name`,`password` FROM `xgj_users` WHERE `user_name` = '{$userName}' AND `password` = '{$passWordMd5}' LIMIT 1";

   //      $userNameSelResult = mysql_fetch_array(mysql_query($userNameLoginSql), MYSQL_ASSOC);

   //      if(!empty($userNameSelResult)){
	
   //      	$_SESSION=$userNameSelResult["user_name"];
   //          setcookie("userName",$userNameSelResult["user_name"]);
   //          setcookie("userId",$userNameSelResult["user_id"]);
   //           //跳转到首页
   //          if ($_SESSION['price_post']) {
   //              header("Location:user.php?dohousedata");exit;      
   //          }else{
   //              header("Location:index.php");             
   //              return;
   //          }
            
           
   //      }
   //      else{
             
   //          //如果登录失败
   //          echo "<script type=text/javascript>alert('密码错误!!!');history.back();</script>";
			// return $userNameSelResult;
   //      }
   //  }
    
    /**
     * updateLogin  表示修改客户端的IP与上次登录的时间
     * 
     * 修改  last_time ，last_ip
     */
   
    function updateLogin($ip){
        $db=new db();
    
    	$userSql="UPDATE `xgj_users` SET `last_time`= NOW(), `last_ip`= '{$ip}' WHERE `user_id`='{$_SESSION["userId"]}' ";
    
    	$userResult=$db->query($userSql);
    	
    }
    
    /**
     *   goodsdata    表示把购物车的商品放到数据库里  
     */
    
      
     function  goods(){
     	$db=new db();
     	$cart = $_COOKIE['cart'];
     	$data = array();
     	
     	foreach ($cart as $k=>$v) {
     		$tmp = explode('-', $k);
     		$data[] = array(
     				'goods_id'      => $tmp[0],
     				'goods_attr_id' => $tmp[1],
     				'goods_num'     => $v,
     				'user_id'       => 0,
     				'id'            => $k,
     		);
     	
     	//查 ，如果用户没有该商品 insert 否则 update
     	$goodsSql="SELECT `order_id`,`goods_number` FROM `xgj_eu_cart` WHERE `user_id`='{$_SESSION["user_id"]}' AND `goods_id`='{$tmp[0]}'";
     	$goodsResult=$db->getRow($goodsSql);
     	//有 ，update
     	if(!empty($goodsResult)){
     		$goods_number=$goodsResult['goods_number']+$v;
     		$goodsSql="UPDATE `xgj_eu_cart` SET `goods_number`='{$goods_number}' WHERE `user_id`='{$_SESSION["user_id"]}' AND `goods_id`='{$tmp[0]}'";
     		$goodsResult=$db->query($goodsSql);
     	}
     	//没有，insert 
     	else {
     		
     		     $goodsSql="INSERT INTO `xgj_eu_cart`( `id`,goods_number`,`goods_id`,`goods_attr_id`) VALUE ('{$k}',{$v}','{$tmp[0]}','{$tmp[1]}') ";
     		     
     		     $goodsResult=$db->query($goodsSql);
     		     
     		     if($goodsResult){
     		     	
     		     }    		
     	     } 
     	} 
     	  echo "<script>window.location.href='index.php';</script>";
     	
     }

     /**
     * centerSelInfo            表示在用户中心页面查询用户信息
     * @param string $userId    用户Id
     * @return array $centerSelResult     用户的信息
     */
     function centerSelInfo($userId){
        $db=new db();
        //查询用户信息的sql语句
        $centerSelSql = "SELECT`user_id`,`user_name`,`face`,`user_money`,`integral`,`coupon` FROM `xgj_users` WHERE
                            `user_id` = '{$userId}' LIMIT 1";

        //执行sql语句
        $centerSelResult = $db->getRow($centerSelSql);

        return $centerSelResult;
    }
    
    
    /**
     * centerSelInfo            表示在用户中心页面查询用户信息
     * @param string $userId    用户Id
     * @param string $userName  用户名
     * @return array $centerSelResult     用户的信息
     */
    /* function centerSelInfo($userId){
    
			    //查询用户信息的sql语句
			    $centerSelSql = "SELECT
			    `user_id`,
			    `user_name`,
			    `face`,
			    `user_money`,
			    `pay_points`,
			    FROM
			    `xgj_users` u join `xgj_coupon` c on `u.user_id`=`c.user_id`
			    WHERE
			    `u.user_id` = '{$userId}'";
	    
	    //执行sql语句
	    $centerSelResult = mysql_fetch_array(mysql_query($centerSelSql), MYSQL_ASSOC);
	    
	    return $centerSelResult;
    }  */


    /**
     * countAll     所有状态的总数
     * @param int $userId   用户ID
     * @return array $buyStatusSelResult    查询到的状态总数
     */
    function countAll($userId){
        $db=new db();
        $CountAllSelSql = "SELECT count(`pay_status`) 'state' FROM  `xgj_furnish_order_info` where `user_id` = '{$userId}'";
        $CountAllSelResult = $db->getRow($CountAllSelSql);
        return $CountAllSelResult;

    }
    
    /**
     * countAlldata  所有状态的总数   《欧洲团购，德国母婴》
     * @param int $userId   用户ID
     * @return array $CountAllSelResult_new    查询到的状态总数
     */
    function  countAlldata($userId){
        $db=new db();
    	$CountAllSelSql_new = "SELECT count(`order_status`) 'state' FROM `xgj_eu_order` where user_id = '{$userId}'
                          ORDER BY `id` DESC";

    	$CountAllSelResult_new = $db->getRow($CountAllSelSql_new);

    	return $CountAllSelResult_new;

    }


    /**
     * waitPayCount   表示查询待付款状态的订单数量
     * @param int $userId   用户ID
     * @return array $buyStatusSelResult    查询到的状态总数
     */
    function waitPayCount($userId){
        $db=new db();

        $waitPayCountSelSql = "SELECT count(`order_id`) 'state' FROM  `xgj_furnish_order_info` where `user_id` = '{$userId}' and `pay_status` = '0'";
        $waitPaySelResult = $db->getRow($waitPayCountSelSql);

        return $waitPaySelResult;

    }
    
    /**
     * waitPayCountdata   表示查询待付款状态的订单数量  《欧洲团购，德国母婴》
     * @param int $userId   用户ID
     * @return array $buyStatusSelResult_new    查询到的状态总数
     */
    function  waitPayCountdata($userId){
    $db=new db();	
    $waitPayCountSelSql_new = "SELECT count(`xgj_eu_order`.`id`) 'state' FROM   
                            `xgj_eu_order` INNER JOIN  `xgj_eu_order_goods` 
                                on `xgj_eu_order`.id=`xgj_eu_order_goods`.id
                            where
                               `is_pay` = 0 AND `xgj_eu_order`.user_id = '{$userId}'";
    $waitPaySelResult_new = $db->getRow($waitPayCountSelSql_new);
    return $waitPaySelResult_new;
    	
    }

   

    /**
     * waitReceivCount   表示查询待收货(已发货)状态的订单数量
     * @param int $userId   用户ID
     * @return array $buyStatusSelResult    查询到的状态总数
     */
    function waitReceivCount($userId){
        $db=new db();
        $waitReceivCountSelSql = "SELECT count(`order_id`) 'state' FROM  `xgj_furnish_order_info` where `user_id` = '{$userId}' and `shipping_status` = '0'";
        $waitReceivSelResult = $db->getRow($waitReceivCountSelSql);

        return $waitReceivSelResult;

    }
    
    /**
     * waitReceivCountdata  表示查询待收货(已发货)状态的订单数量    《欧洲团购，德国母婴》
     *  @param int $userId   用户ID
     *  @return array $buyStatusSelResult_new    查询到的状态总数
     */
    function waitReceivCountdata($userId){
    	$db=new db();
    	$waitReceivCountSelSql_new = "SELECT count(xgj_eu_order.`id`) 'state' FROM  `xgj_eu_order` where `user_id` = '{$userId}' and `order_status` = '1'";
    	$waitReceivSelResult_new = $db->getRow($waitReceivCountSelSql_new);
    	
    	return $waitReceivSelResult_new;
    	
    }
    

    /**
     * waitAssessCount   表示查询待评价状态的订单数量
     * @param int $userId   用户ID
     * @return array $buyStatusSelResult    查询到的状态总数
     */
    function waitAssessCount($userId){
        $db=new db();
        $waitAssessCountSelSql = "SELECT count(`xgj_furnish_order_info`.order_id) 'state' FROM  `xgj_furnish_order_info`,`xgj_furnish_order_detail` ,`xgj_furnish_quote`
   where  `xgj_furnish_order_info`.`order_id`=`xgj_furnish_order_detail`.`order_id`   
 AND  `xgj_furnish_order_detail`.`quote_id`=`xgj_furnish_quote`.quote_id  AND  `xgj_furnish_order_info`.user_id='{$userId}' and `is_comment`=0";
        $waitAssessSelResult = $db->getRow($waitAssessCountSelSql);

        return $waitAssessSelResult;

    }
    
    /**
     *  ordweInfoAlldata     表示查询待评价状态的订单信息
     *   @param int $userId   用户ID
     * 
     */
    
    function  ordweInfoAlldata($userId){
    	$db=new db();
    	$waitAssessCountSelSql = "SELECT *  FROM  `xgj_furnish_order_info`,`xgj_furnish_order_detail` ,`xgj_furnish_quote`
 where  `xgj_furnish_order_info`.`order_id`=`xgj_furnish_order_detail`.`order_id`   
 AND  `xgj_furnish_order_detail`.`quote_id`=`xgj_furnish_quote`.quote_id  AND  `xgj_furnish_order_info`.user_id='{$userId}' and `is_comment`=0 ";
        $waitAssessinfoResult=$db->getRow($waitAssessCountSelSql);
         // while ($list = mysql_fetch_array($waitAssessCountSelSql, MYSQL_ASSOC))
         // {
         //     	$waitAssessinfoResult[] = $list;
         	
         // }	
     
          return @$waitAssessinfoResult;
    	
    }
    
    /**
     * waitAssessCountdata   表示查询待评价状态的订单数量       《欧洲团购，德国母婴》
     * @param int $userId   用户ID
     * @return array $buyStatusSelResult_new    查询到的状态总数  
     */
     
    function waitAssessCountdata($userId){
     $db=new db();	
    $waitAssessCountSelSql_new = "SELECT count(`id`) 'state' from `xgj_eu_comment`
                        INNER JOIN    `xgj_eu_order`
                                on `xgj_eu_order`.id=`xgj_eu_comment`.comment_id 
                    WHERE  `xgj_eu_comment`.user_id ='{$userId}'";
    $waitAssessSelResult_new = $db->getRow($waitAssessCountSelSql_new);
    	
    return $waitAssessSelResult_new;
    	
    	
    }

    /**
     * goodsdata   表示商品信息
     * @param int $userId   用户ID
     * @return array $myOrderSelResult  查询到的我的商品总详细
     */
    
    function goodsdata($userId){
    $db=new db();
	$mygoodsSelSql = "select * from `xgj_furnish_order_info` where user_id='{$userId}'";
	$mygoodsSelResult=$db->getRow($mygoodsSelSql);
    return $mygoodsSelResult;
    
    }

    /**
     * 获取调整订单的所有信息
     * @param int $userId   用户ID
     * @return array $myOrderSelResult  查询到的我的商品总详细
     */
    
    function getAdjustData($order_id){
    $db=new db();
    $mygoodsSelSql = "select * from `xgj_furnish_order_adjust_info` where order_id='{$order_id}'";
    $mygoodsSelResult=$db->getRow($mygoodsSelSql);
    return $mygoodsSelResult;
    
    }
    
    /**
     *   constructPlan     表示查询施工计划信息
     * 
     */
    function constructPlan($userId,$quote_id){
    	$db=new db();
    	// $xinfSql=mysql_query("select *  from  `xgj_furnish_order_info` ,`xgj_furnish_order_construct`,`xgj_furnish_order_detail` where  `xgj_furnish_order_info`.order_id=`xgj_furnish_order_detail`.order_id=`xgj_furnish_order_detail`.order_id and	`xgj_furnish_order_info`.user_id='{$userId}'  and `xgj_furnish_order_detail`.quote_id='{$quote_id}' ");
        $xinfSql="SELECT * FROM xgj_furnish_order_construct a LEFT JOIN xgj_furnish_order_detail b ON a.detail_id=b.detail_id LEFT JOIN xgj_furnish_order_info c ON b.order_id=c.order_id WHERE b.quote_id='{$quote_id}' AND c.user_id='{$userId}'";
        //echo $xinfSql;die;
        $xinfResult=$db->getAll($xinfSql);
    	// while ($list = mysql_fetch_array($xinfSql,MYSQL_ASSOC)){
    	// 	 $xinfResult[] = $list; }
    
    	return @$xinfResult;
    	
   
    }
    
    /**
     *    countPlan  查询系统名称
     */
    function countPlan($userId){
    	$db=new db();
    	$countPlanSql="SELECT  a.quote_name ,a.quote_id ,c.alias FROM  xgj_furnish_order_detail a INNER JOIN   xgj_furnish_order_info b
             ON  a.order_id=b.order_id  INNER JOIN   xgj_furnish_quote c
			 ON  a.quote_id=c.quote_id
    		      WHERE  b.user_id='{$userId}'";
       $countPlanResult=$db->getAll($countPlanSql);
    	// while ($list = mysql_fetch_array($countPlanSql,MYSQL_ASSOC)){
    	// 	$countPlanResult[] = $list;
    	// }
    	
    	return @$countPlanResult;
         
    }
    
    /**
     *    zhilPlan    表示查询质量审核信息
     * 
     */
     
    function   zhilPlan($userId,$quote_id){
    	$db=new db();
  //   	$zhilSql=mysql_query("select *  from  `xgj_furnish_order_info` ,`xgj_furnish_order_construct`,`xgj_furnish_order_detail`
  // where  `xgj_furnish_order_info`.order_id=`xgj_furnish_order_detail`.order_id=`xgj_furnish_order_detail`.order_id
  //  and	`xgj_furnish_order_info`.user_id='{$userId}'  and  `xgj_furnish_order_detail`.quote_id='{$quote_id}' ");
    
        $zhilSql="SELECT * FROM xgj_furnish_order_construct a LEFT JOIN xgj_furnish_order_detail b ON a.detail_id=b.detail_id LEFT JOIN xgj_furnish_order_info c ON b.order_id=c.order_id WHERE b.quote_id='{$quote_id}' AND c.user_id='{$userId}'";
    	// while ($list = mysql_fetch_array($zhilSql,MYSQL_ASSOC)){
    	// 	 $zhilResult[] = $list; }
        $zhilResult=$db->getAll($zhilSql);
    	return @$zhilResult;
    
    	}
    
    	/**
    	 *    file    表示文件区域信息
    	 *
    	 */
        function file($userId){
    	   $db=new db();
    		$fileSql="select  *  from  `xgj_furnish_order_info`  inner  join   `xgj_furnish_order_file` 
    		  on `xgj_furnish_order_info`.order_id =`xgj_furnish_order_file`.order_id  where  
    				 `xgj_furnish_order_info`.user_id='{$userId}'    and  `xgj_furnish_order_file`.`status`=1";
    		
    		// while ($list = mysql_fetch_array($fileSql,MYSQL_ASSOC)){
    		// 	$fileResult[] = $list;
    		// }
    		$fileResult=$db->getAll($fileSql);
    		return @$fileResult;
    		
    	}
    	
    	/**
    	 * Productfile  表示产品手册信息
    	 */
    	
    	function   Productfile($userId,$quote_id){
    		$db=new db();
    		
    		$ProductfileSql="select  *  from  `xgj_furnish_order_file` f join `xgj_furnish_order_detail` d
       ON f.order_id =d.order_id WHERE  f.order_id='{$userId}'  AND  f .`status`=2   and  d.quote_id=
            '{$quote_id}'";
            $ProductfileResult=$db->getAll($ProductfileSql);
    		return @$ProductfileResult;
    		
    		
    		
    	}
    	
    /**
     *  waitgoods   表示待付款信息
     * 
     */
    
    function waitgoods($userId){
        $db=new db();
    	$waitgoodsSelSql =
"SELECT  *  FROM  `xgj_furnish_order_info`,`xgj_furnish_order_detail` ,`xgj_furnish_quote` 
 where  `xgj_furnish_order_info`.order_id=`xgj_furnish_order_detail`.`order_id`   
 AND  `xgj_furnish_order_detail`.`quote_id`=`xgj_furnish_quote`.quote_id  AND  `xgj_furnish_order_info`.user_id='{$userId}'  AND  `pay_status`=0
  LIMIT 3 ";
    	
    	
    	// while ($list = mysql_fetch_array($waitgoodsSelSql,MYSQL_ASSOC)){
    	// 	 $waitgoodsSelResult[] = $list;
    	// }
    	$waitgoodsSelResult=$db->getAll($waitgoodsSelSql);
    	
    	return @$waitgoodsSelResult;
    	
    	}
    	
    /**
     *     goodsdata     表示待收货信息
     */	

    function waitgoodsdata($userId){
    	
    	$db=new db();    	
    	$goodsSelSql = "SELECT  *  FROM  `xgj_furnish_order_info`,`xgj_furnish_order_detail` ,`xgj_furnish_quote` 
 where  `xgj_furnish_order_info`.order_id=`xgj_furnish_order_detail`.`order_id`   
 AND  `xgj_furnish_order_detail`.`quote_id`=`xgj_furnish_quote`.quote_id  AND  `xgj_furnish_order_info`.user_id='{$userId}'  And  `shipping_status`=0
  LIMIT 3";
    	//$goodsSelResult = mysql_fetch_array($goodsSelSql,MYSQL_ASSOC);
    			
    	$goodsSelResult=$db->getAll($goodsSelSql);	 
    			 
    	return $goodsSelResult;
    	
    	
    }
      /**
       * waitevaluatedata    表示待评价信息
       * 
       */
    
    function   waitevaluatedata($userId){
    	$db=new db(); 
    	$evaluateSelSql = "SELECT  *  FROM  `xgj_furnish_order_info`,`xgj_furnish_order_detail` ,`xgj_furnish_quote` 
 where  `xgj_furnish_order_info`.order_id=`xgj_furnish_order_detail`.`order_id`   
 AND  `xgj_furnish_order_detail`.`quote_id`=`xgj_furnish_quote`.quote_id  AND  `xgj_furnish_order_info`.user_id='{$userId}' AND  `is_comment`=0 
  LIMIT 3 ";
    	
    	$evaluateSelResult=$db->getAll($evaluateSelSql);  
    	//$evaluateSelResult = mysql_fetch_array($evaluateSelSql,MYSQL_ASSOC);
    	
    	   return $evaluateSelResult;
    	 
    	
    }
    /**
     * assetinformation   表示用户资产
     * @param int $userId   用户ID
     * @param int $rowpage  每列几条数据
     * @return array $moneyResult 查询到用户资金情况
     */
    
    function  assetinformation($page='',$num=''){
        $db=new db(); 
        //查询总记录条数的sql语句
        if ($page == "count") {
            $dataListSql="SELECT * FROM `xgj_balance` WHERE `user_id` = '{$_SESSION['userId']}'";
        }else{
            $page = ($page-1)*8;
            $dataListSql="SELECT * FROM `xgj_balance` WHERE `user_id` = '{$_SESSION['userId']}' ORDER BY `id` ASC limit {$page},{$num}";
        }

        $data = $db->getAll($dataListSql);
     //    echo '<pre>';
    	// var_dump($data);exit;
    	return  $data;

    }
    
    // function  assetinformation($userId,$rowpage){

    //     //查询总记录条数的sql语句
    //     $allrow="SELECT count(`id`) 'countDate' FROM  `xgj_balance` where `user_id` = '{$userId}'";
    //     //查询数据的sql语句
    //     $DataListSql="SELECT * FROM `xgj_balance` WHERE `user_id` = '{$userId}' ORDER BY `id` ASC";
        
    //     $moneydataResult = T_Tool::pageDataList($allrow, $DataListSql ,$rowpage);
        
    //     return  $moneydataResult;

    // }
 
    
    /**
     * orderInfoAll     订单显示
     * @param string $mouseOverChoice   订单分类
     * @param string $timeSlot  时6间段
     * @return array   $result  数据信息、分页信息
     */
    function orderInfoAll($mouseOverChoice = "all", $timeSlot = "近三个月订单"){
        $db=new db(); 
        $time=date("Y-m-d H:i:s",time()-3*30*24*3600);
    	//查询总记录条数的sql语句
    	$allnum=" SELECT count(*) ab FROM `xgj_eu_order` a left JOIN `xgj_eu_order_goods` b on a.id=b.order_id LEFT JOIN `xgj_eu_comment` c on a.id=c.comment_id where a.add_time>'{$time}' AND a.user_id = '{$_SESSION['userId']}'";
        $allrow=$db->getOne($allnum);
        //时间段处理

        if($timeSlot == "近三个月订单"){
            $timeSlot = "xgj_eu_order.add_time > '{$time}' AND";
        }elseif($timeSlot == "今年内订单"){
            $timeSlot = "`add_time` >= DATE_SUB(CURRENT_DATE(),INTERVAL 12 MONTH) AND";
        }elseif($timeSlot == "2014年订单"){
            $timeSlot = "date_format(`add_date`, '%Y')= '2014' AND";
        }elseif($timeSlot == "2013年订单"){
            $timeSlot = "date_format(`add_date`, '%Y')= '2013' AND";
        }
        
      
       
        //全部订单
        if($mouseOverChoice == "all"){	
            $orderInfoSelSql = "SELECT *,a.add_time add_time,c.add_time AS aa FROM `xgj_eu_order` a left JOIN `xgj_eu_order_goods` b on a.id=b.order_id LEFT JOIN `xgj_eu_comment` c on a.id=c.comment_id where a.add_time>'{$time}' and a.order_status<>7 AND a.user_id = '{$_SESSION['userId']}' ORDER BY a.id DESC" ;
                    
          //  echo $orderInfoSelSql;
             
             $orderResult = T_Tool::pageDataList($allrow, $orderInfoSelSql ,6);
          //   print_r($orderResult);
             return $orderResult;
            
        }
        
       

        //待付款
        if($mouseOverChoice == "waitPay"){
           
        	
            $orderInfoWaitPaySql = "SELECT *  FROM `xgj_eu_order` INNER JOIN  `xgj_eu_order_goods`  on `xgj_eu_order`.id=`xgj_eu_order_goods`.id where {$timeSlot}   `is_pay` = 0 AND `xgj_eu_order`.user_id = '{$_SESSION["userId"]}'";
          
            // while ($list = mysql_fetch_array($orderInfoWaitPaySql,MYSQL_ASSOC)){
            //     $orderInfoWaitPayResult[] = $list;
            // }
          $orderInfoWaitPayResult=$db->getRow($orderInfoWaitPaySql);
       
            return @$orderInfoWaitPayResult;
        
        }

        //待收货(已发货)
        elseif($mouseOverChoice == "waitReceiv"){
            $orderInfoWaitReceivSql = "SELECT * FROM `xgj_eu_order`  INNER JOIN  `xgj_eu_order_goods`  on `xgj_eu_order`.id=`xgj_eu_order_goods`.id  where {$timeSlot}    `order_status` > '0' AND `order_status` < '3' AND `xgj_eu_order`.`user_id` = '{$_SESSION["userId"]}' ";
            
            // while ($list = mysql_fetch_array($orderInfoWaitReceivSql,MYSQL_ASSOC)){
            //     $orderInfoWaitReceivResult[] = $list;
            // }
            $orderInfoWaitReceivResult=$db->getRow($orderInfoWaitReceivSql);
            return @$orderInfoWaitReceivResult;

        }

        //待评价
        elseif($mouseOverChoice == "waitAssess"){

            // $orderInfoWaitAssessSql = mysql_query("
            //                SELECT
            //               *
            //               FROM
            //             `xgj_eu_order` INNER JOIN  `xgj_eu_comment` 
            //                      on `xgj_eu_order`.id=`xgj_eu_comment`.comment_id 
            //               where
            //                 {$timeSlot}  `xgj_eu_comment`.user_id = '{$_SESSION["userId"]}'
            //               ORDER BY `id` DESC
            //         ");
             $orderInfoWaitAssessSql = "SELECT * FROM `xgj_eu_order`  INNER JOIN  `xgj_eu_order_goods` on `xgj_eu_order`.id=`xgj_eu_order_goods`.id  where {$timeSlot}    `order_status` = '4' AND `xgj_eu_order`.`user_id` = '{$_SESSION["userId"]}'";

            // while ($list = mysql_fetch_array($orderInfoWaitAssessSql,MYSQL_ASSOC)){
            //     $orderInfoWaitAssessResult[] = $list;
            // }
            $orderInfoWaitAssessResult=$db->getRow($orderInfoWaitAssessSql);
            return @$orderInfoWaitAssessResult;

        }

        //状态出错
        else{
            exit("状态出错!");
        }
    }


    /**
     * cancelOrderShow  已取消订单详情
     * @param int $userId   用户ID
     * @return array $cancelOrderInfoResult 查询到的已取消订单信息
     */
    function cancelOrderShow($userId){
        $db=new db(); 
        $cancelOrderInfoSel = "SELECT * FROM `xgj_eu_order_goods` inner join  `xgj_eu_order`  on  `xgj_eu_order_goods`.id=`xgj_eu_order`.id  WHERE `order_status`='8' AND `xgj_eu_order_goods`.`user_id` = '{$userId}' ";

        // while ($list = mysql_fetch_array($cancelOrderInfoSel,MYSQL_ASSOC)){
        //     $cancelOrderInfoResult[] = $list;
        // }
        $cancelOrderInfoResult=$this->m->getAll($cancelOrderInfoSel);

        return @$cancelOrderInfoResult;
    }


    /**
     * concernGoodsList 表示查询关注的商品
     * @param int $userId 用户ID
     * @param int $pageSizeParam 每页显示条数
     * @return array $concernResult     查询到的信息及分页信息
     */
    function concernGoodsList($page='0', $num='8'){
        $db=new db(); 
        if ($page == 'count') {
            $dataListSql = "SELECT `c_id`,`c_images`,`c_goodsname`,`c_goodsprice`,`goods_id`,`class_id` FROM `xgj_concern` WHERE `user_id` = '{$_SESSION['userId']}'";
        }else{
            $page = ($page-1)*8;
            $dataListSql = "SELECT `c_id`,`c_images`,`c_goodsname`,`c_goodsprice`,`goods_id`,`class_id` FROM `xgj_concern` WHERE `user_id` = '{$_SESSION['userId']}' ORDER BY `c_id` DESC limit {$page},{$num}";
        }        
        //查询数据的sql语句
        $data = $db->getAll($dataListSql);

        return $data;
          
    }
    
    /**
     * orderGoodsList  表示查询订单商品     《欧洲团购，德国母婴》
     * @param int $userId 用户ID
     * @param int $rowPage 每页显示条数
     * @return array $orderResult     查询到的信息及分页信息
     */
    /**
    function orderGoodsList($userId,$rowpage){
    	
    	//查询总记录条数的sql语句
    	$allrow="SELECT count(`buy_status`) 'countDate' FROM  `xgj_eu_order` where `user_id` = '{$userId}'";
    	//查询数据的sql语句
    	$DataListSql="SELECT * FROM `xgj_eu_order` INNER JOIN  `xgj_comment` 
                     on `xgj_eu_order`.user_id=`xgj_comment`.user_id  WHERE `xgj_eu_order`.`user_id` = '{$userId}' ORDER BY `id` DESC";
    	
    	$orderResult = T_Tool::pageDataList($allrow, $DataListSql ,$rowpage);
    	
    	
    	return  $orderResult;
    }

   **/
    /**
     * userInfoList     个人信息查询
     * @param int $userId   用户ID
     * @return array $list  查询到的用户信息
     */
    function userInfoList($userId){
        $userInfoSelSql = "SELECT `user_id`,`email`,`user_name`,`face`,`sex`,`birthday`,`identity_card`,`mobile_phone`,`alias`,`addr`,`monthly_profit`,`education_status`,`real_name` FROM `xgj_users` WHERE `user_id` = '{$userId}'";

        $list = $this->m->getRow($userInfoSelSql);

        return $list;

    }

    /**
     * * userInfoEdit     个人信息修改
     * @param string $alias             昵称
     * @param string $mobilePhone       手机号码
     * @param string $email             邮箱
     * @param string $addr              地址
     * @param string $identityCard      身份证
     * @param string $birthday          生日
     * @param string $sex               性别
     * @param string $monthlyProfit     月收入
     * @param string $educationStatus   教育程度
     * @param int $userId   用户ID
     * @return int  mysql_affected_rows 执行结果(受影响的行数)
     */
    function userInfoEdit($data,$userId){

       $userdataSql= $this->m->update("xgj_users",$data,"user_id=$userId");
       return  $userdataSql;
    }


    /**
     * addrInfoSel      查询收货地址
     * @param int $userId   用户ID
     * @return array $addrInfoSelResult 查询到的地址信息
     */
    function addrInfoSel($userId){
        $addrInfoSelSql = "SELECT * FROM `xgj_address` WHERE `user_id`='{$userId}' ORDER BY `default` DESC";

        // while ($list = mysql_fetch_array($addrInfoSelSql,MYSQL_ASSOC)){
        //     $addrInfoSelResult[] = $list;
        // }
        $addrInfoSelResult=$this->m->getAll($addrInfoSelSql);
        return @$addrInfoSelResult;

    }
        

    /**
     * addrCount   表示查询已设置的收货地址数量
     * @param int $userId   用户ID
     * @return array $addrSelResult    查询到的地址总数
     */
    function addrCount($userId){

        $addrCountSelSql = "SELECT count(`a_id`) 'addrCount' FROM `xgj_address` WHERE `user_id` = '{$userId}'";
        $addrSelResult = $this->m->getRow($addrCountSelSql);

        return $addrSelResult;

    }


    /**
     * addrDefaultSet   设置为默认收货地址
     * @param int $addrId   收货地址(主键)ID
     * @param int $userId   用户ID
     * @return int resource 执行的状态
     */
    function addrDefaultSet($addrId, $userId){

        //先将所有的状态设为未默认
        $this->m->query("UPDATE `xgj_address` SET `default` = '0' WHERE `user_id` = '{$userId}'");

        //再将选定的地址设为默认
        $default = $this->m->query("UPDATE `xgj_address` SET `default` = '1' WHERE `a_id` = '{$addrId}' AND `user_id` = '{$userId}'");

        return $default;

    }


    /**
     * doAddrInfoAdd    添加收货地址
     * @param int $userId   用户ID
     * @param string $receivingName 收货人姓名
     * @param string $mobile    收货人手机
     * @param string $email     邮箱
     * @param string $addr      收货详细地址
     * @return int resource     添加的状态
     */
    function doAddrInfoAdd($data){
        $addrInfoAddResult = $this->m->add('xgj_address',$data);
        // $addrInfoAddResult = $this->m->query("INSERT INTO `xgj_address` (`user_id`,`a_name`,`a_mobile_phone`,`a_phone`,`a_email`,`a_addr`) VALUE ('{$userId}','{$receivingName}','{$mobile}','{$phone}','{$email}','{$addr}')");
        return $addrInfoAddResult;
    }


    /**
     * addrInfoSelOne   查询出要修改的地址信息，以便于修改时显示
     * @param $addrId   地址(主键)ID
     * @return array $list  查询到的信息
     */
    function addrInfoSelOne($addrId){

        $userInfoSelSql = "SELECT * FROM `xgj_address` WHERE `a_id` = '{$addrId}'";

        //$list = mysql_fetch_array($userInfoSelSql,MYSQL_ASSOC);
        $list=$this->m->getRow($userInfoSelSql);
        return $list;

    }

    /**
     * doAddrInfoEdit          修改地址信息
     * @param int $addrId     地址(主键)ID
     * @param string $receivingName 收货人姓名
     * @param string $mobile    收货人手机
     * @param string $phone     固定电话
     * @param string $email     邮箱
     * @param string $addr      收货详细地址
     * @return int resource     修改的状态
     */
    function doAddrInfoEdit($data,$id){

        // $addrInfoEditResult = $this->m->query("UPDATE `xgj_address` SET `a_name`='{$data['receivingName']}',`a_mobile_phone`='{$data['mobile']}',`a_phone`='{$data['phone']}',`a_email`='{$data['email']}',`a_addr`='{$data['addr']}',`a_pro`='{$data['province']}',`a_city`='{$data['city']}',`a_area`='{$data['xian']}' WHERE `a_id`='{$data['addrId']}' AND user_id = '{$_SESSION['userId']}'");
        $u_id = $_SESSION['userId'];
        $addrInfoEditResult = $this->m->update('xgj_address',$data,"a_id='$id' AND user_id='$u_id'");
        return $addrInfoEditResult;

    }


    /**
     * addrInfoDel  删除收货地址
     * @param int $addrId   地址(主键)ID
     * @return int resource 修改的状态
     */
    function addrInfoDel($addrId){
        $delResult = $this->m->query("DELETE FROM `xgj_address` WHERE `a_id`='{$addrId}' AND user_id = '{$_SESSION['userId']}'");
        return $delResult;
    }
    
    /**
     *   deletegoods   删除关注的商品
     *   @param int $id  商品id
     * 
     */
    function deletegoods($id){
    	
    	$dodeleteSql=$this->m->query(" DELETE  FROM  `xgj_concern` WHERE  `user_id`='{$_SESSION['userId']}'  AND  `goods_id`='{$id}'" );
    
    }

    /**
     *   deletegoods   删除单个关注的商品
     *   @param int $id  关注的商品id
     * 
     */
    function deletegoods1($id){
        
        $dodeleteSql=$this->m->query(" DELETE  FROM  `xgj_concern` WHERE  `c_id`='{$id}'" );
    
    }

    /**
     *   deletegoods   删除单个关注的商品
     *   @param int $id  关注的商品id
     * 
     */
    function eucart($id){
        $sql = "SELECT * FROM `xgj_concern` WHERE `user_id`='{$_SESSION['userId']}' AND `c_id`='{$id}'";
        $row = $this->m->getRow($sql);
        $data['user_id'] = $row['user_id'];
        $data['goods_id'] = $row['goods_id'];
        $data['goods_num'] = 1;
        if ($row['class_id']!=1){
            $sql1 = "SELECT * FROM `xgj_eu_cart` WHERE `user_id`='{$row['user_id']}' AND `goods_id`='{$data['goods_id']}'";
            $row1 = $this->m->getRow($sql1);

            if (!empty($row1)) {
                $data1['goods_num'] = $row1['goods_num']+1;
                $id=$row1['id'];
                $return = $this->m->update('xgj_eu_cart',$data1,"id=$id");
            }else{
                $return = $this->m->add('xgj_eu_cart',$data);
            }
        }
        return $return;
    }

    /**
     * checkPassWordToModify    查询密码个用户是否匹配，用于修改密码
     * @param int $userId   用户ID
     * @param string $passWord  密码
     * @return array $passWordSelResult 查询到的结果
     */
    function checkPassWordToModify($userId, $passWord){
        $passWordSel = "SELECT `user_id` FROM `xgj_users` WHERE `user_id`='{$userId}' AND `password`='{$passWord}'";
        //$passWordSelResult = mysql_fetch_array(mysql_query($passWordSel), MYSQL_ASSOC);
        $passWordSelResult =$this->m->getRow($passWordSel);
        return $passWordSelResult;
    }


    /**
     * PassWordToModify     执行修改密码
     * @param int $userId   用户ID
     * @param string $oldPassWord   原始密码
     * @param string $newPassWord   新密码
     * @return resource 修改的结果
     */
    function PassWordToModify($userId, $oldPassWord, $newPassWord){
		$data['password']=$newPassWord;
        $passWordModify = $this->m->update("xgj_users", $data,"user_id='".$userId."' AND password='".$oldPassWord."'");
        return $passWordModify;
    }
     

     public function selecttel($tel){
        $sql = "SELECT * FROM `xgj_users` WHERE `mobile_phone`='{$tel}'";
        $row = $this->m->getRow($sql);
        return $row;
     }
    /**
     *   findpassworddata   执行忘记密码
     * 
     */
    
    public function findpassworddata($iPhonenum,$password){
    	
    	//去除空格
    	// $passWordTrim = T_Tool::triMall($password);
        $passWordTrim = trim($password);
    	//MD5加密
    	$passWordMd5 = md5($passWordTrim.MD5_PASSWORD);

        if (!empty($passWordMd5)) {
            $data = array('password' =>$passWordMd5);
            $dofindpassword=$this->m->update('xgj_users',$data,"mobile_phone=$iPhonenum");
            return $dofindpassword;
        }else{
            return  '';
        }
    	

    	// if(mysql_affected_rows())
    	// { 
    	// 	echo "<script> window.location.href=\"user.php?login\"</script>";
    	// }

    	// else { echo  "<script type=text/javascript>alert('手机号码不存在，请重新操作 !!!');history.back();</script>";;} 
    	
    }

    /**
     * couponStatusCount    查询优惠券各状态的数量
     * @param string $where   查询条件
     * @return array $couponStatusCountResult   查询到的个状态优惠券数量
     */
    function couponStatusCount($where){
        $couponStatusCountSql = "SELECT `coupon` FROM `xgj_users` $where";
        $couponStatusCountResult = $this->m->getRow($couponStatusCountSql);

        return $couponStatusCountResult;
    }

    /**
     * couponInfo    查询优惠券流水表用户的使用金额
     * @param string $where   查询条件
     * @return array $couponStatusCountResult   查询到的个状态优惠券数量
     */
    function couponInfo($where){
        $couponInfoSql = "SELECT * FROM `xgj_coupon_info` $where";
        $couponInfoResult = $this->m->getAll($couponInfoSql);
        $price=0;
        foreach($couponInfoResult AS $k=>$v){
            $price+=$v['use_money'];
        }

        return $price;
    }
    
    
    /**
     * couponInfoList   查询优惠券信息
     * @param int $userId
     * @return array $couponSelList 查询到的优惠券信息
     */
    function couponInfoList($page=null,$num=null){
        if ($page == null) {
            $limit = '';
        }else{
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }

    	$couponSelSql = "SELECT * FROM `xgj_coupon_info` WHERE `user_id` = '{$_SESSION['userId']}' order by use_time desc $limit";
        $couponSelList=$this->m->getAll($couponSelSql);

        if ($limit == '') {
            return $couponSelList;exit;
        }

        $euList = '';
        $furnishList = '';
        foreach ($couponSelList as $k => $v) {
            if ($v['class_id'] == 1) $euList .= 'i.`class_id`= 1 and e.id = '.$v['order_id'].' or ';
            else if ($v['class_id'] == 2) $furnishList .= 'i.`class_id`= 2 and o.order_id = '.$v['order_id'].' or ';
        }

        if (!empty($euList)) {
            $euList      = rtrim($euList,'or ');
            $euSql = "SELECT i.*,e.sn sn FROM `xgj_coupon_info` i join `xgj_eu_order` e on e.`id`= i.`order_id` WHERE $euList";
            $euData=$this->m->getAll($euSql);

            foreach ($euData as $k => $v) {
                $euRowSql = "SELECT * FROM `xgj_eu_order_goods` WHERE order_id = '{$v['order_id']}' limit 1";
                $euRow=$this->m->getRow($euRowSql);
                $euData[$k]['goods_name'] = $euRow['goods_title'];
            }
        }

        if (!empty($furnishList)) {
            $furnishList = rtrim($furnishList,'or ');
            $furnishSql = "SELECT i.*,o.order_code sn FROM `xgj_coupon_info` i join `xgj_furnish_order_info` o on o.`order_id`= i.`order_id` WHERE $furnishList";
            $furnishData=$this->m->getAll($furnishSql);

            foreach ($furnishData as $k => $v) {
                $furnishRowSql = "SELECT * FROM `xgj_furnish_order_detail` WHERE order_id = '{$v['order_id']}' limit 1";
                $furnishRow=$this->m->getRow($furnishRowSql);
                $furnishData[$k]['goods_name'] = $furnishRow['quote_name'];
            }
        }
        if (!empty($euData) && !empty($furnishData)) $returnList = array_merge($euData,$furnishData);
        if (!empty($euData) && empty($furnishData))  $returnList = $euData;
        if (empty($euData) && !empty($furnishData))  $returnList = $furnishData;

    	return @$returnList;
    }

    //查询优惠券信息分页
    public function couponInfoList_P($page,$num){
        $page = ($page-1)*$num;
        $limit = "limit $page,$num";
        $couponSelSql = "SELECT * FROM `xgj_coupon_info` i join `xgj_furnish_order_info` o on o.`order_id`= i.`order_id` WHERE i.`user_id` = '{$_SESSION['userId']}' limit {$page},{$num}";
        $data=$this->m->getAll($couponSelSql);
        foreach ($data as $k => &$v) {
            $v['use_time']=date("Y-m-d H:i:s",$v['use_time']);
        }
        return $data;
    }
    

    /**
     * waitEvaluateShow 待评价信息显示
     * @param int $userId   用户ID
     * @return array $waitEvaluateInfoSelResult 查询到的评价信息
     */
    function waitEvaluateShow($userId){
        $waitEvaluateInfoSelSql = "SELECT `e_id`,`class_id`,`goods_id`,`goods_name`,`goods_img`,`buy_time`,`e_content`,`evaluate_time` FROM `xgj_evaluate` WHERE `user_id`='{$userId}' AND `e_status`='0'";

        $waitEvaluateInfoSelResult=$this->m->getRow($waitEvaluateInfoSelSql);

        return @$waitEvaluateInfoSelResult;
    }


    /**
     * evaluatedShow 已评价信息显示
     * @param int $userId   用户ID
     * @return array $evaluatedInfoSelResult 查询到的评价信息
     */
    function evaluatedShow($userId){
        $evaluatedInfoSelSql = "SELECT `e_id`,`class_id`,`goods_id`,`goods_name`,`goods_img`,`buy_time`,`e_content`,`evaluate_time` FROM `xgj_evaluate` WHERE `user_id`='{$userId}' AND `e_status`='1' AND `evaluate_time` >= DATE_SUB(CURRENT_DATE() , INTERVAL 3 MONTH)";
        $evaluatedInfoSelResult=$this->m->getRow($evaluatedInfoSelSql); 

        return @$evaluatedInfoSelResult;
    }
    
    /**
     *   evaluatedEurope  已评价信息显示    《 欧洲团代购， 德国母婴》
     *   @param int $userId   用户ID
     * 
     */
    function evaluatedEurope($userId){
    	$evaluatedInfoEuropeResult=array();
    	$evaluatedInfoEuropeSql = "SELECT * FROM  `xgj_eu_comment` , `xgj_eu_order_goods` , xgj_eu_order
          WHERE  `xgj_eu_comment`.goods_id=`xgj_eu_order_goods`.goods_id  AND `xgj_eu_order`.user_id='{$userId}'
           AND  `status`=1";
    	
    	 $evaluatedInfoEuropeResult=$this->m->getRow($evaluatedInfoEuropeSql); 
    	
    	return @$evaluatedInfoEuropeResult;
    	
    }
    
    /**
     *  evaluatedhome 已评价信息显示      《健康舒适家》
     *  @param int $userId   用户ID
     */
    function evaluatedhome($userId){
    	$evaluatedInfohomeResult=array();
    	$evaluatedInfohomeSql = "SELECT * FROM  `xgj_furnish_goods` , xgj_furnish_comment , xgj_furnish_order_info
    where   `xgj_furnish_goods`.goods_id=xgj_furnish_comment.goods_id    and    xgj_furnish_comment.user_id='{$userId}'
     and  xgj_furnish_order_info.user_id='{$userId}'  and `status`=1 ";
    	 
    	$evaluatedInfohomeResult=$this->m->getRow($evaluatedInfohomeSql);
    	
    	return @$evaluatedInfohomeResult;    	
    }


    /**
     * waitEvaluateCount    待评价的商品数量
     * @param int $userId   用户ID
     * @return array $waitEvaluateCountResult   查询到的结果
     */
    function waitEvaluateCount($userId){
        $waitEvaluateCount = "SELECT COUNT(`e_id`) 'dataCount' FROM `xgj_evaluate` WHERE `user_id`='{$userId}' AND `e_status`=0";
        $waitEvaluateCountResult=$this->m->getRow($waitEvaluateCount);
        return @$waitEvaluateCountResult;
    }

    /**
     * evaluatedCount    已评价的三个月内商品数量
     * @param int $userId   用户ID
     * @return array $waitEvaluateCountResult   查询到的结果
     */
    /**
    function evaluatedCount($userId){
        $evaluatedCount = "SELECT COUNT(`e_id`) 'dataCount' FROM `xgj_evaluate` WHERE `user_id`='{$userId}' AND `e_status`='1' AND `evaluate_time` >= DATE_SUB(CURRENT_DATE() , INTERVAL 3 MONTH)";
        $evaluatedCountResult = mysql_fetch_array(mysql_query($evaluatedCount), MYSQL_ASSOC);

        return @$evaluatedCountResult;
    }
    
    **/
    
    /**
     *  evaluatedCounthome   已评价的三个月内商品数量    《健康舒适家》
     *   @param int $userId   用户ID
     *   @return array $evaluatedCounthomeResult   查询到的结果
     */
    function evaluatedCounthome($userId){
    	
    	$evaluatedCounthome = "SELECT COUNT(`comment_id`) 'dataCount' from  `xgj_furnish_goods` , xgj_furnish_comment , xgj_furnish_order_info
    where   `xgj_furnish_goods`.goods_id=xgj_furnish_comment.goods_id    and    xgj_furnish_comment.user_id='{$userId}'
     and  xgj_furnish_order_info.user_id='{$userId}'   and `status`=1";
    	$evaluatedCounthomeResult=$this->m->getRow($evaluatedCounthome);
    	return @$evaluatedCounthomeResult;
    	
    }
    
    /**
     *  evaluatedCountEurope   已评价的三个月内商品数量    《 欧洲团代购， 德国母婴》
     *   @param int $userId   用户ID
     */
    function evaluatedCountEurope($userId){
    	
    	$evaluatedCountEurope = "SELECT COUNT(`comment_id`) 'dataCount' FROM `xgj_eu_comment`
    	  WHERE `user_id`='{$userId}' AND `status` ='1' ";
        $evaluatedCountEuropeResult=$this->m->getRow($evaluatedCountEurope);
    	return @$evaluatedCountEuropeResult;
    	
     }
     

    /**
     * doEvaluate   执行评论(修改)操作        健康舒适家评论
     * @param int $eId  评论(主键，自增长)ID
     * @param string $content   评论内容
     * @return resource 执行结果
     */
    function doEvaluate($eId, $content, $detail_id,$order_id){
        $uid = $_SESSION['userId'];
        $uname = $_SESSION['userName'];
        $mysql = new db();
        //添加评论
        $add = array('content'=>$content,'status'=>1,'class_id'=>1,'goods_id'=>$detail_id,'quote_id'=>$eId,'user_name'=>$uname,'add_time'=>time(),'user_id'=>$uid,'order_id'=>$order_id);
        $doEvaluateResult = $mysql->add('xgj_furnish_comment',$add);

        if (!empty($doEvaluateResult)) {
            $save = array('status'=>1);
            $where = "detail_id=$detail_id";
            $updata = $mysql->update('xgj_furnish_order_detail',$save,$where);

            //查询订单内商品是否全部评价完毕
            $sql = "select status from xgj_furnish_order_detail where order_id=$order_id";
            $select = $mysql->getall($sql);
            foreach ($select as $key => $value) {
                if ($value["status"]==0) {
                    $aaa = 1;  
                }
            }
            //如果全部评价完毕更改订单状态
            if (empty($aaa)) {
                $save1 = array('is_comment'=>1);
                $where1 = "order_id=$order_id";
                $updata = $mysql->update('xgj_furnish_order_info',$save1,$where1);
            }

        }else{
            return '';
        }

        return @$updata;

        //修改评论
        // $doEvaluateResult = mysql_query("UPDATE `xgj_furnish_comment`  SET `status` = '1', `content` = '{$content}'
        //     WHERE `quote_id` = '{$eId}'");
        
    }
    
    
    /**
     * europe   执行评论(修改)操作       欧洲团代购 ，德国母婴 评论
     * @param int $eId  评论(主键，自增长)ID
     * @param string $content   评论内容
     * @return resource 执行结果
     */
    
    function europe($eId, $content, $id, $order_id){
        $uid = $_SESSION['userId'];
        $uname = $_SESSION['userName'];
        $mysql = new db();

        //添加评论
        $add = array('content'=>$content,'status'=>1,'goods_id'=>$eId,'user_name'=>$uname,'add_time'=>time(),'user_id'=>$uid,'order_goods_id'=>$order_id);
        $doEvaluateResult = $mysql->add('xgj_eu_comment',$add);

        if (!empty($doEvaluateResult)) {
            $save = array('status'=>1);
            $where = "id=$id";
            $updata = $mysql->update('xgj_eu_order_goods',$save,$where);

            //查询订单内商品是否全部评价完毕
            $sql = "select status from xgj_eu_order_goods where order_id=$order_id";
            $select = $mysql->getall($sql);
            foreach ($select as $key => $value) {
                if ($value["status"]==0) {
                    $aaa = 1;  
                }
            }
            //如果全部评价完毕更改订单状态
            if (empty($aaa)) {
                $save1 = array('order_status'=>5);
                $where1 = "id=$order_id";
                $updata = $mysql->update('xgj_eu_order',$save1,$where1);
            }
        }else{
            return '';
        }

        return @$updata;

    
    	//修改评论
    	// $doEvaluateResult = mysql_query("UPDATE `xgj_eu_comment` SET `status` = '1', `content` = '{$content}'
    	// WHERE `goods_id` = '{$eId}'");
    	// return @$EvaluateResult;
    	
    }


     /** 
     * returnShow   显示所有可以退换货的订单
     * @param int $userId   用户ID
     * @param int $addDate  查询的时间区间(几天内)
     * @param int $pageSizeParam    每页显示数据条数
     * @return array $result    查询到的信息及分页信息
     */
    function returnShow($page='', $num=''){
        $db=new db(); 
        if ($page == 'count') {
            $dataListSql = "SELECT * FROM `xgj_eu_order` WHERE `user_id` = '{$_SESSION['userId']}' and  `is_pay`=1";
        }else{
            $page = ($page-1)*$num;
            $dataListSql = "SELECT xgj_eu_order.*,xgj_eu_order_goods.id gid,r_status,goods_title,goods_price FROM `xgj_eu_order` inner join `xgj_eu_order_goods` on   `xgj_eu_order`.id=`xgj_eu_order_goods`.order_id
                            where  `xgj_eu_order`.user_id='{$_SESSION['userId']}'   and `is_pay` = '1' and `xgj_eu_order_goods`.`r_status` <> '5' order by pay_time desc limit {$page},{$num}";
        }        
        //查询数据的sql语句
        $data = $db->getAll($dataListSql);

        return $data;
        
    }
    // function returnShow($userId, $addDate, $pageSizeParam){

    //     //查询总记录
    //     $countDateSql="SELECT COUNT(`order_id`) 'countDate' FROM `xgj_eu_order` WHERE `user_id` = '{$userId}' and  `is_pay`=1 ";

    //     $returnGoodsSql = " SELECT * FROM `xgj_eu_order` inner join `xgj_eu_order_goods`  on   `xgj_eu_order`.id=`xgj_eu_order_goods`.id
    //                         where  `xgj_eu_order`.user_id='{$userId}'   and `is_pay` = '1' ";

    //     $returnGoodsList = T_Tool::pageDataList($countDateSql, $returnGoodsSql, $pageSizeParam);

    //     return @$returnGoodsList;
    // }


    /**
     * returnedList   显示所有已经退换货的订单
     * @param int $userId   用户ID
     * @return array $result    查询到的信息及分页信息
     */
    function returnedList($userId){
        $returnedGoodsSql ="SELECT * FROM `xgj_eu_order` inner join `xgj_eu_order_goods`  on   `xgj_eu_order`.id=`xgj_eu_order_goods`.order_id
                                 where  `xgj_eu_order`.user_id='{$userId}'   and
                                  `xgj_eu_order_goods`.`r_status` = '5' ";

       $returnedGoodsSelResult=$this->m->getAll($returnedGoodsSql);
     
        return @$returnedGoodsSelResult;
    }
     
    /**
     *  orderinformation  表示查询用户 返修退换货  商品信息
     */
    
    function  orderinformation(){
    
    	   $orderSql="SELECT *  FROM  `xgj_order_info` WHERE `user_id`='{$_SESSION["userId"]}'";
    	  $orderResult=$this->m->getAll($orderSql);
    	   
    	   return  $orderResult;
    	
    	
     }
    
    

    /**
     * returnMoneyList   显示所有退货的信息
     * @param int $userId   用户ID
     * @return array $result    查询到的信息及分页信息
     */
    function returnMoneyList($userId){
        $returnMoneySql = "SELECT * FROM `xgj_furnish_order_refund`  WHERE `user_id` = '{$userId}' AND `refund_status` = '1' ";

       $returnMoneySelResult=$this->m->getAll($returnMoneySql);

        return @$returnMoneySelResult;
    }


    /**
     * repairList   显示所有换货的信息
     * @param int $userId   用户ID
     * @return array $result    查询到的信息及分页信息
     */
    function repairList($userId){
    	
        $repairSql = "SELECT * FROM `xgj_furnish_order_refund` WHERE `user_id` = '{$userId}' AND `refund_status`=1 ";

       $repairResult=$this->m->getRow($repairSql);

        return @$repairResult;
    }


    /**
     * applyInfo    查询要修改退换货的订单信息
     * @param int $userId   用户ID
     * @param int $classId  商品classId(分类ID)
     * @param int $goodsId  商品ID
     * @return array $applyInfo 查询到的信息
     */
    function applyInfo($gid,$order_id){
        $applyInfoSql = " SELECT g.*,o.sn sn FROM xgj_eu_order_goods g,xgj_eu_order o where g.`user_id`='{$_SESSION['userId']}' and g.`id`='{$gid}' and g.`order_id`='{$order_id}' and g.order_id=o.id";
        $applyInfo=$this->m->getRow($applyInfoSql);
        return @$applyInfo;
    }
    // function applyInfo($userId, $classId, $goodsId){
    //     $applyInfoSql = " SELECT * FROM `xgj_eu_order_goods` WHERE `user_id`='{$userId}' AND `class_id`='{$classId}' AND
    //                       `goods_id`='{$goodsId}' AND `id` IN ( SELECT `id` FROM `xgj_eu_order_goods` )  ";
    //     //$applyInfo = mysql_fetch_array(mysql_query($applyInfoSql), MYSQL_ASSOC);
    //     $applyInfo=$this->m->getRow($applyInfoSql);
    //     return @$applyInfo;
    // }


    /**
     * applyInfo    查询要修改退换货的订单信息
     *
     */
    function doApply($data){
        $doApplyResult = $this->m->add('xgj_order_return',$data);
        return @$doApplyResult;

        // $doApplyResult = $this->m->query("UPDATE`xgj_order_info`SET  `return_type` ='{$returnType}', `consignee` ='{$username}', `mobile`='{$mobile}', `addess`='{$address}', `return_remark`='{$returnRemark}' WHERE  `user_id`='{$userid}'  AND  `order_sn`='{$order_sn} ");
         
    }

    function doApplyupdata($where){
        $data['r_status']=1;
        $doApplyResult = $this->m->update('xgj_eu_order_goods',$data,$where);
        return @$doApplyResult;
    }


	function show_house_info($userid){
		$db = new db();
        $sql = "select * from xgj_users_house where user_id='".$userid."'";
        $arr = $db->getAll($sql);
        return $arr;
        
	}
    
	function add_house_info($data){
		
		$fields = implode('`,`',array_keys($data));
		     
		$values = implode('\',\'', array_values($data));
		  //print "value=".$values;
	     	// echo 'insert into `xgj_users_house` (`'. $fields. '`) values (\''. $values .'\')';
		 return mysql_query('insert into `xgj_users_house` (`'. $fields 
			. '`) values (\''. $values .'\')');
	}
    function  select_data($userid){  
        
        $mysql = new db();

        $sql="SELECT * FROM xgj_users_house WHERE user_id = $userid LIMIT 1";

        $return = $mysql->getAll($sql);

        return $return;

    }

    function  updata_data($userid,$province,$city,$town,$detail,$housetype,$str,$area,$str2,$attic,$basement,$attic_area,$basement_area,$area_1){  
        
        $mysql = new db();

        $data = array('province'=>$province,'city'=>$city,'district'=>$town,'address'=>$detail,'house_type'=>$housetype,'house_layout'=>$str,'total_area'=>$area,'type_area'=>$str2,'attic'=>$attic,'basement'=>$basement,'attic_area'=>$attic_area,'basement_area'=>$basement_area,'area'=>$area_1);

        $where = "user_id = $userid";

        $return = $mysql->update('xgj_users_house',$data,$where);

        return $return;

    }
	
    function addConcern($data){

         $mysql = new db();

         $data = $mysql->add('xgj_concern',$data);

         return $data;

    }

    function selectConcern($id){

         $mysql = new db();

         $sql = "select * from xgj_concern where user_id = $id";

         $data = $mysql->getAll($sql);

         return $data;

    }

    public function orderall($order_id,$order_sn){
        $user_id = $_SESSION['userId'];
        $mysql = new db();
        $sql = "select * from xgj_eu_order where sn = '$order_sn' and id = $order_id and user_id = $user_id";
        $data = $mysql->getAll($sql);
        return $data;
    }

    public function ordersave($data,$order_id,$order_sn){
        $user_id = $_SESSION['userId'];
        $mysql = new db();
        $where = "sn = '$order_sn' and id = $order_id and user_id = $user_id";
        $return = $mysql->update('xgj_eu_order',$data,$where);
        return $return;
    }

    //全部订单
    public function orderInfoAllList($page=0,$num=5,$status=''){
        if (!empty($page)) {
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }else{
            $limit = '';
        }
        if(!empty($status) || $status=='0'){
            $str=" and order_status = $status ";
        }else{
            $str=" and order_status!=7 ";
        }
        //var_dump($status,$str);exit;
        $uid = $_SESSION['userId'];
        $mysql = new db();
        $sql = "select * from xgj_eu_order where user_id = $uid $str ORDER BY add_time DESC $limit";

        $data = $mysql->getAll($sql);

        foreach ($data as $k=> $v) {
            $data[$k]['integral']=$mysql->getOne("select integral from xgj_user_integral where `order`={$v['id']} and status='1' and user_id=$uid and class='2'");
            $data[$k]['coupon']=$mysql->getOne("select use_money from xgj_coupon_info where order_id = $v[id] ");
            $data[$k]['detail']=$mysql->getAll("select *,o.id id,s.id s_id from xgj_eu_order_goods o join xgj_eu_goods_new n on o.goods_id=n.id join xgj_eu_split_order s on s.detail_id=o.id  where o.order_id = $v[id] ");
        }        

        return $data;
    }


    //订单总数
    function count_order($status=''){
        $db=new db();
        if(!empty($status)){
            $str=" and order_status = $status ";
        }else{
            $str=" and order_status!=7 ";
        }
        $uid = $_SESSION['userId'];
        $sql = "select count(id) from xgj_eu_order where user_id = $uid $str ORDER BY add_time DESC ";
        $result=$db->getOne($sql);
        return $result;
    }

    //全部订单
    public function ovorderInfoAllList($page=0,$num=5,$status=''){
        if (!empty($page)) {
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }else{
            $limit = '';
        }
        if(!empty($status) || $status=='0'){
            $str=" and order_status = '{$status}' ";
        }else{
            $str=" and order_status!=7 ";
        }
        // var_dump($status,$str);exit;
        $uid = $_SESSION['userId'];
        $mysql = new db();
        $sql = "select * from xgj_ov_order where user_id = $uid $str ORDER BY add_time DESC $limit";

        $data = $mysql->getAll($sql);

        foreach ($data as $k=> $v) {
            $data[$k]['integral']=$mysql->getOne("select integral from xgj_user_integral where `order`={$v['id']} and status='1' and user_id=$uid and class='2'");
            $data[$k]['coupon']=$mysql->getOne("select use_money from xgj_coupon_info where order_id = $v[id] ");
            $data[$k]['detail']=$mysql->getAll("select *,o.id id,s.id s_id from xgj_ov_order_goods o join xgj_ov_goods n on o.goods_id=n.id join xgj_ov_split_order s on s.detail_id=o.id  where o.order_id = $v[id] ");
        }        
 
        return $data;
    }
    //订单总数
    function count_ovorder($status=''){
        $db=new db();
        if(!empty($status)){
            $str=" and order_status = $status ";
        }else{
            $str=" and order_status!=7 ";
        }
        $uid = $_SESSION['userId'];
        $sql = "select count(id) from xgj_ov_order where user_id = $uid $str ORDER BY add_time DESC ";
        $result=$db->getOne($sql);
        return $result;
    }

    //待付款订单信息
    public function orderInfoWaitPayList($page=0,$num=5){
        if (!empty($page)) {
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }else{
            $limit = '';
        }
        $uid = $_SESSION['userId'];
        $mysql = new db();
        $sql = "select * from xgj_eu_order where user_id = $uid and order_status=0 and TO_DAYS(NOW())-TO_DAYS(add_time) <= 90 ORDER BY add_time DESC $limit";

        $data = $mysql->getAll($sql);

        return $data;
    }

    //待收货订单信息
    public function orderInfoWaitReceivList($page=0,$num=5){
        if (!empty($page)) {
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }else{
            $limit = '';
        }
        $uid = $_SESSION['userId'];
        $mysql = new db();

        $sql = "select * from xgj_eu_order where user_id = $uid  and order_status > 0 and order_status < 3 and TO_DAYS(NOW())-TO_DAYS(add_time) <= 90 ORDER BY add_time DESC $limit";
        $data = $mysql->getAll($sql);

        return $data;
    }

    //待评价订单信息
    public function orderInfoWaitAssessList($page=0,$num=5){
        if (!empty($page)) {
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }else{
            $limit = '';
        }
        $uid = $_SESSION['userId'];
        $mysql = new db();

        $sql = "select * from xgj_eu_order where user_id = $uid  and order_status = 4 and TO_DAYS(NOW())-TO_DAYS(add_time) <= 90 ORDER BY add_time DESC $limit";
        $data = $mysql->getAll($sql);

        return $data;
    }

    //全部评价(欧团)
    public function select_evaluateShowAll($page=null,$num=null){
        if (!empty($page)) {
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }else{
            $limit = '';
        }
        $uid = $_SESSION['userId'];
        $mysql = new db();
        $sql = "SELECT * FROM xgj_eu_order o JOIN xgj_eu_order_goods g ON o.id = g.order_id WHERE o.user_id = $uid AND o.order_status > 3 AND o.order_status < 6 ORDER BY o.pay_time DESC $limit";
        $data = $mysql->getAll($sql);
        return $data;
    }

    //待商品评价(欧团)
    public function select_evaluateShow($page=null,$num=null){
        if (!empty($page)) {
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }else{
            $limit = '';
        }
        $uid = $_SESSION['userId'];
        $mysql = new db();
        $sql = "SELECT * FROM xgj_eu_order o JOIN xgj_eu_order_goods g ON o.id = g.order_id WHERE o.user_id = $uid AND o.order_status = 4 AND g.status = '0' ORDER BY o.pay_time DESC $limit";
        $data = $mysql->getAll($sql);
        return $data;
    }

    public function euGoodsRow($id){
        $mysql = new db();
        // echo $id;exit;
        $sql = "select g.*,o.sn sn,o.pay_time pay_time from xgj_eu_order_goods g join xgj_eu_order o on g.order_id = o.id where g.id = $id and g.user_id = '{$_SESSION['userId']}'";
        $data = $mysql->getRow($sql);
        return $data;
    }

    public function addEuData($data){
        $re = $this->m->add('xgj_eu_comment',$data);
        return $re;
    }

    public function updataEuStatus($id){
        $where = "id = $id and user_id = '{$_SESSION['userId']}'";
        $data = array(
            'status'    => 1,
            'time'      => time()
            );
        $re = $this->m->update('xgj_eu_order_goods',$data,$where);
        $data_ = array(
            'order_status'    => 5,
            );
         $re = $this->m->update('xgj_eu_split_order',$data_,"detail_id = $id and user_id = '{$_SESSION['userId']}'");
        //查询订单ID
        $sql = "select order_id from xgj_eu_order_goods where $where";
        $orderId = $this->m->getRow($sql);

        //查询是否全部评价完毕
        $sql = "select * from xgj_eu_order_goods where order_id='{$orderId['order_id']}' and status='0' and user_id = '{$_SESSION['userId']}'";
        $orderList = $this->m->getAll($sql);

        if (empty($orderList)) {
            //全部完毕后更改订单状态
            $update = array(
                'order_status'  => '5'
            );

            $re = $this->m->update('xgj_eu_order',$update,"id='{$orderId['order_id']}' and user_id = '{$_SESSION['userId']}'");

            if (!empty($re)) {

                //查询订单应付价格
                $sql = "select deal_price from xgj_eu_order where id='{$orderId['order_id']}' and user_id = '{$_SESSION['userId']}'";
                $orderPrice = $this->m->getRow($sql);

                //添加积分
                $addData = array(
                    'user_id'   =>$_SESSION['userId'],
                    'user_name' =>$_SESSION['userName'],
                    '`order`'   =>$orderId['order_id'],
                    'integral'  =>floor($orderPrice['deal_price']*0.05),
                    'time'      =>time(),
                    '`status`'  =>'2',
                    'class'     =>'3',
                );

                $re = $this->m->add('xgj_user_integral',$addData);

                if (floor($orderPrice['deal_price']*0.05)=='0') {
                    return $re;exit;
                }

                if ($re) {
                    $sql = "select integral from xgj_users where user_id = '{$_SESSION['userId']}'";
                    $userIntegral = $this->m->getRow($sql);

                    $integral['integral'] = $userIntegral['integral']+floor($orderPrice['deal_price']*0.05);
                    $re = $this->m->update('xgj_users',$integral,"user_id = '{$_SESSION['userId']}'");
                }
            }
        }
        return $re;
    }

    //最近三个月已评价(欧团)
    public function select_evaluateShow1($page=null,$num=null){
        if (!empty($page)) {
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }else{
            $limit = '';
        }
        $uid = $_SESSION['userId'];
        $mysql = new db();
        $sql = "SELECT g.*,o.pay_time FROM xgj_eu_order_goods g join xgj_eu_order o ON g.order_id = o.id WHERE g.status = '1' and g.user_id = $uid ORDER BY o.pay_time DESC $limit";
        $data = $mysql->getAll($sql);
        return $data;
    }

    //待商品评价(家居)
    public function select_evaluateShow2($page=null,$num=null){
        if ($page == null) {
            $limit = '';
        }else{
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }
        $uid = $_SESSION['userId'];
        $mysql = new db();
        $sql = "SELECT *,i.order_id FROM xgj_furnish_order_info i JOIN xgj_furnish_order_detail d ON i.order_id = d.order_id JOIN xgj_furnish_quote q ON d.quote_id = q.quote_id WHERE i.user_id = $uid AND i.schedule_status = 7 AND d.status = '0' ORDER BY add_order_time DESC $limit";
        $data = $mysql->getAll($sql);
        return $data;
    }

    //全部评价(家居)
    public function evaluateShowAll($page=null,$num=null){
        if ($page == null) {
            $limit = '';
        }else{
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }
        $uid = $_SESSION['userId'];
        $mysql = new db();
        $sql = "SELECT *,i.order_id FROM xgj_furnish_order_info i JOIN xgj_furnish_order_detail d ON i.order_id = d.order_id JOIN xgj_furnish_quote q ON d.quote_id = q.quote_id WHERE i.user_id = $uid AND i.schedule_status = 7 ORDER BY add_order_time DESC $limit";
        $data = $mysql->getAll($sql);
        return $data;
    }

    public function FuRow($id){
        $uid = $_SESSION['userId'];
        $mysql = new db();
        $sql = "select d.*,i.*,q.img from xgj_furnish_order_detail d join xgj_furnish_order_info i on d.order_id = i.order_id join xgj_furnish_quote q on d.quote_id = q.quote_id where d.detail_id = $id and d.status = '0' and user_id = $uid";
        $orderInfo['info'] = $mysql->getRow($sql);
        if (empty($orderInfo['info'])) {
            echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
        }
        //查询经销商调整后的房屋信息
        $sql = "select * from xgj_dealer_adjust_info where order_code = '{$orderInfo['info']['order_code']}' and user_id = $uid";
        $orderInfo['house'] = $mysql->getRow($sql);
        //如果调整后的房屋信息没有就查询用户自己填写的房屋信息
        if (empty($orderInfo['house'])) {
            $sql = "select * from xgj_users_house where user_id = $uid";
            $orderInfo['house'] = $mysql->getRow($sql);
        }
        return $orderInfo;
    }

    //最近三个月已评价(家居)
    public function select_evaluateShow3($page=null,$num=null){
        if ($page == null) {
            $limit = '';
        }else{
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }
        $uid = $_SESSION['userId'];

        $mysql = new db();
        $sql = "select * from xgj_furnish_order_detail d join xgj_furnish_order_info i on d.order_id = i.order_id join xgj_furnish_quote q on d.quote_id = q.quote_id  where d.status = '1' and user_id = $uid order by pay_time desc $limit";
        $data = $mysql->getAll($sql);
        return $data;
    }


    public function delOrder($id){
        $userId = $_SESSION['userId'];
        $sql = "DELETE FROM `xgj_eu_order` WHERE `id`='{$id}' AND user_id = '{$userId}' AND order_status = '0'";
        $re = $this->m->query($sql,'exec');
        return $re;
    }

    #=============================用户注册（优惠券验证）[L]====================================#
    public function checkCoupon($coupon){
        $rs=$this->m->getRow("select coupon_number,status from xgj_coupon where coupon_number='".$coupon."' limit 1");
        //var_dump($rs);exit;
        if(empty($rs['coupon_number'])){
            echo 1;
        }else if(!empty($rs['status']) && $rs['status']==1){
            echo 2;
        }else{
            echo 3;
        }
    }
    public function getafterService($user_id,$page='',$num='',$type=''){
        if(!empty($page)){
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }else{
            $limit='';
        }
        if(!empty($type)){
            $where="and type='$type'";
        }else{
            $where='';
        }
        $rs=$this->m->getAll("select * from xgj_user_problem where user_id=$user_id $where $limit");
        foreach ($rs as $k=>$v){
            $rs[$k]['time']=date('Y-m-d H:i',$v['time']);
            $rs[$k]['s_time']=date('Y-m-d H:i',$v['s_time']);
            $arr=explode('-',$v['quote_id']);
            foreach($arr as $ke=>$va){
                $rs[$k]['quote_name']=$this->m->getOne("select quote_name from xgj_furnish_quote where quote_id=$va");
            }
        }
        return $rs;
    }
    #==========================================================================================#

    //执行兑换优惠券操作
    public function activationCoupon($code,$pass,$uid){
        $mysql = new db();
        $sql = "select * from xgj_coupon where coupon_number = '{$code}' ";
        $couponRow = $mysql->getRow($sql);
        if (empty($couponRow)) {
			echo '无此优惠券！';
			exit;
		}else if($couponRow['status']=='1'){
			echo '该优惠券已兑换！';
			exit;
		}else if($couponRow['coupon_password']!=$pass) {
			echo '优惠券密码不对';
			exit;
		}
		
         $updateDate = array(
				'user_id'       => $uid,
				'status'        => '1',
				'activate_time' => time(),
				'is_status'     => '0'
          );

         $return = $mysql->update('xgj_coupon',$updateDate,"id = '{$couponRow['id']}'");

         if (!empty($return)) {
                $coupon = $mysql->getRow("select user_id, coupon ,coupon_number from xgj_users where user_id = '{$uid}'");
                $total = $coupon['coupon'] + $couponRow['discount_amount'];
				if(empty($coupon['coupon_number'])){//优惠券存入用户表 用于绑定地推人员
					 $res = $mysql->getRow("select user_id from xgj_users where coupon_number = '{$couponRow['coupon_number']}'");//查询用户H5注册绑定优惠券但未激活优惠券的用户是否存在
					 if(empty($res)) {
						$data['coupon_number']=$couponRow['coupon_number'];
					 }					 
				}
				$data['coupon']=$total;
                $result = $mysql->update('xgj_users',$data,"user_id = '{$uid}'");
                if (!empty($result)) echo '兑换成功！' ;
                else echo '兑换失败！';
         }else{
                echo '兑换失败1！';exit;
         }

          
    }

    public function couponList($page=null,$num=null){
        if ($page == null) {
            $limit = '';
        }else{
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }
        $mysql = new db();
        $sql = "SELECT * FROM `xgj_coupon` WHERE `user_id` = '{$_SESSION['userId']}' and is_status = '0' order by activate_time desc $limit";
        $couponAll = $mysql->getAll($sql);
        return $couponAll;
    }

    public function countCouponMoney(){
        $mysql = new db();
        $sql = "SELECT * FROM `xgj_coupon` WHERE `user_id` = '{$_SESSION['userId']}'";
        $couponAll = $mysql->getAll($sql);
        foreach ($couponAll as $k => $v) {
            $price[] = $v['discount_amount'];
        }
        return array_sum($price);
    }

    public function delcoupon($id){
        $mysql = new db();
        $updateDate = array(
            'is_status'     => '1'
            );
        $return = $mysql->update('xgj_coupon',$updateDate,"id = '{$id}'");
        echo $return;
    }

    public function addComment($table,$data){
        $re = $this->m->add($table,$data);
        return $re;
    }

    public function detailList($id){
        $mysql = new db();
        $sql = "SELECT * FROM `xgj_furnish_order_detail` d join xgj_furnish_order_info i on d.order_id = i.order_id WHERE d.status = '0' AND d.detail_id = $id AND i.user_id = '{$_SESSION['userId']}'";
        $re = $mysql->getRow($sql);
        return $re;
    }

    public function updataFuStatus($id){
        $where = "detail_id = $id";
        $data = array(
            'status'    => 1,
            'time'      => time()
            );
        $re = $this->m->update('xgj_furnish_order_detail',$data,$where);
        return $re;
    }

    public function commentRow($id){
        $mysql = new db();
        $sql = "select * from xgj_furnish_comment where goods_id = $id and status = '1' and user_id = '{$_SESSION['userId']}'";
        $re['comment'] = $mysql->getRow($sql);

        $sql = "SELECT * FROM `xgj_furnish_order_detail` d join xgj_furnish_order_info i on d.order_id = i.order_id WHERE d.status = '1' AND d.detail_id = $id AND i.user_id = '{$_SESSION['userId']}'";
        $re['quote'] = $mysql->getRow($sql);

        $sql = "select * from xgj_dealer_adjust_info where order_code = '{$re['quote']['order_code']}' and user_id = '{$_SESSION['userId']}'";
        $re['house'] = $mysql->getRow($sql);
        
        if (empty($re['house'])){
            $sql = "select * from xgj_users_house where user_id = '{$_SESSION['userId']}'";
            $re['house'] = $mysql->getRow($sql);
        }

        return $re;
    }

    public function fuCommentRow($id){
        $mysql = new db();
        $sql = "select * from xgj_eu_comment where order_goods_id = $id and status = '1' and user_id = '{$_SESSION['userId']}'";
        $re['comment'] = $mysql->getRow($sql);

        if (!empty($re['comment']['images'])) $re['images'] = explode('|', $re['comment']['images']);

        $sql = "select * from xgj_eu_order_goods where id = $id and status = '1' and user_id = '{$_SESSION['userId']}'";
        $re['goods'] = $mysql->getRow($sql);

        return $re;
    }

    public function euComment($id){
        $sql = "select * from xgj_eu_comment where order_goods_id = $id";
        $return= $this->m->getRow($sql);
        return $return;
    }

    //查询海外超市订单信息
    public function supermarketOrderList(){
        $uid = $_SESSION['userId'];
        $sql = "select * from xgj_ov_order where user_id = $uid order by add_time desc";
        $list = $this->m->getAll($sql);
        foreach ($list as $k => $v) {
            if($v['order_status'] <= 5) $orderList['all'][]          = $v;                                //全部订单
            if($v['order_status'] == 0) $orderList['payment'][]      = $v;                                //待付款
            if($v['order_status'] > 0 && $v['order_status'] <= 2) $orderList['goodsReceipt'][] = $v;      //待收货
            if($v['order_status'] == 4) $orderList['evaluate'][]     = $v;                                //待评价
        }
        return $orderList;
    }

    //分页查询海外超市订单信息
    public function supermarketOrderPage($page=null,$num=null,$where=null){
        if(!empty($page)){
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }else{
            $limit='';
        }

        if (!empty($where)) $where = 'and '.$where;

        $sql = "select * from xgj_ov_order where user_id = '{$_SESSION['userId']}' $where order by add_time desc $limit";
        $list = $this->m->getAll($sql);
        return $list;
    }

    public function delEuOrder($id){
        $userId = $_SESSION['userId'];
        $sql = "DELETE FROM `xgj_ov_order` WHERE `id`='{$id}' AND user_id = '{$userId}' AND order_status = '0'";
        $re = $this->m->query($sql,'exec');
        return $re;
    }

    public function ovorderall($order_id,$order_sn){
        $user_id = $_SESSION['userId'];
        $mysql = new db();
        $sql = "select * from xgj_ov_order where sn = '$order_sn' and id = $order_id and user_id = $user_id";
        $data = $mysql->getAll($sql);
        return $data;
    }

    public function ovordersave($data,$order_id,$order_sn){
        $user_id = $_SESSION['userId'];
        $mysql = new db();
        $where = "sn = '$order_sn' and id = $order_id and user_id = $user_id";
        $return = $mysql->update('xgj_ov_order',$data,$where);
        return $return;
    }

    public function countsupermarketOrderEvaluateNone(){
        $sql = "select * from xgj_ov_order o join xgj_ov_order_goods g on o.id = g.order_id where o.order_status='4' and g.status='0' and g.user_id = '{$_SESSION['userId']}'";
        $return = $this->m->getAll($sql);
        return count($return);
    }

    public function countsupermarketOrderEvaluateBlock(){
        $sql = "select * from xgj_ov_order o join xgj_ov_order_goods g on o.id = g.order_id where o.order_status>='4' and o.order_status<='5' and g.status='1' and g.user_id = '{$_SESSION['userId']}'";
        $return = $this->m->getAll($sql);
        return count($return);
    }

    public function supermarketOrderEvaluateNone($limit=''){
        $sql = "select g.*,o.pay_time from xgj_ov_order o join xgj_ov_order_goods g on o.id = g.order_id where o.order_status='4' and g.status='0' and g.user_id = '{$_SESSION['userId']}' order by o.pay_time desc $limit";
        $return['none'] = $this->m->getAll($sql);
        return $return;
    }

    public function supermarketOrderEvaluateAll($limit=''){
        $sql = "select g.*,o.pay_time from xgj_ov_order o join xgj_ov_order_goods g on o.id = g.order_id where o.order_status>'3' and o.order_status<'6' and g.user_id = '{$_SESSION['userId']}' order by o.pay_time desc $limit";
        $return['none'] = $this->m->getAll($sql);
        return $return;
    }

    public function supermarketOrderEvaluateAlready($page=null,$num=null,$where=1){
        if(!empty($page)){
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }else{
            $limit='';
        }

        if ($where==1) $sql = "select g.*,o.pay_time from xgj_ov_order o join xgj_ov_order_goods g on o.id = g.order_id where o.order_status='4' and g.status='0' and g.user_id = '{$_SESSION['userId']}' order by o.pay_time desc $limit";
        else if ($where == 3) $sql = "select g.*,o.pay_time from xgj_ov_order o join xgj_ov_order_goods g on o.id = g.order_id where o.order_status>'3' and o.order_status<'6' and g.user_id = '{$_SESSION['userId']}' order by o.pay_time desc $limit";
        else $sql = "select g.*,o.pay_time from xgj_ov_order_goods g join xgj_ov_order o on g.order_id = o.id where g.status='1' and g.user_id = '{$_SESSION['userId']}' order by o.pay_time desc $limit";
        $return= $this->m->getAll($sql);

        return $return;
    }

    public function ovEvaluate($id){
        $sql = "select * from xgj_ov_order_goods g join xgj_ov_order o on g.order_id = o.id where g.id = $id and g.user_id = '{$_SESSION['userId']}'";
        $return= $this->m->getRow($sql);
        return $return;
    }

    public function addOvData($data){
        $re = $this->m->add('xgj_ov_comment',$data);
        return $re;
    }

    public function updataOvStatus($id){
        $where = "id = $id and user_id = '{$_SESSION['userId']}'";
        $data = array(
            'status'    => 1,
            'time'      => time()
        );

        $orderStatus['order_status'] = 5;

        $re = $this->m->update('xgj_ov_split_order',$orderStatus,"detail_id=$id");

        $re = $this->m->update('xgj_ov_order_goods',$data,$where);

        //查询订单ID
        $sql = "select order_id from xgj_ov_order_goods where $where";
        $orderId = $this->m->getRow($sql);

        //查询是否全部评价完毕
        $sql = "select * from xgj_ov_order_goods where order_id='{$orderId['order_id']}' and status='0' and user_id = '{$_SESSION['userId']}'";
        $orderList = $this->m->getAll($sql);

        if (empty($orderList)) {
            //全部完毕后更改订单状态
            $update = array(
                'order_status'  => '5'
            );

            $re = $this->m->update('xgj_ov_order',$update,"id='{$orderId['order_id']}' and user_id = '{$_SESSION['userId']}'");
        
            if (!empty($re)) {

                //查询订单应付价格
                $sql = "select deal_price from xgj_ov_order where id='{$orderId['order_id']}' and user_id = '{$_SESSION['userId']}'";
                $orderPrice = $this->m->getRow($sql);

                if ($orderPrice['deal_price'] <= '0') {
                    return $re;exit;
                }

                //添加积分
                $addData = array(
                    'user_id'   =>$_SESSION['userId'],
                    'user_name' =>$_SESSION['userName'],
                    '`order`'   =>$orderId['order_id'],
                    'integral'  =>floor($orderPrice['deal_price']*0.05),
                    'time'      =>time(),
                    '`status`'  =>'2',
                    'class'     =>'3',
                );
               
                $re = $this->m->add('xgj_user_integral',$addData);

                if (!empty($re)) {
                    $sql = "select integral from xgj_users where user_id = '{$_SESSION['userId']}'";
                    $userIntegral = $this->m->getRow($sql);

                    $integral['integral'] = $userIntegral['integral']+floor($orderPrice['deal_price']*0.05);
                    $re = $this->m->update('xgj_users',$integral,"user_id = '{$_SESSION['userId']}'");
                }
            }
        }
        return $re;
    }

    public function ovComment($id){
        $sql = "select * from xgj_ov_comment where order_goods_id = $id";
        $return= $this->m->getRow($sql);
        return $return;
    }


    public function getOvGoodsId($id){
        $sql = "select * from xgj_ov_order_goods where id = $id and user_id = '{$_SESSION['userId']}'";
        $return= $this->m->getRow($sql);
        return $return['goods_id'];
    }

    public function getEuGoodsId($id){
        $sql = "select * from xgj_eu_order_goods where id = $id and user_id = '{$_SESSION['userId']}'";
        $return= $this->m->getRow($sql);
        return $return['goods_id'];
    }

    public function getOvGoodsComment($id){
        $sql = "select c.*,g.goods_title from xgj_ov_comment c join xgj_ov_order_goods g on c.order_goods_id = g.id where g.id = $id and c.user_id = '{$_SESSION['userId']}'";
        $return['comment'] = $this->m->getRow($sql);

        if(!empty($return['comment']['images'])) $return['images'] = explode('|', $return['comment']['images']);
        return $return;
    }

    public function integralCount($os){
        if ($os==1)      $where = '';
        else if ($os==2) $where = "status='2' AND";
        else if ($os==3) $where = "status='1' AND";
        $sql = "select count(integral_id) from xgj_user_integral where $where user_id = '{$_SESSION['userId']}' order by time desc";
        $return = $this->m->getRow($sql);
        return $return['count(integral_id)'];
    }
    
    public function integral($page=null,$num=null,$os=1){

        if ($page == null) {
            $limit = '';
        }else{
            $page = ($page-1)*$num;
            $limit = "limit $page,$num";
        }

        if ($os==1)      $where = '';
        else if ($os==2) $where = "status='2' AND";
        else if ($os==3) $where = "status='1' AND";

        $sql = "select * from xgj_user_integral where $where user_id = '{$_SESSION['userId']}' order by time desc $limit";
        $return = $this->m->getAll($sql);

        foreach ($return as $k => $v) {
            if ($v['class']==1) {
                $where = 'i.order_id = '.$v['order'].' and i.user_id = '.$_SESSION['userId'];
                $sql = "select i.order_code,d.quote_name from xgj_furnish_order_info i join xgj_furnish_order_detail d on i.order_id=d.order_id where $where";
                $result = $this->m->getRow($sql);
                $return[$k]['sn'] = $result['order_code'];
                $return[$k]['name'] = $result['quote_name'];
            }

            if ($v['class']==2) {
                $where = 'o.id = '.$v['order'].' and o.user_id = '.$_SESSION['userId'];
                $sql = "select o.sn,g.goods_title from xgj_eu_order o join xgj_eu_order_goods g on o.id=g.order_id where $where";
                $result = $this->m->getRow($sql);
                $return[$k]['sn'] = $result['sn'];
                $return[$k]['name'] = $result['goods_title'];
            }

            if ($v['class']==3) {
                $where = 'o.id = '.$v['order'].' and o.user_id = '.$_SESSION['userId'];
                $sql = "select o.sn,g.goods_title from xgj_ov_order o join xgj_ov_order_goods g on o.id=g.order_id where $where";
                $result = $this->m->getRow($sql);
                $return[$k]['sn'] = $result['sn'];
                $return[$k]['name'] = $result['goods_title'];
            }
        }
        return $return;
    }

    public function totalIntegral(){
        $sql = "select integral from xgj_users where user_id = '{$_SESSION['userId']}'";
        $return = $this->m->getRow($sql);
        return $return['integral'];
    }

    public function ovOrderDetails($splitId){

        $userId = $_SESSION['userId'];

        $sql = "select order_id,order_status from xgj_ov_split_order where id = $splitId";
        $row = $this->m->getRow($sql);

        if (empty($row)) {
            return 'error';exit;
        }

        if ($row['order_status']=='0') {
            $sql = "select s.*,g.*,gs.goods_sn,s.id id from xgj_ov_split_order s join xgj_ov_order_goods g on s.detail_id = g.id join xgj_ov_goods gs on g.goods_id=gs.id where s.user_id=$userId and s.order_id='{$row['order_id']}'";
            $list['splitAll'] = $this->m->getAll($sql);
            $list['split'] = $list['splitAll']['0'];
        }else{
            $sql = "select s.*,g.*,gs.goods_sn,s.id id from xgj_ov_split_order s join xgj_ov_order_goods g on s.detail_id = g.id join xgj_ov_goods gs on g.goods_id=gs.id where s.user_id=$userId and s.id=$splitId";
            $list['split'] = $this->m->getRow($sql);
        }

        if (empty($list['split'])) {
            return 'error';exit;
        }

        $sql = "select * from xgj_ov_express where split_order_id='{$list['split']['id']}'";
        $list['express'] = $this->m->getRow($sql);

        $sql = "select * from xgj_ov_order where id='{$list['split']['order_id']}'";
        $list['order'] = $this->m->getRow($sql);

        $sql = "select * from xgj_user_integral where `order`='{$list['split']['order_id']}' and status='1' and class='3'";
        $integralList = $this->m->getAll($sql);
        $list['integral'] = 0;
        foreach ($integralList as $k => $v) {
            $list['integral'] += $v['integral'];
        }

        $sql = "select * from xgj_coupon_info where order_id='{$list['split']['order_id']}' and class_id='1'";
        $coupon = $this->m->getAll($sql);
        $list['coupon'] = 0;
        foreach ($coupon as $k => $v) {
            $list['coupon'] += $v['use_money'];
        }
        return $list;
    }

    public function euOrderDetails($splitId){

        $userId = $_SESSION['userId'];

        $sql = "select order_id,order_status from xgj_eu_split_order where id = $splitId";
        $row = $this->m->getRow($sql);

        if (empty($row)) {
            return 'error';exit;
        }

        if ($row['order_status']=='0') {
            $sql = "select s.*,g.*,gs.goods_sn,s.id id from xgj_eu_split_order s join xgj_eu_order_goods g on s.detail_id = g.id join xgj_eu_goods_new gs on g.goods_id=gs.id where s.user_id=$userId and s.order_id='{$row['order_id']}'";
            $list['splitAll'] = $this->m->getAll($sql);
            $list['split'] = $list['splitAll']['0'];
        }else{
            $sql = "select s.*,g.*,gs.goods_sn,s.id id from xgj_eu_split_order s join xgj_eu_order_goods g on s.detail_id = g.id join xgj_eu_goods_new gs on g.goods_id=gs.id where s.user_id=$userId and s.id=$splitId";
            $list['split'] = $this->m->getRow($sql);
        }
  
        if (empty($list['split'])) {
            return 'error';exit;
        }

        $sql = "select * from xgj_eu_express where split_order_id='{$list['split']['id']}'";
        $list['express'] = $this->m->getRow($sql);

        $sql = "select * from xgj_eu_order where id='{$list['split']['order_id']}'";
        $list['order'] = $this->m->getRow($sql);

        $sql = "select * from xgj_user_integral where `order`='{$list['split']['order_id']}' and status='1' and class='2'";
        $integralList = $this->m->getAll($sql);
        $list['integral'] = 0;
        foreach ($integralList as $k => $v) {
            $list['integral'] += $v['integral'];
        }

        $sql = "select * from xgj_coupon_info where order_id='{$list['split']['order_id']}' and class_id='1'";
        $coupon = $this->m->getAll($sql);
        $list['coupon'] = 0;
        foreach ($coupon as $k => $v) {
            $list['coupon'] += $v['use_money'];
        }
        return $list;
    }
}