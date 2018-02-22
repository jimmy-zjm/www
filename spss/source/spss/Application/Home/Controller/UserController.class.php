<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends BaseController {
	private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Home\Model\UserModel;
    }
	//个人中心首页
    public function index(){
    	//查出用户信息
		$userInfo = $this->m->centerSelInfo($_SESSION['user']["userId"]);
		$concern = $this->m->selectConcern($_SESSION['user']["userId"]);
    	$this->assign("userInfo", $userInfo);
    	$this->assign("concern", $concern);
    	$this->display();
    }
    //修改密码
    public function changePsd(){
    	$this->display();
    }
    //执行修改密码
    public function doChangePsd(){
    	//判断是否有POST数据提交
		if(!empty($_POST)){
			$oldpass = trim($_POST["oldPassWordName"]);
			$newpass = trim($_POST["newPassWordName"]);
			$renewpass = trim($_POST["rPassWordName"]);
			if ($newpass != $renewpass) {
				echo "<SCRIPT type='text/javascript'>alert('修改密码失败,2次密码不一致,请重试!');history.back();</SCRIPT>";
				exit;
			}
			if(!preg_match("/^\w{6,15}$/", $newpass)){
			    echo "<SCRIPT type='text/javascript'>alert('修改密码失败,密码为6-15位数字字母下划线组成,请重试!');history.back();</SCRIPT>";
				exit;
			}
			$oldPassWord = md5($oldpass.C(MD5_PASSWORD));	//原始密码
			$newPassWord = md5($newpass.C(MD5_PASSWORD));	//新密码
			$renewPassWord = md5($renewpass.C(MD5_PASSWORD));	//确认新密码
			//判断原始密码是否正确
			$checkResult = $this->m->checkPassWordToModify($_SESSION['user']['userId'], $oldPassWord);

			if(!empty($checkResult)){	//如果原始密码正确
				$passWordModifyResult = $this->m->PassWordToModify($_SESSION['user']['userId'], $oldPassWord, $newPassWord);

				if($passWordModifyResult > 0){
					echo "<SCRIPT type='text/javascript'>alert('修改密码成功!');window.location.href='".U('User/index')."';</SCRIPT>";
					exit;
				}else{
					echo "<SCRIPT type='text/javascript'>alert('修改密码失败,请重试!');history.back();</SCRIPT>";
					exit;
				}
			}else{	//如果原始密码错误
				echo "<SCRIPT type='text/javascript'>alert('原始密码错误,请重试!');history.back();</SCRIPT>";
				exit;
			}
		}
    }

    //个人信息
    public function info(){
    	$infoList = $this->m->userInfoList($_SESSION['user']['userId']);
    	//性别
		if($infoList["sex"] == 0){
			$this->assign("infoSex", '<input type="radio" name="sex" class="jdradio" value="0" checked="checked" /><label class="mr10">男</label><input type="radio" name="sex" class="jdradio" value="1"><label class="mr10" >女</label><input type="radio" name="sex" class="jdradio" value="2" /><label class="mr10">保密</label>');
		}elseif($infoList["sex"] == 1){
			$this->assign("infoSex", '<input type="radio" name="sex" class="jdradio" value="0" /><label class="mr10">男</label><input type="radio" name="sex" class="jdradio" value="1" checked="checked" /><label class="mr10">女</label><input type="radio" name="sex" class="jdradio" value="2" /><label class="mr10">保密</label>');
		}elseif($infoList["sex"] == 2){
			$this->assign("infoSex", '<input type="radio" name="sex" class="jdradio" value="0" /><label class="mr10">男</label><input type="radio" name="sex" class="jdradio" value="1" /><label class="mr10">女</label><input type="radio" name="sex" class="jdradio" value="2"  checked="checked" /><label class="mr10">保密</label>');
		}

		//月收入
		if($infoList["monthly_profit"] == "1万以上"){
			$this->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option selected="selected">1万以上</option><option>8千——1万</option><option>6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "8千——1万"){
			$this->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option selected="selected">8千——1万</option><option>6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "6千——8千"){
			$this->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option >8千——1万</option><option selected="selected">6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "4千——6千"){
			$this->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option>8千——1万</option><option>6千——8千</option><option selected="selected">4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "4千以下"){
			$this->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option>8千——1万</option><option>6千——8千</option><option>4千——6千</option><option selected="selected">4千以下</option></select>');
		}else{
			$this->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option selected="selected">请选择</option><option>1万以上</option><option>8千——1万</option><option>6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}

		//教育程度
		if($infoList["education_status"] == "初中"){
			$this->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option selected="selected">初中</option><option>高中</option><option>大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "高中"){
			$this->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option selected="selected">高中</option><option>大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "大学"){
			$this->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option selected="selected">大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "硕士"){
			$this->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option>大学</option><option selected="selected">硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "博士"){
			$this->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option>大学</option><option>硕士</option><option selected="selected">博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "其他"){
			$this->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option>大学</option><option>硕士</option><option>博士</option><option selected="selected">其他</option></select>');
		}else{
			$this->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option selected="selected">请选择</option><option>初中</option><option>高中</option><option>大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
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

		$this->assign("infoList",$infoList);
		$this->assign("infoBirthdayYear",$birYear);
		$this->assign("infoBirthdayMonth",$birMonth);
		$this->assign("infoBirthdayDate",$birDate);

    	$this->display();
    }

    public function editInfo(){
    	layout(false);
    	if($_POST){
			$userId = $_SESSION['user']['userId'];
			//头像
	         if(isset($_FILES['userFace'])&&$_FILES['userFace']['error']==0){
	         	$oldName=$_POST['oldimg'];
	            $image = uploadOne('userFace','UserFace',C('IMG_THUMB_FACE'));
	            if($image['code']!=1){
	                 //头像上传失败
	                 $this->error = $image['error'];
	                 return false;
	            }
	             deleteImage($oldName,C('IMG_THUMB_FACE'));
	             $data['face'] = $image['images'];
	         }
			$data['user_name'] = I('infoAlias');	//昵称
			//$data['mobile_phone']  = $_POST["infoMobilePhone"];	//手机
			$data['email'] =  I('infoEmail');	//邮箱
			//$infoAddr = $_POST["infoAddr"];	//地址
			$data['real_name'] =  I('name');
			$data['identity_card'] =  I('infoIdentityCard');	//身份证
			$birYear =  I('birYearSelName');	//生日(年份)
			$birMonth =  I('birMonthSelName');	//生日(月份)
			$birDate =  I('birDateSelName');	//生日(日期)
			$data['birthday'] = $birYear."-".$birMonth."-".$birDate;	//生日
			$data['sex'] =  I('sex');	//性别
			$data['monthly_profit'] = I('infoMonthlyProfitSelName');	//月收入
			$data['education_status']=  I('educationStatusSelName');   //教育程度
			$data['work'] =  I('occupation');  //所在行业
			$rs = $this->m->userInfoEdit($data,$userId); 
			if($rs!==false){
				$this->success('修改成功',U('User/info'));
			}else{
				$this->error('修改失败');
			}
		}
    }
    //收货地址
    public function address(){
    	$user_id=$_SESSION['user']['userId'];
    	//查询省级城市
    	$area = getPCD();
    	$data_=$this->m->getAddressByUserId($user_id);
    	$num=$this->m->getAddressCountByUserId($user_id);
    	$this->assign("area",$area);
    	$this->assign("data_",$data_);
    	$this->assign("num",$num);
    	$tab=isset($_GET['tab'])?$_GET['tab']:'';
    	if($tab=='edit'){
    		$a_id = I('id');
	    	$data=$this->m->getAddressById($a_id);
	    	$phone=explode('-',$data['a_phone']);
	    	foreach ($area as $k=>$v){
	    		if($v['name']==$data['a_pro']){
	    			$pro_id=$v['id'];
	    		}
	    	}
	    	$this->assign("data",$data);
	    	$this->assign("phone",$phone);
	    	$this->assign("pro_id",$pro_id);
    	}
    	$this->assign("tab",$tab);
    	$this->display();
    }

    //添加地址操作
    public function doAddAddress(){

    	layout(false);
		$user_id                =$_SESSION['user']['userId'];
		$num=$this->m->getAddressCountByUserId($user_id);
		$data['a_name']         =I('a_name');
		$data['user_id']        =$user_id;
		$data['a_mobile_phone'] =I('mobile');
		$data['a_phone']        =I('quhao').'-'.I('number').'-'.I('fenji');
		$data['a_zipcode']      =empty(I('zipcode'))?'000000':I('zipcode');
		$data['a_pro']          =getPCDName(I('province'));
		$data['a_city']         =getPCDName(I('city'));
		$data['a_area']         =getPCDName(I('district'));
		$data['a_addr']         =I('addr');
		$data['default']        =empty(I('default'))?0:1;
		if($data['default']==1){
			$res=$this->m->updateAddressInfo(array('default'=>0),array('user_id'=>$user_id));
		}
		if($num<6){
			$re=$this->m->addAddressInfo($data);
		}else{
			$id=$this->m->getNewIdByUserId($user_id);
			$re=$this->m->updateAddressInfo($data,array('a_id'=>$id));
		}
		if($re!==false){
			$this->success('添加地址成功', 'address');
		}else{
			$this->error('添加地址失败', 'address');
		}
    }

    //编辑地址操作
    public function doUpdateAddress(){
    	layout(false);
    	$a_id = I('id');
		$user_id                =$_SESSION['user']['userId'];
		$data['a_name']         =I('a_name');
		$data['user_id']        =$user_id;
		$data['a_mobile_phone'] =I('mobile');
		$data['a_phone']        =I('quhao').'-'.I('number').'-'.I('fenji');
		$data['a_zipcode']      =empty(I('zipcode'))?'000000':I('zipcode');
		$data['a_pro']          =getPCDName(I('province'));
		$data['a_city']         =getPCDName(I('city'));
		$data['a_area']         =getPCDName(I('district'));
		$data['a_addr']         =I('addr');
		$data['default']        =empty(I('default'))?0:1;
		$re=$this->m->updateAddressInfo($data,array('a_id'=>$a_id));
		if($re!==false){
			$this->success('修改地址成功', 'address');
		}else{
			$this->error('修改地址失败');
		}
    }

    //删除地址操作
    public function delAddress(){
    	layout(false);
    	$a_id = I('id');
		$re=$this->m->delAddressInfo(array('a_id'=>$a_id));
		if($re!==false){
			echo '1';die;
		}else{
			echo '2';die;
		}
    }

    //更新默认
    public function defaultAddress(){
    	layout(false);
    	$a_id = I('id');
    	$user_id                =$_SESSION['user']['userId'];
    	$res=$this->m->updateAddressInfo(array('default'=>0),array('user_id'=>$user_id));
    	if($res!==false){
    		$re=$this->m->updateAddressInfo(array('default'=>1),array('a_id'=>$a_id));
			if($re!==false){
				$info=M('xgj_address')->where(array('default'=>1,'user_id'=>$user_id))->find();
				$data[0]=$info['a_name']."&nbsp;".$info['a_mobile_phone'];
				$data[1]=$info['a_pro']."&nbsp;".$info['a_city']."&nbsp;".$info['a_area']."&nbsp;".$info[''].$info['a_addr'];
				echo json_encode($data);die;
			}else{
				echo '2';die;
			}
    	}else{
    		echo '2';die;
    	}	
    }

    //查询省市县三级联动
    public function area(){
    	$id = $_GET['v'];
    	$return = M('xgj_area')->where("pid=$id")->field('id,name')->select();
    	echo json_encode($return);
    }

    //登录
    public function login(){
    	$this->display();
    }


    public function outLogin(){
    	unset($_SESSION['user']);
		$_SESSION['auto'] = 'false';
    	//执行跳转
		$this->redirect('Index/index');
    }

    //忘记密码
    public function changePass(){
    	$this->display();
    }

    //执行修改密码
    public function doChangePass(){

    	//判断是否有POST数据提交
		if(!empty($_POST)){
			$tel 		  = I('post.mobile_phone');
			$pass      	  = I('post.password');
			$repass       = I('post.repassword');
			$msg          = I('post.msg');

			if (time()-$_SESSION['msgTime'] > 180)      $this->error('验证码已过期，请重新获取！');

			if(!preg_match("/^1[34578]\d{9}$/", $tel)) 	$this->error('请填写正确的手机号码！!');

			if(!preg_match("/^\w{6,15}$/", $pass)) 		$this->error('请正确填写密码，密码为6-15位！!');
			
			if ($pass !== $repass)  	  				$this->error('两次密码不一致请重新输入！');
			if ($msg  !=  $_SESSION['msg']) 			$this->error('验证码错误!');
			if ($tel  !=  $_SESSION['tel']) 			$this->error('手机号码与获取验证码号码不一致!');

			$map['mobile_phone'] = $tel;
	    	$re = M('xgj_users')->where($map)->find();
	    	if (empty($re))  	  			  			$this->error('您还未注册过!');


			$save['password'] = md5($pass.C(MD5_PASSWORD));

			$data   = M('xgj_users')->create($save);

			$return = M('xgj_users')->where($map)->save($data);

			if ($return===false) $this->error('修改失败！请重试!');

			$_SESSION['user']['userId']   = $re['user_id'];
			$_SESSION['user']['userName'] = $tel;

			if (!empty($_SESSION['url'])) $url = $_SESSION['url'];
			else $url = 'Index/index';
			
			echo U("$url");
		}
    }

    //验证MSG
    public function verifyMsg(){
    	$tel = I('post.mobile_phone');
    	$msg = I('post.msg');

    	if (time()-$_SESSION['msgTime'] > 180)      $this->error('验证码已过期，请重新获取！');
    	if ($msg != $_SESSION['msg']) 				$this->error('验证码错误!');
		if ($tel != $_SESSION['tel']) 				$this->error('手机号码与获取验证码号码不一致!');
		$map['mobile_phone'] = $tel;
    	$re = M('xgj_users')->where($map)->find();
    	if (empty($re))  	  		  				$this->error('您还未注册过!');
    	echo 1;
    }

    //注册
    public function register(){
    	$this->display();
    }

    //验证码
    public function verify(){
    	$config = array(
            'length'=>4,
            'fontSize'=>24,
            'useCurve'=>false,
            'useNoise'=>false,
        );
        $verify = new \Think\Verify($config);
        $verify->entry();
    }

    function check_verify($code, $id = ''){
	    $verify = new \Think\Verify();
	    return $verify->check($code, $id);
	}

    //执行注册
    public function doRegister(){
    	layout(false);

		$tel    = I('post.mobile_phone');
		$pass   = I('post.password');
		$repass = I('post.repassword');
		$msg 	= I('post.msg');

		foreach ($_POST as $key => $v) {
			if (empty($v)) $this->error('请填写完整再提交');
		}

		if (time()-$_SESSION['msgTime'] > 180)      $this->error('验证码已过期，请重新获取！');

		if(!preg_match("/^1[34578]\d{9}$/", $tel)) 	$this->error('请填写正确的手机号码！!');
		if(!preg_match("/^\w{6,15}$/", $pass)) 		$this->error('请正确填写密码，密码为6-15位！!');
		
		if ($pass !== $repass)  	  				$this->error('两次密码不一致请重新输入！');
		if ($msg  != $_SESSION['msg']) 				$this->error('验证码错误!');
		if ($tel  != $_SESSION['tel']) 				$this->error('手机号码与获取验证码号码不一致!');

		$map['mobile_phone'] = $tel;
    	$re = M('xgj_users')->where($map)->count();
    	if ($re > 0)  	  			  				$this->error('您已注册过，不可再次注册!');


		$_POST['password'] = md5($pass.C(MD5_PASSWORD));
		$_POST['reg_time'] = time();

		$data = M('xgj_users')->create();
		$return = M('xgj_users')->add();

		if (empty($return)) $this->error('注册失败！请重试!');

		$_SESSION['user']['userId'] = $return;

		if (!empty($_SESSION['url'])) $url = $_SESSION['url'];
		else $url = 'Index/index';
		
		echo U("$url");
		// $this->redirect($url);
    }

    //发送验证码
    public function message(){
		$verify = I('post.verify');
		$re = $this->check_verify($verify);
		if ($re == true) {
			$tel 	= I('post.tel');
			$rest=getMessage($tel);
			if($rest['success']===true){
				echo 1;
			}else{
				echo -1;
			}
		}else{
			echo '2';
		}
	}

	//验证验证码
	public function verification(){
		$msg = I('post.msg');
		$tel = I('post.tel');
		if (time()-$_SESSION['msgTime'] > 180){
			echo '验证码已过期，请重新获取！';
		}else if ($msg != $_SESSION['msg']) {
			echo '验证码错误';
		}else if($tel != $_SESSION['tel']){
			echo '手机号码与获取验证码号码不一致';
		}else if ($msg == $_SESSION['msg'] && $tel == $_SESSION['tel']) {
			echo '1';
		}else{
			echo '验证失败';
		}
	}

    public function checkRegister(){
    	$map['mobile_phone'] = I('post.tel');
    	$re = M('xgj_users')->where($map)->count();
    	echo $re;
    }

	/***关注的商品***/
	
	function concern(){

		$where['user_id']=$_SESSION['user']['userId'];
		$count = M('xgj_concern')->where($where)->count();
		$show       = getPage($count,10);
		$data = M('xgj_concern')->where($where)->limit($show['limit'])->select();
		// foreach($data as &$val){
		// 	if($val['class_id']==1){
		// 		$val['lv']=1;				
		// 	}
		// }
		if(I('del')==1&&I('id')!=''){
			$res=M('xgj_concern')->where(array('c_id'=>I('id')))->delete(); 
			echo $res;
		}		
		$this->assign('concernGoodsList',$data); 
		$this->assign('page', $show['page']);
		$this->display();
	}
	//优惠券主页
	function actCoulist(){

		//查询优惠券总金额 
		$where['user_id']=$_SESSION['user']["userId"];
		$couponAvailable = D('user')->totalUserCoupons($where);

		//查询可用优惠券总金额
		$use_money = D('user')->couponInfo($where);
		


		$count = M('xgj_coupon_info')->where($where)->count();
		$show       = getPage($count,8);
		$data =D('user')->couUserInfo($where,$show['limit']); 
		$this->assign('page', $show['page']);
		$this->assign("couponInfo",$data);					//所以优惠券详细使用信息
		$this->assign("useMoney",$use_money);				//所以已使用优惠券总金额
		$this->assign("couponAvailable",$couponAvailable);	//所以未使用(可使用)的优惠券总金额

	 	$this->display();
   	}
		//优惠券激活
	function actcou(){

		$uid  = $_SESSION['user']['userId'];
		$code = $_GET['code'];
		$pass = $_GET['pass'];
   		if (empty($uid)) {
   			echo '未查询到登录';exit;
   		}
		$return =D('user')->activationCoupon($code,$pass,$uid);
		echo $return;
   	}
		
	// couponShow	显示优惠券兑换明显页面

	function coupon(){

		//查询优惠券总金额 
		$where['user_id']=$_SESSION['user']["userId"];
		$couponAvailable = D('user')->totalUserCoupons($where);
		//查询可用优惠券总金额
		$use_money = D('user')->couponInfo($where);
		$where['is_status']='0';
		$sumAmout = M('xgj_coupon')->where($where)->sum('discount_amount');
		
		$count = M('xgj_coupon')->where($where)->count();
		$show       = getPage($count,8);		
		$data =D('user')->couponList($where,$show['limit']); 
		
		$this->assign("sumAmout",$sumAmout);			//兑换优惠券金额
		$this->assign("couponList",$data);							//优惠券兑换信息
		$this->assign("useMoney",$use_money);						//所以已使用优惠券总金额
		$this->assign("couponAvailable",$couponAvailable);			//所以未使用(可使用)的优惠券总金额
		$this->assign('page', $show['page']);
		$this->display();
	}
	//删除优惠券兑换明细
	public function delcoupon(){
		$id = $_GET['id'];
		 $updateDate['is_status'] ='1'; 
        $return = M('xgj_coupon')->where(array('id'=>$id))->data($updateDate)->save(); 
		echo $return;
	}

	//积分明细
	public function integral(){
		$where['user_id']=$_SESSION['user']["userId"];
		$count = M('xgj_user_integral')->where($where)->count();
		$show       = getPage($count,8);	
		$data =D('user')->integral($where,$show['limit']); 
		$userintegral=D('user')->getuserintl($where);//个人积分总额
		$this->assign("totalIntegral",$userintegral);
		$this->assign("page",$show['page']);
		$this->assign('list',$data);
		$this->display();
	}

	//积分收入
	public function integralin(){
		$where['user_id']=$_SESSION['user']["userId"];
		$userintegral=D('user')->getuserintl($where);//个人积分总额
		$where['status']='2';
		$count = M('xgj_user_integral')->where($where)->count();
		$show       = getPage($count,8);	
		$data =D('user')->integral($where,$show['limit']); 
		$this->assign("totalIntegral",$userintegral);
		$this->assign("page",$show['page']);
		$this->assign('list',$list);
		$this->display();
	}

	//积分支出
	public function integralout(){
		$where['user_id']=$_SESSION['user']["userId"];
		$userintegral=D('user')->getuserintl($where);//个人积分总额
		$where['status']='1';
		$count = M('xgj_user_integral')->where($where)->count();
		$show       = getPage($count,8);	
		$data =D('user')->integral($where,$show['limit']); 
		$this->assign("totalIntegral",$userintegral);
		$this->assign("page",$show['page']);
		$this->assign('list',$data);
		$this->display();
	}

	//健康舒适家订单页面
	function homeOrder(){
		$where['user_id']=$_SESSION['user']["userId"];
		$count = M('xgj_furnish_order_info')->where($where)->count();
		$show       = getPage($count,15);	
		$data=D('user')->goodsdata($where,$show['limit']);
		$this->assign("page",$show['page']);
		$this->assign('data',$data);
		$this->display();
	}

	//
	function homeOrderShow(){
		
		$where['order_id']=(int)I('get.id');
		if(empty($where['order_id'])) {
			$this->redirect('User/homeOrder');
		}
        $contentdata =D('user')->homeorder($where);
        $order_id=$contentdata["order_id"];
        $quote_info=M('xgj_furnish_order_detail d')->join('xgj_furnish_quote q on d.quote_id=q.quote_id')->where(array('d.order_id'=>$order_id))->select();
	    $ecprice='';
	    $trprice='';
	    foreach ($quote_info as $k=>$v){		
			if($contentdata['order_status']=='5'){
				$ecprice += $v['adjust_quote_price']*$v['num']*$v['ecprice']/100;
				$trprice  += $v['adjust_quote_price']*$v['num']*(100-$v['ecprice'])/100;
			}else{
				$ecprice += $v['quote_price']*$v['num']*$v['ecprice']/100;
				$trprice  += $v['quote_price']*$v['num']*(100-$v['ecprice'])/100;
			}
	     }

	     $contentdata['ecpri']=ceil($ecprice);
	     $contentdata['trpri']=ceil($trprice-500);
       

        //施工系统 
        $countPlan =D('user')->countPlan(array('b.user_id'=>$_SESSION['user']["userId"],'a.order_id'=>$order_id));

		$count=M('xgj_furnish_order_construct')->where(array('order_id'=>$order_id,'status'=>array('NEQ',1)))->count();

        //施工计划
        if ($countPlan){
	        foreach ($countPlan  as $k=>$v){
	         	$Plan[]=D('user')->constructPlan(array('detail_id'=>$v['detail_id']));	
	        }
			
			//文件区域
			$dealerOrderFile =D('user')->file(array('a.order_id'=>$order_id,'b.status'=>'1'));
			//产品手册
			$Product=D('user')->Productfile(array('order_id'=>$order_id,'status'=>'2'));
			
			//施工计划与质量审核
			$this->assign('Plan',$Plan);
			
			//文件区域
			$this->assign('dealerOrderFile',$dealerOrderFile);
			//产品手册
			$this->assign('Producttxt',$Product);
		}
		//var_dump($Plan);die;
        //订单信息
        $this->assign("orderInfo",$contentdata );
        $this->assign("count",$count );
        
        //系统名称
        $this->assign('countPlan',$countPlan);
		$this->display();
	}

	//文件下载
	function orderfildown(){		 
		layout(false);
		$file_id = (int)$_GET['file_id'];
		$order_id = (int)$_GET['id'];
		if(empty($file_id)) {
			$this->redirect('User/homeOrderShow',array('id'=>$order_id));
		}
		$result     = M('xgj_furnish_order_file')->where(array('file_id'=>$file_id))->find();
		
		if($result['class']=='3')
			  down("/Public/Uploads/",$result['file_img']);    
		else
			 down("/Public/Uploads/",$result['file_img'],'1');	
	}

	//家居订单评价列表页
	function hEvaluateList(){
		$uid=$_SESSION['user']["userId"];
		$action=isset($_GET['action'])?$_GET['action']:'';//var_dump($a);die();

		$data2 = D('user')->evaluateShow(array('a.user_id'=>$uid,'a.schedule_status'=>'7','b.status'=>'0')); //家居待评价			
		$data3 = D('user')->evaluateShow(array('a.user_id'=>$uid,'b.status'=>'1')); //家居已评价
		$count = count($data2)+count($data3);
		//全部订单
		$show       = getPage($count,8);		
		$data=D('user')->evaluateShow("(a.user_id=$uid and a.schedule_status= '7' and b.status='0') or(a.user_id=$uid and b.status='1')",$show['limit']);

		if($action=='waiting'){
			$show       = getPage(count($data2),8);	
			$data= D('user')->evaluateShow(array('a.user_id'=>$uid,'a.schedule_status'=>'7','b.status'=>'0'),$show['limit']); //家居待评价
		}
		elseif($action=='already'){
			$show       = getPage(count($data3),8);	
			$data= D('user')->evaluateShow(array('a.user_id'=>$uid,'b.status'=>'1'),$show['limit']); //家居已评价
		}
		$this->assign("page",$show['page']);		
		$this->assign("data",$data);
		$this->assign("cdata",count($data2));	
		$this->assign("cdata1",count($data3));	
		$this->display();
	}

	//家居评价详情页面
	function hEvaluate(){
		$id = (int)$_GET['id'];
		if(empty($id))
			$this->redirect('User/hEvaluateList');
		
		$data = D('user')->hEvaluateRow(array('user_id'=>$_SESSION['user']["userId"],'a.status'=>'0','a.detail_id'=>$id));

		if (empty($data['detail_id'])) {
			$this->redirect('User/hEvaluateList');
		}	
		$this->assign("list", $data);
		$this->display();
	}

	//家居评价提交处理
	function dohEvaluate(){
	layout(false);

	if(empty($_POST['is_time']) && @$_POST['is_time']!='0'){
	   	echo "<SCRIPT type='text/javascript'>alert('您好，请选择是否按时完工！');history.back();</SCRIPT>";exit;
	}

	if(empty($_POST['is_clean']) && @$_POST['is_clean']!='0'){
	   	echo "<SCRIPT type='text/javascript'>alert('您好，请选择工地现场是否整洁！');history.back();</SCRIPT>";exit;
	}

	if(empty((int)$_POST['detail_id']) ){
	   	echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
	}
	
	if (!empty($_FILES['images']['name']['0'])) {
		 $_FILES['images']['name']=array_filter($_FILES['images']['name']);
		 $_FILES['images']['type']=array_filter($_FILES['images']['type']);
		 $_FILES['images']['tmp_name']=array_filter($_FILES['images']['tmp_name']);
		 $_FILES['images']['error']=array_filter($_FILES['images']['error']);
		 $_FILES['images']['size']=array_filter($_FILES['images']['size']);
		// var_dump( $_FILES['images']);die();
		 $data=uploadone('images','hEvaluate','','','2');
		 if($data['code']==0)
			 $this->error($data['error']);
	}

	$addData = array(
		'class_id'     => '1',
		'goods_id'     => $_POST['detail_id'],
		'quote_id'     => $_POST['quote_id'],
		'user_name'    => $_SESSION['user']['userName'],
		'add_time'     => time(),
		'user_id'      => $_SESSION['user']['userId'],
		'status'     => '1',
		'order_id'   =>  $_POST['order_id'],
		'goods'        => $_POST['goods'],
		'distributor'  => $_POST['distributor'],
		'personnel'    => $_POST['personnel'],
		'construction' => $_POST['construction'],
		'content'      => trim($_POST['content']),
		'is_time'      => $_POST['is_time'],
		'is_clean'     => $_POST['is_clean'],
		'display'      => '1',
		'star'    	   => ceil(($_POST['goods']+$_POST['distributor']+$_POST['personnel']+$_POST['construction'])/4)

		);

	if (!empty($_POST['none']) && $_POST['none']=='on') $addData['is_none'] = '1';

	if (!empty($data['images'])) $addData['images'] = implode('|', $data['images']);

	$addComment = D('user')->addtable('xgj_furnish_comment',$addData);

	if (!empty($addComment)) $updataFuStatus = D('user')->updateOrderDetail($_POST['detail_id'],$_POST['order_id']);

	if (!empty($updataFuStatus)) 
		$this->redirect('User/hEvaluateList');
	}

	
	//家居评价详情页面
	function   hEvaluation(){

		if(empty((int)$_GET['id']))
			$this->redirect('User/hEvaluateList');
		
		$id = $_GET['id'];

		$commentRow = D('user')->commentRow($id,$_SESSION['user']['userId']);
		if (empty($commentRow['quote'])) {
			$this->redirect('User/hEvaluateList');
		}
		if (empty($commentRow['comment'])) {
			$this->redirect('User/hEvaluateList',3,'此评论由于内含违规内容被删除');
		}
		if (!empty($commentRow['comment']['images'])) $commentRow['images'] = explode('|', $commentRow['comment']['images']);

		$this->assign("commentRow",$commentRow);	
		$this->display();
	}

	

		/**
	 * order	我的订单  《欧洲团购，德国母婴》
	 */
	public function euOrderList(){
		
		$tab = I('get.tab');

		switch ($tab) {			
			case 'df':
				$where['order_status']='0';
				break;
			case 'dfh':
				$where['order_status']='1';
				break;
			case 'ds':
				$where['order_status']='2';
				break;
			case 'dp':
				$where['order_status']='4';
				break;
			default:
				$where['order_status']=array('neq','7');
				break;
			return $status;
		}
		$where['user_id']=$_SESSION['user']['userId'];
		$count=D('user')->count_order($where);//分页的总条数
		$show       = getPage($count,5);		
		$orderInfoAllList=D('user')->orderInfoAllList($where,$show['limit']);//显示列表内容
		//var_dump($orderInfoAllList);die();
		$this->assign('OrderAll',$orderInfoAllList); //全部订单的数据列表
	    $this->assign("page", $show['page']);  //订单总数量
		$this->assign("tab",$tab);
		$this->display();
	}
	
	//*建材确认收货
	public function theGoods(){
		$id=I('post.id');
		$order_id=I('post.order_id');
		$data['order_status']='4';

		if(M('xgj_eu_split_order')->where(array('id'=>$id))->save($data)){
			$where['order_id']=$order_id;
			$where['order_status']=array('in','4,5');
			$arr=M('xgj_eu_split_order')->where($where)->count('id');
			$arr1=M('xgj_eu_split_order')->where(array('order_id'=>$order_id))->count('id');
			if($arr==$arr1){//var_dump($arr,$arr1);die();
				M('xgj_eu_order')->where(array('id'=>$order_id))->save($data);
			}
			echo "1";exit;
		}else{
			echo "2";exit;
		}
	}




	//建材取消订单
	public function cancel(){
		$order_id=I('post.order_id');
		$data=array(
				'order_status'=>6,
		);
		if(M('xgj_eu_order')->where(array('id'=>$order_id))->save($data)){
			echo "1";exit;
		}else{
			echo "2";exit;
		}
	}



	//建材删除订单
	public function delOrder(){
	
		$order_id=I('post.order_id');
		$data=array(
				'order_status'=>7,
		);
		if(M('xgj_eu_order')->where(array('id'=>$order_id))->save($data)){
			echo "1";exit;
		}else{
			echo "2";exit;
		}
	}


  


	//欧洲建材订单详情
	function euOrder(){
		
		if( empty((int)I('get.id')) )
		    $this->redirect('User/euOrderList');
		
		$splid = I('get.id');
		$data = D('user')->euOrderDetails(array('a.user_id'=>$_SESSION['user']['userId'],'a.id'=>$splid,'order_status'=>array('neq','7')));
//var_dump($data['split']['order_status'] );die();
		$this->assign("data", $data);
		$this->display();
	}

/**
	 * 欧团评价页面数据显示
	 */
	function euEvaluateList(){
		$uid=$_SESSION['user']['userId'];
		$action=isset($_GET['action'])?$_GET['action']:'';
		$data   = D('user')->euEvaluateList(array('a.user_id'=>$uid,'a.order_status'=>'4','b.status'=>'0'));   //建材待评价
		$data1 = D('user')->euEvaluateList(array('a.user_id'=>$uid,'b.status'=>'1'));  //建材已评价
		//分页的总条数
		$orderAll = count($data)+count($data1);

	
		$show       = getPage($orderAll,8);		

		//全部评价订单
		$data_page=D('user')->euEvaluateList("(a.user_id = $uid and a.order_status='4' and b.status = '0') or (a.user_id = $uid and b.status='1') ",$show['limit']);
//var_dump($data_page);die;
		if($action=='waiting'){
			$show       = getPage(count($data),8);	
			$data_page= D('user')->euEvaluateList(array('a.user_id'=>$uid,'a.order_status'=>'4','b.status'=>'0'),$show['limit']); //建材待评价
		}
		elseif($action=='already'){
			$show       = getPage(count($data1),8);	
			$data_page= D('user')->euEvaluateList(array('a.user_id'=>$uid,'b.status'=>'1'),$show['limit']); //建材已评价
		}

		/*********************************/
//var_dump($data_page);die();
		$this->assign("page",$show['page']);	
		$this->assign("data",$data_page);		
		$this->assign("cdata",count($data));	
		$this->assign("cdata1",count($data1));	
		$this->display();
	}



	//建材评价提交页面
	function   euEvaluate(){
		$id = (int)I('get.id');
		if(empty($id)){
			$this->redirect('User/euEvaluateList');
		}
		$where['a.id']=$id;
		$where['a.user_id']=$_SESSION['user']['userId'];

		$euGoodsRow = D('user')->euGoodsRow($where);

		if (empty($euGoodsRow)) {
			echo "<SCRIPT type='text/javascript'>alert('该订单不存在');history.go(-1);</SCRIPT>";exit;
		}elseif($euGoodsRow['status']=='1'){
			echo "<SCRIPT type='text/javascript'>alert('该订单已评价');history.go(-1);</SCRIPT>";exit;
		}

		$this->assign("euGoodsRow", $euGoodsRow);
	
		$this->display();
	}


		//建材评价提交
	function addEuEvaluate(){

		if(empty($_POST['goods_id']) || empty($_POST['order_id']) || !preg_match("/^[1-5]$/", $_POST['describe']) || !preg_match("/^[1-5]$/", $_POST['logistics']) || !preg_match("/^[1-5]$/", $_POST['goods'])){
			echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
		}
		$ogid = $_POST['goods_id'];

		$goodsId =I('post.g_id');
		
		if (!empty($_FILES['images']['name']['0'])) {
			 $_FILES['images']['name']=array_filter($_FILES['images']['name']);
			 $_FILES['images']['type']=array_filter($_FILES['images']['type']);
			 $_FILES['images']['tmp_name']=array_filter($_FILES['images']['tmp_name']);
			 $_FILES['images']['error']=array_filter($_FILES['images']['error']);
			 $_FILES['images']['size']=array_filter($_FILES['images']['size']);
			// var_dump( $_FILES['images']);die();
			 $data=uploadone('images','euEvaluate','','','2');
			 if($data['code']==0)
			$this->error($data['error']);
		}
		


		$addData = array(
			'goods_id'       => $goodsId,
			'order_goods_id' => $ogid,
			'user_name'      => $_SESSION['user']['userName'],
			'add_time'       => time(),
			'user_id'        => $_SESSION['user']['userId'],
			'status'       => 1,
			'describe'     => $_POST['describe'],
			'logistics'      => $_POST['logistics'],
			'goods'          => $_POST['goods'],
			'content'        => trim($_POST['content']),
			'display'        => '1',
			'star'        	 => ceil(($_POST['describe']+$_POST['logistics']+$_POST['goods'])/3)
			);

			if (!empty($_POST['none']) && $_POST['none']=='on') $addData['is_none'] = '1';

			if (!empty($data['images'])) $addData['images'] = implode('|', $data['images']);

			

			if (!empty($addData)) $addEuData = D('user')->addtable('xgj_eu_comment',$addData);

			if (!empty($addEuData)) $updataEuStatus = D('user')->updataEuStatus( $ogid, $_SESSION['user']['userId'],I('post.order_id'));
			
			if (!empty($updataEuStatus)) $this->redirect('User/euEvaluation',array('id'=>$ogid));
		}




		//建材评价详情页面
		function   euEvaluation(){
			if(empty((int)I('get.id')))
				$this->redirect('User/euEvaluateList');
			

			$id = I('get.id');

			$fuCommentRow = D('user')->euComment($id,$_SESSION['user']['userId']);
			if (empty($fuCommentRow['comment'])) {
				$this->redirect('User/euEvaluateList','',3,'无此订单评论');
			}
			// echo $id;exit;
			$euGoodsRow = D('user')->euGoodsRow(array('a.id'=>$id,'a.user_id'=>$_SESSION['user']['userId']));

			// if (!empty($fuCommentRow['comment']['images'])) $fuCommentRow['images'] = explode('|', $fuCommentRow['comment']['images']);

			$this->assign("commentRow",$fuCommentRow);	
			$this->assign("euGoodsRow",$euGoodsRow);	

			$this->display();
		}


		//关注的欧洲建材商品加入购物车
    public function AddCart(){
      //查询是否登录
      if(empty($_SESSION['user']['userId'])) die('请先登录再加入购物车');

      /***************************/
      //查询商品是否存在
      $id   = I('post.goods_id');

      if(empty($id)){
      	die('没有该商品，核对后再次购买');
      }
      
      $num  = I('post.num');
      $c_id = I('post.id');

      if ($num < 1) die('数量不能小于1');

      $dataMap['is_putaway'] = '1';
      $dataMap['id']         = $id;
      $data = M('xgj_eu_goods_new')->field('type_id,shop_price,goods_title,face_image')->where($dataMap)->find();

      //商品不存在返回
      if (empty($data)) die('抱歉！没有您要的商品');
      /***************************/

      $attr = M('xgj_concern')->where(array("c_id"=>$c_id))->getField('attr');

      $attr = json_decode($attr,true);

      /***************************/
      //查询商品属性是否存在
      foreach ($attr as $k => $v) {
        $rowMap['a.id']       = $v['1'];
        $row = M('xgj_eu_goods_attr a')->join('xgj_eu_attribute b on a.attr_id = b.id')->field('a.*,b.name')->where($rowMap)->find();
        if (!empty($row) && $row['goods_id']==$id && $row['attr_id']==$v['0']) {
          //如存在处理商品属性 
          $addAttr[$row['name']] = $row['attr_value'];
          $attr_price[]          = $row['attr_price'];
        }else{
          die('您选择的'.$row['name'].'不存在');
        }
      }

      $price = array_sum($attr_price);
      /***************************/


      /***************************/
      //加入购物车表
      $addData = array(
        'user_id'   =>$_SESSION['user']['userId'],
        'cat_id'    =>$id,
        'price'     =>$data['shop_price']+$price,
        'class'     =>2,
        'attr'      =>json_encode($addAttr),
        'img'       =>$data['face_image'],
        );

      //查询购物车内是否有此商品 有就更新数量，没有就添加
      $is = M('xgj_furnish_cart')->where($addData)->find();
      if (empty($is)) {
        $addData['num']       = $num;
        $addData['shop_name'] = $data['goods_title'];
        $return = M('xgj_furnish_cart')->add($addData);
      }else{
        $saveMap['user_id'] = $_SESSION['user']['userId'];
        $saveMap['cart_id'] = $is['cart_id'];
        $save['num'] = $is['num']+$num;
        $return = M('xgj_furnish_cart')->where($saveMap)->save($save);
      }

      //返回结果
      if ($return>0) echo  empty($is)?$return:$is['cart_id'];
      else echo 'error';
      /***************************/
    }


		//建材评价详情页面
		function   maintain(){
			
			//echo(__ROOT__);die();
			$where['user_id']=$_SESSION['user']['userId'];
			$count = M('xgj_user_problem')->where($where)->count();
			$show       = getPage($count,8);	
			$data = D('user')->getafterService($where,$show['limit']);	
			//var_dump($data);die();
			$this->assign("data",$data);	
			$this->assign("page",$show['page']);
			$this->display();
		}

	

	public function upkeep(){
		$data = D('user')->getSorder(9);
		$page = getPage(count($data),8);
		$list = D('user')->getSorder(9,$page['limit']);
		$this->assign('page',$page['page']);
		$this->assign('list',$list);
		$this->display();
	}


	public function consumable(){
		$data = D('user')->getSorder(8);
		$page = getPage(count($data),8);
		$list = D('user')->getSorder(8,$page['limit']);
		$this->assign('page',$page['page']);
		$this->assign('list',$list);
		$this->display();
	}

	
	public function wxOrder(){
		$total = M('pad_order')->where([['uid'=>$_SESSION['user']['userId']]])->count();
		$page = getPage($total, 10);
		$list = M('pad_order')->where([['uid'=>$_SESSION['user']['userId']]])->limit($page['limit'])->select();
		foreach ($list as $k => $v) {
			$id .= ','.$v['id'];
		}
		$id = trim($id,',');
		$info = M('pad_order_info')->field('order_id,price,province,city,district,address')->where(['order_id'=>['in',$id]])->select();
		foreach ($info as $k => $v) {
			$money[$v['order_id']] += $v['price'];
			$addr[$v['order_id']] = $v['province'].$v['city'].$v['district'].$v['address'];
		}
        $this->assign('page',$page['page']);
        $this->assign('list',$list);
        $this->assign('money',$money);
        $this->assign('addr',$addr);
		$this->display();
	}

	public function wxOrderdetails(){
		$oid = I('get.oid');
        $map = ['o.id'=>$oid,'o.uid'=>$_SESSION['user']['userId']];
		$data=M('pad_order')->where($map)->find();
		$info = M('pad_order_info')->field('order_id,price,province,city,district,address')->where(['order_id'=>['in',$oid]])->select();
		foreach ($info as $k => $v) {
			$data['money'] += $v['price'];
			$data['addr']= $v['province'].$v['city'].$v['district'].$v['address'];
		}
        $re = M('pad_workplan')->where(['o_id'=>$oid])->select();
        foreach ($re as $k => $v) {
            $list[$v['workname']] = $v;
        }
         //查询所有系统
        $quoteinfo=M('pad_order_info i')
                ->where(['i.order_id'=>$oid])
                ->select();

        $this->assign('list',$list);
        $this->assign('quoteinfo',$quoteinfo);
        $this->assign('data',$data);
        $this->assign('oid',$oid);
		$this->display();
	}
	
	public function wxProcess(){

		$oid  = I('get.oid');
        $type = I('get.type');

        switch ($type) {
            case '1':
                $title='房屋面积';
                break;
            case '2':
                $title='施工图纸';
                break;
            case '3':
                $title='施工安装';
                break;
            case '4':
                $title='设备调试';
                break;
            case '5':
                $title='验收进度';
                break;
        }

        $re = M('pad_order')->where(['id'=>$oid,'uid'=>$_SESSION['user']['userId']])->count();
        if ($re > 0) {
            $list = M('pad_file')->where(['o_id'=>$oid,'types'=>$type])->order('add_time desc')->select();
            foreach ($list as $k => &$v) {
                $v['add_time'] = substr($v['add_time'], 0,10);
                $lists[$v['add_time']][] = $v;
            }
            $this->assign('list',$lists);
        }

        $this->assign('oid',$oid);
        $this->assign('title',$title);
		$this->display();
	}
	public function wxLook(){
		$oid  = I('get.oid');
		$id  = I('get.id');

        $list = M('pad_file')->where(['id'=>$id])->find();

        $re = M('pad_order')->where(['id'=>$list['o_id'],'uid'=>$_SESSION['user']['userId']])->count();

        if ($re>0) {
            $this->assign('list',$list);
        }
        $this->assign('oid',$oid);
		$this->display();
	}
	// public function wxList(){
		
	// 	$this->display();
	// }
	public function wxListdetails(){
		$id = I('get.id');
        $info = M('pad_order_info')->field('info,order_id')->where(['id'=>$id,'uid'=>$_SESSION['user']['userId']])->find();
        $list = json_decode(stripslashes($info['info']),true);
        $this->assign('oid',$info['order_id']);
        $this->assign('list',$list);
		$this->display();
	}















}