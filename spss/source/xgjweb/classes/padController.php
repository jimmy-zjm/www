<?php
/**
 * 平板报价系统
*/
require_once(WWW_DIR."/model/padModel.php");
require_once(WWW_DIR."/conf/mysql_db.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR . "/model/price_model.php");
class padController 
{
	private $tpl;
    public function __construct(){
       $this->tpl = get_smarty();
    }


	//选择系统分类
	public function index(){
		$this->check_login();
		//查询出所有系统分类
		$model = new padModel();
		$quoteCat = $model->quoteCat();
		$user = $model->user();
		// var_dump($user['system']);exit;
		$this->tpl->assign('system',explode('|', $user['system']));
		$this->tpl->assign('quoteCat',$quoteCat);
		$this->tpl->assign('setup',$this->selectSetUp());
		$this->tpl->assign('staff',$this->isDisplayStaff());
		$this->tpl->display('pad/index.html');
	}

	//输入报价信息页面 
	public function details($catId=null){
		$this->check_login();
		if (!empty($catId)) {
			$_GET['catId'] = $catId;
		}
		$array = explode(',', $_GET['catId']);
		
		$model    = new padModel();
		$userCity = $model->userCity();

		foreach ($array as $k => $v) {
			if(!preg_match("/^[0-9]+$/", $v)){
			  	$this->index();
			}
			$catId = $v;
			//查询出选择的分类下所有系统
			if (!in_array($catId,explode('|', $userCity['system']))) {
				echo "<script>alert('您没有权限查看此类系统！');history.go(-1);</script>";exit;
			}
			$catName = $model->getCatName($catId);

			$quoteList[$catName['cat_name']] = $model->quoteList($catId);
		}

		if (!empty($_SESSION['moneyList'])) {
			$moneyList = $_SESSION['moneyList'];
		    $build     = $_SESSION['pad_post']['build'];
		    unset($_SESSION['moneyList']);
		    $moneyAll['zhuCai'] = 0;
		    $moneyAll['fuCai']  = 0;
		    foreach ($moneyList as $k => $v) {
		    	$moneyAll['zhuCai'] += $v['money']['all']-$v['money']['install']-$v['money']['fuCai'];
		    	$moneyAll['fuCai']  += $v['money']['install']+$v['money']['fuCai'];
		    }
		    $this->tpl->assign('moneyAll',$moneyAll); 
		    $this->tpl->assign('build',$build); 
    		$this->tpl->assign('moneyList',$moneyList); 
		}
		
		$area     = $model->getPCD();
    	$this->tpl->assign('area',$area); 
		$this->tpl->assign('userCity',$userCity);
		$this->tpl->assign('quoteList',$quoteList);
		$this->tpl->assign('setup',$this->selectSetUp());
		$this->tpl->assign('staff',$this->isDisplayStaff());
		$this->tpl->display('pad/details.html');
	}

	public function sprov(){
		$model = new padModel();
		$userCity = $model->userCity();
		if ($userCity['service'][$_GET['prov']]['0']=='全部') {
			echo true;
		}else{
			echo $userCity['service'][$_GET['prov']]['0'];
		}
		
	}

	public function scity(){
		$model = new padModel();
		$userCity = $model->userCity();
		foreach ($userCity['service'][$_GET['prov']] as $k => $v) {
			echo "<li><a href='javascript:;' onclick='scity(this.innerHTML)'>".trim($v)."</a></li>";
		}
		
	}

	public function order(){
		$this->check_login();

		$model = new padModel();
		if (empty($_GET['checkId'])) {
			if (empty($_GET['c_id']) && empty($_GET['name']) && empty($_GET['money']) && empty($_GET['zprice'])) {
				echo '提交失败！';exit;
			}
			$re = $model->getCustomerQuote($_GET['name']);
			$customer = $model->getCustomer($_GET['c_id']);

			$addCustomerQuoteData = array(
				'u_id'       =>$_SESSION['pad_id'],
				'quote_id'   =>$_SESSION['pad_newList_id'],
				'c_id'       =>$_GET['c_id'],
				'name'       =>$_GET['name'],
				'price'      =>$_GET['money'],
				'zprice'     =>$_GET['zprice'],
				'province'   =>$customer['province'],
				'city'       =>$customer['city'],
				'district'   =>$customer['district'],
				'address'    =>$customer['address'],
				'type'       =>$customer['type'],
				'layout'     =>$customer['layout'],
				'total_area' =>$customer['total_area'],
				'area' 		 =>$customer['area'],
				'people' 	 =>$customer['people'],
				'build'		 =>$customer['build'],	
				'time'       =>time()
	        	);
			

			if (empty($re)) {
	        	$customerQuoteId = $model->addCustomerQuote($addCustomerQuoteData);
			}else{
				echo '该系统已提交,请勿重复提交';exit;
			}
		}else{
			$customerQuoteId = $_GET['checkId'];
			$re = $model->getPadOrder($customerQuoteId);
			if (!empty($re)) {
				echo '该系统已提交,请勿重复提交';exit;
			}
		}
		// !empty($_GET['checkId'])?$customerQuoteId = $_GET['checkId']:$customerQuoteId = '';

		
		$customerQuote = $model->getCustomerQuoteData($customerQuoteId);
		$customerData  = $model->getCustomer($customerQuote['c_id']);
		
		$catId  	   = $model->getFurnishQuote($customerQuote['quote_id']);

		if (empty($customerQuote)) {
			echo '提交失败';exit;
		}

		$quoteName['quote_name'] = $customerQuote['name'];
		$quoteName['quote_id']   = $customerQuote['quote_id'];
		$customerId				 = $customerQuote['c_id'];
		$quoteId       			 = $customerQuote['quote_id'];
		// $model       			 = $customerQuote['model'];
		$houseAreaAll    	 	 = $customerQuote['area'];
		$layout					 = explode(',', $customerQuote['layout']);

		$houseAreaArray = explode('|', $houseAreaAll);

		foreach ($houseAreaArray as $key => $value) {
			$houseArea[$key] = explode(',', $value);
		}

		$_SESSION['pad_post']['name']         = $customerData['name'];
		$_SESSION['pad_post']['tel']          = $customerData['tel'];
		$_SESSION['pad_post']['cho_Province'] = $customerQuote['province'];
		$_SESSION['pad_post']['cho_City']     = $customerQuote['city'];
		$_SESSION['pad_post']['cho_Area']     = $customerQuote['district'];
		$_SESSION['pad_post']['diqu']         = $customerQuote['address'];
		$_SESSION['pad_post']['house']        = $customerQuote['area'];
		$_SESSION['pad_post']['build']        = $customerQuote['build'];

		$_SESSION['pad_post']['type1'] = $layout['0'];
		$_SESSION['pad_post']['type2'] = $layout['1'];
		$_SESSION['pad_post']['type3'] = $layout['2'];
		$_SESSION['pad_post']['type4'] = $layout['3'];
		$_SESSION['pad_post']['type6'] = $layout['5'];
		$_SESSION['pad_post']['type7'] = $layout['6'];

		$_SESSION['pad_post']['bedroom']  = $houseArea['0'];
		$_SESSION['pad_post']['liveroom'] = $houseArea['1'];
		$_SESSION['pad_post']['kitchen']  = $houseArea['2'];
		$_SESSION['pad_post']['bathroom'] = $houseArea['3'];
		if($_SESSION['pad_post']['type6']>0) $_SESSION['pad_post']['gelou'] = $houseArea['5'];
		if($_SESSION['pad_post']['type7']>0) $_SESSION['pad_post']['database'] = $houseArea['6'];

		$build = $customerQuote['build'];
		
		$_GET['catId'] = $catId['cat_id'];
		$id            = $customerQuote['id'];
		$tableName     = 'pad_customer_quote';

		/*********************/
		//计算总面积
		$areaAll = 0;
		foreach ($houseArea as $k => $v) {
			if (is_array($v)) {
				$areaAll += array_sum($v);
			}
		}
		$this->tpl->assign('areaAll',$areaAll);
		$_SESSION['pad_post']['area'] = $areaAll;
		/*********************/

		$this->tpl->assign('houseArea',$houseArea);

		require_once(WWW_DIR . "/classes/priceController.php");
        $order = new priceController();

        if (is_array($quoteId)) {
        	foreach ($quoteId as $k => $v) {
	        	$lists = $order->get_price($v,$id,'1','',$tableName);
	        	if ($lists == 'error') {
		        	echo "抱歉！您的房间不支持该系统，请电话咨询或更换系统";exit;
		        }
		        $listsArray[$v] = $lists[2];
	        }
        }else{
        	$lists = $order->get_price($quoteId,$id,'1','',$tableName);
        	if ($lists == 'error') {
	        	echo "抱歉！您的房间不支持该系统，请电话咨询或更换系统";exit;
	        }
        }

        if (!empty($_POST['href']) && $_POST['href'] == 'details') {
        	foreach ($quoteNames as $k => $v) {
	        	$moneyList[$k]['name']  = $v['quote_name']; 
	        	$moneyList[$k]['q_id']  = $v['quote_id']; 
	        	$moneyList[$k]['money'] = $listsArray[$v['quote_id']]; 
	        }
	        $_SESSION['moneyList'] = $moneyList;
	        $this->details();exit;
        }

        $newLists = $lists['4'];
        
		$microtime            = explode('.',microtime(true));//转化时间戳 
		$data['info']         =addslashes(json_encode($newLists));
		$data['q_id']         =$customerQuoteId;
		$data['u_id']         =$_SESSION['pad_id'];
		$data['order_status'] ='1';
		$data['add_time']     =time();
		$data['sn']           = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);//订单编号
		
		$re = $model->addPadOrder($data);
		if ($re > 0) {
			echo '1';
		}else{
			echo '提交失败！';
		}
		
	}

	//产生报价页面
	public function offer(){

		$this->check_login();
		$model = new padModel();

		// if (!empty($_GET['val']) && !empty($_GET['os'])) {
		// 	$val = explode('|', $_GET['val']);
		// 	$value = '';
		// 	foreach ($val as $k => $v) {
		// 		if ($v=='除霾') {
		// 			$value .= $v;
		// 		}else if ($v!='') {
		// 			$value .= '+'.$v;
		// 		}
		// 	}
		// 	if ($val == '+') $val = '';
		// 	if ($_GET['os']==1) $val = '热回收新风'.$value;
		// 	else if ($_GET['os']==2) $val = '前置过滤'.$value;

		// 	$_POST = $_SESSION['pad_post'];

		// 	$quote = $model->getQuoteId($val);
		// 	$_POST['quote_id'] = $quote['quote_id'];
		// 	$ajax = true;
		// }

		if (!empty($_POST['cho_Province'])) $_POST['cho_Province'] 	= $model->getPCDName($_POST['cho_Province']);
		if (!empty($_POST['cho_City'])) 	$_POST['cho_City'] 		= $model->getPCDName($_POST['cho_City']);
		if (!empty($_POST['cho_Area'])) 	$_POST['cho_Area'] 		= $model->getPCDName($_POST['cho_Area']);

		if (empty($_GET['checkId'])) {

			if (!empty($_POST['q_id'])) {
				$_POST['quote_id'] = $_POST['q_id'];
			}

			if (!empty($_POST)) $_SESSION['pad_post']=$_POST;

			if (empty($_POST['quote_id'])) {
				echo "<script>alert('请选择报价产品');history.go(-1);</script>";exit;
			}

			if(!preg_match("/^1[34578]\d{9}$/", $_POST['tel'])){
			    echo "<script>alert('手机号码不正确');history.go(-1);</script>";exit;
			}

			if($_POST['cho_Area']=='请输入地区'){
			    echo "<script>alert('请输入地区');history.go(-1);</script>";exit;
			}

			if($_POST['diqu']=='请输入地址'){
			    echo "<script>alert('请输入地址');history.go(-1);</script>";exit;
			}
		

			/******************************************/
			//验证房屋信息是否正确
			if (empty($_POST['type1']) || empty($_POST['house'])) {
				echo "<script>alert('请选择房屋类型');history.go(-1);</script>";exit;
			}

			if ($_POST['house']==1) {
				unset($_POST['type6']);
				unset($_POST['type7']);
				unset($_POST['gelou']);
				unset($_POST['database']);
			}
			// echo '<pre>';
			// var_dump($_POST);exit;

			if (!empty($_POST['bedroom']) && empty($error)) {
				foreach ($_POST['bedroom'] as $k => $v) {
					if(!preg_match("/^[0-9]{0,}(.?[0-9]{1,2})?$/", $v) || empty($v)) $error = true;
				}
			}

			if (!empty($_POST['liveroom']) && empty($error)) {
				foreach ($_POST['liveroom'] as $k => $v) {
					if(!preg_match("/^[0-9]{0,}(.?[0-9]{1,2})?$/", $v) || empty($v)) $error = true;
				}
			}

			if (!empty($_POST['kitchen']) && empty($error)) {
				foreach ($_POST['kitchen'] as $k => $v) {
					if(!preg_match("/^[0-9]{0,}(.?[0-9]{1,2})?$/", $v) || empty($v)) $error = true;
				}
			}

			if (!empty($_POST['bathroom']) && empty($error)) {
				foreach ($_POST['bathroom'] as $k => $v) {
					if(!preg_match("/^[0-9]{0,}(.?[0-9]{1,2})?$/", $v) || empty($v)) $error = true;
				}
			}

			if (!empty($_POST['gelou']) && empty($error)) {
				foreach ($_POST['gelou'] as $k => $v) {
					if(!preg_match("/^[0-9]{0,}(.?[0-9]{1,2})?$/", $v) || empty($v)) $error = true;
				}
			}

			if (!empty($_POST['database']) && empty($error)) {
				foreach ($_POST['database'] as $k => $v) {
					if(!preg_match("/^[0-9]{0,}(.?[0-9]{1,2})?$/", $v) || empty($v)) $error = true;
				}
			}


			if(empty($_POST['people']) || !preg_match("/^[0-9]{0,}$/", $_POST['people'])) $error = true;

			if(empty($_POST['build']) || !preg_match("/^[0-9]{0,}(.?[0-9]{1,2})?$/", $_POST['build'])) $error = true;

			if (!empty($error) && $error == true) {
				echo "<script>alert('请认真填写面积！');history.go(-1);</script>";exit;
			}
			/******************************************/

			$build = $_POST['build'];

			//查询此客户是否存在
			$row = $model->getCustomerRow($_POST['name'],$_POST['tel']);
			//查询系统名称
			if ($_POST['href'] == 'details') {
				foreach ($_POST['quote_id'] as $key => $val) {
					$quoteNames[] = $model->getFurnishQuote($val);
				}
			}else{
				$quoteName = $model->getFurnishQuote($_POST['quote_id']);
			}
			/********************************************/
			$type1 = $_POST['type1'];
			/**********************************/
			//房屋布局
			!empty($_POST['type1'])?$_POST['type1'] = $_POST['type1']:$_POST['type1'] = '0';
			!empty($_POST['type2'])?$_POST['type2'] = $_POST['type2']:$_POST['type2'] = '0';
			!empty($_POST['type3'])?$_POST['type3'] = $_POST['type3']:$_POST['type3'] = '0';
			!empty($_POST['type4'])?$_POST['type4'] = $_POST['type4']:$_POST['type4'] = '0';
			!empty($_POST['type5'])?$_POST['type5'] = $_POST['type5']:$_POST['type5'] = '0';
			!empty($_POST['type6'])?$_POST['type6'] = $_POST['type6']:$_POST['type6'] = '0';
			!empty($_POST['type7'])?$_POST['type7'] = $_POST['type7']:$_POST['type7'] = '0';


			$layout = $_POST['type1'].','.$_POST['type2'].','.$_POST['type3'].','.$_POST['type4'].','.$_POST['type5'].','.$_POST['type6'].','.$_POST['type7'];
			/**********************************/

			

			/**********************************/
			//房屋面积
			!empty($_POST['bedroom'])? $bedroom  = implode(',', $_POST['bedroom']): $bedroom  ='0';
			!empty($_POST['liveroom'])?$liveroom = implode(',', $_POST['liveroom']):$liveroom ='0';
			!empty($_POST['kitchen'])? $kitchen  = implode(',', $_POST['kitchen']): $kitchen  ='0';
			!empty($_POST['bathroom'])?$bathroom = implode(',', $_POST['bathroom']):$bathroom ='0';
			!empty($_POST['balcony'])? $balcony  = implode(',', $_POST['balcony']): $balcony  ='0';
			!empty($_POST['gelou'])?   $gelou    = implode(',', $_POST['gelou']):   $gelou    ='0';
			!empty($_POST['database'])?$database = implode(',', $_POST['database']):$database ='0';

			$area = $bedroom.'|'.$liveroom.'|'.$kitchen.'|'.$bathroom.'|'.$balcony.'|'.$gelou.'|'.$database;
			/**********************************/

			$addData = array(
				'u_id'          => $_SESSION['pad_id'],		//用户ID
				'name'          => $_POST['name'],			//客户姓名
				'tel'           => $_POST['tel'],			//客户电话
				'province'      => $_POST['cho_Province'],	//省
				'city'          => $_POST['cho_City'],		//市
				'district'      => $_POST['cho_Area'],		//县/区
				'address'       => $_POST['diqu'],			//具体地址
				'type'    		=> $_POST['house'],			//房屋类型 1 公寓 2叠加 3联排 4独董
				'layout'  		=> $layout,					//房屋布局
				'total_area'    => $_POST['area'],			//房屋总面积
				'area'     		=> $area,					//房屋面积
				'people'		=> $_POST['people'],		//常住人口
				'build'			=> $build,					//建筑面积
				'time'          => time(),					//创建时间
				);


			//如果该客户数据不存在，添加该用户
			if (empty($row)) {
				$customerId = $model->addCustomer($addData);
				if (empty($customerId)) $error = true;
			}else{
				$customerId = $row['id'];
				$re = $model->updateCustomer($addData,$customerId);
				if (empty($re)) $error = true;
			}

			if (!empty($error)) {
				echo "<script>alert('房屋信息保存失败，请重试！');history.go(-1);</script>";exit;
			}

			$_SESSION['pad_post']=$_POST;
			$_SESSION['pad_post']['type1'] = $type1;

			$quoteId = $_POST['quote_id'];

			!empty($_POST['bedroom'])? $houseArea['0'] = $_POST['bedroom'] :$houseArea['0'] = '0';
			!empty($_POST['liveroom'])?$houseArea['1'] = $_POST['liveroom']:$houseArea['1'] = '0';
			!empty($_POST['kitchen'])? $houseArea['2'] = $_POST['kitchen'] :$houseArea['2'] = '0';
			!empty($_POST['bathroom'])?$houseArea['3'] = $_POST['bathroom']:$houseArea['3'] = '0';
			!empty($_POST['balcony'])? $houseArea['4'] = $_POST['balcony'] :$houseArea['4'] = '0';
			!empty($_POST['gelou'])?   $houseArea['5'] = $_POST['gelou']   :$houseArea['5'] = '0';
			!empty($_POST['database'])?$houseArea['6'] = $_POST['database']:$houseArea['6'] = '0';
			$_SESSION['padHouseArea'] = $houseArea;

			$id = $customerId;
			$tableName = 'pad_customer';
			/********************************************/
		}else{

			$customerQuote = $model->getCustomerQuoteData($_GET['checkId']);

			$customerData  = $model->getCustomer($customerQuote['c_id']);
			
			$catId  	   = $model->getFurnishQuote($customerQuote['quote_id']);
			
			if (empty($customerQuote)) {
				echo "<script>alert('没有您要查询的数据');history.go(-1);</script>";exit;
			}

			$quoteName['quote_name'] = $customerQuote['name'];
			$quoteName['quote_id']   = $customerQuote['quote_id'];
			$customerId				 = $customerQuote['c_id'];
			$quoteId       			 = $customerQuote['quote_id'];
			// $model       			 = $customerQuote['model'];
			$houseAreaAll    	 	 = $customerQuote['area'];
			$layout					 = explode(',', $customerQuote['layout']);

			$houseAreaArray = explode('|', $houseAreaAll);

			foreach ($houseAreaArray as $key => $value) {
				$houseArea[$key] = explode(',', $value);
			}

			$_SESSION['pad_post']['name']         = $customerData['name'];
			$_SESSION['pad_post']['tel']          = $customerData['tel'];
			$_SESSION['pad_post']['cho_Province'] = $customerQuote['province'];
			$_SESSION['pad_post']['cho_City']     = $customerQuote['city'];
			$_SESSION['pad_post']['cho_Area']     = $customerQuote['district'];
			$_SESSION['pad_post']['diqu']         = $customerQuote['address'];
			$_SESSION['pad_post']['house']        = $customerQuote['area'];
			$_SESSION['pad_post']['build']        = $customerQuote['build'];

			$_SESSION['pad_post']['type1'] = $layout['0'];
			$_SESSION['pad_post']['type2'] = $layout['1'];
			$_SESSION['pad_post']['type3'] = $layout['2'];
			$_SESSION['pad_post']['type4'] = $layout['3'];
			$_SESSION['pad_post']['type6'] = $layout['5'];
			$_SESSION['pad_post']['type7'] = $layout['6'];

			$_SESSION['pad_post']['bedroom']  = $houseArea['0'];
			$_SESSION['pad_post']['liveroom'] = $houseArea['1'];
			$_SESSION['pad_post']['kitchen']  = $houseArea['2'];
			$_SESSION['pad_post']['bathroom'] = $houseArea['3'];
			if($_SESSION['pad_post']['type6']>0) $_SESSION['pad_post']['gelou'] = $houseArea['5'];
			if($_SESSION['pad_post']['type7']>0) $_SESSION['pad_post']['database'] = $houseArea['6'];

			$this->tpl->assign('cqid',$_GET['checkId']);
			$build = $customerQuote['build'];
			

			$_GET['catId'] = $catId['cat_id'];
			$id            = $customerQuote['id'];
			$tableName     = 'pad_customer_quote';

			$order = $model->getPadOrder($_GET['checkId']);
			$this->tpl->assign('order',$order);
		}


		/*********************/
		//计算总面积
		$areaAll = 0;
		foreach ($houseArea as $k => $v) {
			if (is_array($v)) {
				$areaAll += array_sum($v);
			}
		}
		$this->tpl->assign('areaAll',$areaAll);
		$_SESSION['pad_post']['area'] = $areaAll;
		/*********************/

		$this->tpl->assign('houseArea',$houseArea);

		!empty($_GET['checkId'])?$customerQuoteId = $_GET['checkId']:$customerQuoteId = '';

		require_once(WWW_DIR . "/classes/priceController.php");
        $order = new priceController();

        if (is_array($quoteId)) {
        	foreach ($quoteId as $k => $v) {
	        	$lists = $order->get_price($v,$id,'1','',$tableName);
	        	if ($lists == 'error') {
		        	echo "<script>alert('抱歉！您的房间不支持该系统，请电话咨询或更换系统');history.go(-1);</script>";exit;
		        }
		        $listsArray[$v] = $lists[2];
	        }
        }else{
        	$lists = $order->get_price($quoteId,$id,'1','',$tableName);
        	if ($lists == 'error') {
	        	echo "<script>alert('抱歉！您的房间不支持该系统，请电话咨询或更换系统');history.go(-1);</script>";exit;
	        }
        }

        if (!empty($_POST['href']) && $_POST['href'] == 'details') {
        	foreach ($quoteNames as $k => $v) {
	        	$moneyList[$k]['name']  = $v['quote_name']; 
	        	$moneyList[$k]['q_id']  = $v['quote_id']; 
	        	$moneyList[$k]['money'] = $listsArray[$v['quote_id']]; 
	        }
	        $_SESSION['moneyList'] = $moneyList;
	        $catId = $_POST['cid'];
	        $this->details($catId);exit;
        }

        $newLists = $lists['4'];
        // $list=json_encode($newLists);
        // $aa=json_decode($list);
        // var_dump($aa);die;
        $_SESSION['pad_newList']  = $newLists;
        $_SESSION['pad_newMoney'] = $lists['2'];
        
        $_SESSION['pad_newList_name'] = $quoteName['quote_name'];
        $_SESSION['pad_newList_id'] = $quoteName['quote_id'];

        $this->tpl->assign('build',$build);
 		$this->tpl->assign('type',1);
    	$this->tpl->assign('setup',$this->selectSetUp());
        $this->tpl->assign('staff',$this->isDisplayStaff());
        $this->tpl->assign('quoteId',$quoteName['quote_id']);
        $this->tpl->assign('customerId',$customerId);
        $this->tpl->assign('quoteName',$quoteName['quote_name']);
        $this->tpl->assign('list',$newLists);
        $this->tpl->assign('moneyAll',$lists['2']);
        $this->tpl->assign('count',count($newLists));
		$this->tpl->display('pad/offer.html');
	    
	}

	//导出
	public function gotos(){

		$this->check_login();
		$model = new padModel();
		if (!empty($_GET['cqid']) && preg_match("/^[0-9]+$/", $_GET['cqid'])){
			$row = $model->getCustomerQuoteData($_GET['cqid']);
			if (empty($row)) {
				echo "<SCRIPT type='text/javascript'>alert('该报价不存在!!!');history.back();</SCRIPT>";exit;
			}
			$rows = $model->getCustomer($row['c_id']);
		}

		if (!empty($rows)) {
			$name            =$rows['name'];
			$tel             =$rows['tel'];
			$quote_id        =$row['quote_id'];
			$customerQuoteId =$_GET['cqid'];
			$customerId 	 =$rows['id'];
			$table 			 ='pad_customer_quote';
			$id 			 =$_GET['cqid'];
		}else{
			$name            =empty($_GET['name'])?$_SESSION['pad_post']['name']:$_GET['name'];
			$tel             =empty($_GET['tel'])?$_SESSION['pad_post']['tel']:$_GET['tel'];
			$quote_id        =empty($_GET['quote_id'])?$_SESSION['pad_post']['quote_id']:$_GET['quote_id'];
			$customerQuoteId =empty($_GET['cqid'])?'':$_GET['cqid'];
			$row = $model->getCustomerRow($name,$tel);
			$customerId 	 = $row['id'];
			$table 			 ='pad_customer';
			$id 			 = $row['id'];
		}
		
		if (empty($name) || empty($tel)) {
			echo "<SCRIPT type='text/javascript'>alert('该报价不存在!!!');history.back();</SCRIPT>";exit;
		}

		require_once(WWW_DIR . "/classes/priceController.php");
        $order = new priceController();
        $lists = $order->get_price($quote_id,$id,'1',true,$table);
	}

	//发送邮件
	public function email(){

		if(!preg_match("/^[0-9a-z_][_.0-9a-z-]{0,32}@([0-9a-z][0-9a-z-]{0,32}\.){1,4}[a-z]{2,4}$/i", $_GET['mail'])){
		    echo '请正确填写联系邮箱！';exit;
		}
		$model = new padModel();
		$customerQuoteId = trim($_GET['customerQuoteId']);

		/******************************************/
		//列表页发送邮件
		$name     = empty($_GET['name'])?'':$_GET['name'];
		$tel      = empty($_GET['tel'])?'':$_GET['tel'];
		$quote_id = empty($_GET['quote_id'])?'':$_GET['quote_id'];

		if (!empty($name) && !empty($tel) && !empty($quote_id)) {
			$row = $model->getCustomerRow($name,$tel);
			$customerId = $row['id'];
			require_once(WWW_DIR . "/classes/priceController.php");

	        $order = new priceController();
	        $lists = $order->get_price($quote_id,$customerQuoteId,'1','','pad_customer_quote');
	       
	        $_SESSION['pad_newList'] = $lists['4'];

	        $_SESSION['pad_newMoney'] = $lists['2'];

    		$furnishQuote = $model->getFurnishQuote($quote_id);
    		$_SESSION['pad_newList_name'] = $furnishQuote['quote_name'];

		}

		/******************************************/
		$setup = $this->selectSetUp();
		//图片 品牌 名称 产地 型号 单价
		$email[] = $_GET['mail'];
		require_once(WWW_DIR . "/libs/mail/mail.php");
		$mail = new Mail();
		$body = '';

		$quoteData  = $_SESSION['pad_newList'];
		$quoteMoney = $_SESSION['pad_newMoney'];

    	if ($customerQuoteId) {
    		$customerQuote = $model->getCustomerQuoteData($customerQuoteId);
			$houseAreaArray = explode('|', $customerQuote['area']);
			foreach ($houseAreaArray as $key => $value) {
				$houseArea[$key] = explode(',', $value);
			}
    	}else{
    		$houseArea = $_SESSION['padHouseArea'];
    	}

    	if (!empty($quoteData)) {
    		$house = '';
			$body .= '<table style="border:1px solid black;">';
			$body .= '<tr ><th style="border:1px solid black; text-align: center;"  colspan="7"><b>'.$_SESSION['pad_newList_name'].'</b></th></tr>';
    		foreach ($houseArea as $key => $val) {
    			if ($key=='0') $name = '卧室';
    			else if ($key=='1') $name = '客厅';
    			else if ($key=='2') $name = '厨房';
    			else if ($key=='3') $name = '卫生间';
    			else if ($key=='5') $name = '阁楼';
    			else if ($key=='6') $name = '地下室';
    			if (!empty($houseArea[$key]['0'])) {
    				foreach ($houseArea[$key] as $k => $v) {
    					$house .= $name.($k+1).':'.$v.'㎡ ';
    				}
    			}
    		}
    		$body .= '<tr ><th style="border:1px solid black; text-align: left;"  colspan="7"><b>'.$house.'</th></tr>';
			$body .= '<tr>';
			if(!empty($setup['2'])) $body .= '<td style="border:1px solid black;">名称</td>';
			$body .= '<td style="border:1px solid black;">产品编码</td>';
			if(!empty($setup['4'])) $body .= '<td style="border:1px solid black;">型号</td>';
			if(!empty($setup['1'])) $body .= '<td style="border:1px solid black;">品牌</td>';
			if(!empty($setup['3'])) $body .= '<td style="border:1px solid black;">产地</td>';
			if(!empty($setup['6'])) $body .= '<td style="border:1px solid black;">数量</td>';
			if(!empty($setup['5'])) $body .= '<td style="border:1px solid black;">价格</td>';
			$body .= '</tr>';
			foreach ($quoteData as $k => $v) {
				$body .= '<tr>';
				if(!empty($setup['2'])) $body .= '<td style="border:1px solid black;">'.$v['name'].'</td>';
				$body .= '<td style="border:1px solid black;">'.$v['sn'].'</td>';
				if(!empty($setup['4'])) $body .= '<td style="border:1px solid black;">'.$v['model'].'</td>';
				if(!empty($setup['1'])) $body .= '<td style="border:1px solid black;">'.$v['brand'].'</td>';
				if(!empty($setup['3'])) $body .= '<td style="border:1px solid black;">'.$v['origin'].'</td>';
				if(!empty($setup['6'])) $body .= '<td style="border:1px solid black;">'.$v['num'].'</td>';
				if(!empty($setup['5'])) $body .= '<td style="border:1px solid black;">'.$v['money'].'</td>';
				$body .= '</tr>';
			}
			$body .= '<tr>';
			$body .= '<td style="border:1px solid black;">辅材价格</td>';
			$body .= '<td style="border:1px solid black;text-align:right;color:red;" >'.ceil($quoteMoney['fuCai']).'.00</td>';
			$body .= '<td style="border:1px solid black;text-align:right;" colspan="5">&nbsp;</td>';
			$body .= '</tr>';
			$body .= '<tr>';
			$body .= '<td style="border:1px solid black;">总价</td>';
			$body .= '<td style="border:1px solid black;text-align:right;color:red;" >'.ceil($quoteMoney['all']).'.00</td>';
			$body .= '<td style="border:1px solid black;text-align:right;" colspan="5">&nbsp;</td>';
			$body .= '</tr>';
			$body .= '</table><br><br><br>';
		}
		$to = $email; //收件人  
		$subject = "上品上生"; //主题  
		if (Mail::send($subject,$body,$to)) {
		    echo "发送成功!";exit;
		}else{
		    echo "发送失败!";exit;
		} 
	}
	
	
	public function selectHouse(){
		if(empty($_GET['name']) || empty($_GET['tel']) || !preg_match("/^1[34578]\d{9}$/", $_GET['tel'])) exit;
		$model = new padModel();
		$data = $model->getCustomerRow($_GET['name'],$_GET['tel']);
		$data['province']     = $model->getPCDId($data['province'],1);
		$data['city']         = $model->getPCDId($data['city']);
		$data['district']     = $model->getPCDId($data['district']);
		$data['cityList']     = $model->getPCDFind($data['province']);
		$data['districtList'] = $model->getPCDFind($data['city']);
		echo json_encode($data);
	}

	//添加客户已查询系统信息意向表
	public function addCustomerQuote(){

		$model = new padModel();

		$re = $model->getCustomerQuote($_GET['name']);
		$customer = $model->getCustomer($_GET['c_id']);

		$addCustomerQuoteData = array(
			'u_id'       =>$_SESSION['pad_id'],
			'quote_id'   =>$_SESSION['pad_newList_id'],
			'c_id'       =>$_GET['c_id'],
			'name'       =>$_GET['name'],
			'price'      =>$_GET['money'],
			'zprice'     =>$_GET['zprice'],
			'province'   =>$customer['province'],
			'city'       =>$customer['city'],
			'district'   =>$customer['district'],
			'address'    =>$customer['address'],
			'type'       =>$customer['type'],
			'layout'     =>$customer['layout'],
			'total_area' =>$customer['total_area'],
			'area' 		 =>$customer['area'],
			'people' 	 =>$customer['people'],
			'build'		 =>$customer['build'],	
			'time'       =>time()
        	);
		

		if (empty($re)) {
        	$result = $model->addCustomerQuote($addCustomerQuoteData);
		}else{
			$result = $model->editCustomerQuote($addCustomerQuoteData,$re['id']);
		}

		echo $result;
        
	}

	public function selectSetUp(){
		$model = new padModel();
		$row = $model->selectSetUp();
		$return = explode('|', $row['setup']);
		return $return;
	}

	public function isDisplayStaff(){
		$model = new padModel();
		$row = $model->userCity();
		$return = $row['pid'];
		return $return;
	}

	public function setup(){
		if ($_GET['t1']=='true') $t1 = '1';
		else $t1 = '0';
		if ($_GET['t2']=='true') $t2 = '1';
		else $t2 = '0';
		if ($_GET['t3']=='true') $t3 = '1';
		else $t3 = '0';
		if ($_GET['t4']=='true') $t4 = '1';
		else $t4 = '0';
		if ($_GET['t5']=='true') $t5 = '1';
		else $t5 = '0';
		if ($_GET['t6']=='true') $t6 = '1';
		else $t6 = '0';
		if ($_GET['t7']=='true') $t7 = '1';
		else $t7 = '0';
		$t['setup'] = $t1.'|'.$t2.'|'.$t3.'|'.$t4.'|'.$t5.'|'.$t6.'|'.$t7;
		$model = new padModel();
		$row = $model->setup($t);
		echo $row;
	}


	//登录
	public function login(){
		if (!empty($_SESSION['pad_id'])) {
			header("Location:pad.php?index");exit;
		}
		
		if(!empty($_COOKIE['auto_name']) && !empty($_COOKIE['auto_psd']) ){
	        $name=$_COOKIE['auto_name'];
	        $psd=$_COOKIE['auto_psd'];
	        $db=new db();
	        $sql="select * from pad_user where name='{$name}' and psd='{$psd}' and is_open='1' and is_use ='1' limit 1";
	        $res=$db->getRow($sql);
            if($res['auto_login']=='1cok'){
            	if($res['is_try']==1 && $res['start_time'] > time()){
            		$_SESSION['pad_id']=$res['id'];
	                $_SESSION['pad_user']=$res['name'];
	                if($db->update('pad_user',array('is_use'=>'0','is_try'=>1),"id={$_SESSION['pad_id']}") != false){
	                	echo "<SCRIPT type='text/javascript'>alert('您的账号试用时间已到，请联系管理员!!!');history.back();</SCRIPT>";
						header("Location:pad.php?login");exit;
					}else{
						echo "<SCRIPT type='text/javascript'>alert('登录状态错误!!!');history.back();</SCRIPT>";exit;
					}
            	}elseif($res['is_try']==1 && time() > $res['end_time']){
            		$_SESSION['pad_id']=$res['id'];
	                $_SESSION['pad_user']=$res['name'];
	                if($db->update('pad_user',array('is_use'=>'0','is_try'=>2),"id={$_SESSION['pad_id']}") != false){
	                	echo "<SCRIPT type='text/javascript'>alert('您的账号试用时间已到，请联系管理员!!!');history.back();</SCRIPT>";
						header("Location:pad.php?login");exit;
					}else{
						echo "<SCRIPT type='text/javascript'>alert('登录状态错误!!!');history.back();</SCRIPT>";exit;
					}
            	}else{
            		$_SESSION['pad_id']=$res['id'];
	                $_SESSION['pad_user']=$res['name'];
	                if($db->update('pad_user',array('sessionid'=>session_id()),"id={$_SESSION['pad_id']}") != false){
						header("Location:pad.php?index");exit;
					}else{
						echo "<SCRIPT type='text/javascript'>alert('登录状态错误!!!');history.back();</SCRIPT>";exit;
					}
            	}
            }
        }
        $this->tpl->display('pad/login.html');
	}

	/*
    验证登陆
     */
    public function doLogin(){
    	if(empty($_POST['name']) || empty($_POST['psd'])){
    		echo '2';
    	}else{
    		$auto=isset($_POST['auto'])?$_POST['auto']:'';
    		$name=$_POST['name'];
	    	$psd=md5($_POST['psd'].MD5_PAD_PSD);
	        $padOb=new padModel();
	        $padOb->check_login($name,$psd,$auto);
    	}
    }


    //修改个人信息
	public function info(){
		$this->check_login();

		$model = new padModel();
		$user = $model->user();	
		
		if ($user['pid']!='0') {
			$this->psd();exit;
		}

		$user['n'] = substr($user['birthday'], 0,4);
		$user['y'] = substr($user['birthday'], 4,2);
		$user['r'] = substr($user['birthday'], 6,2);

		$this->tpl->assign('user',$user);

		$this->tpl->assign('setup',$this->selectSetUp());
		$this->tpl->assign('staff',$this->isDisplayStaff());
		$this->tpl->display('pad/info.html');
	}

	//修改个人信息
	public function doInfo(){
		$this->check_login();
		$db=new db();
		$data=array(
				'real_name'=>trim($_POST['real_name']),
				'number'=>trim($_POST['number']),
				'email'=>trim($_POST['email']),
				'tel'=>intval($_POST['tel']),
				'birthday'=>str_replace(array("-"),"",trim($_POST['birthday'])),
				'sex'=>intval($_POST['sex']),
				'add_time'=>intval(time()),
		);
		if($db->update('pad_user',$data,"id={$_SESSION['pad_id']}") != false){
			echo "1";exit;
		}else{
			echo "2";exit;
		}
	}
	//修改密码
	public function psd(){
		$this->check_login();
		$this->tpl->assign('setup',$this->selectSetUp());
		$this->tpl->assign('staff',$this->isDisplayStaff());
		$this->tpl->display('pad/psd.html');
	}
	//修改密码
	public function doPsd(){
		$this->check_login();
		$db=new db();
		$res=$db->getOne("select psd from pad_user where id={$_SESSION['pad_id']}");
		if(md5(trim($_POST['oldpsd']).MD5_PAD_PSD)==$res){
			if(trim($_POST['newpsd']) != trim($_POST['newpsd1'])){
				echo '4';exit;
			}
			$data=array(
				'spsd'=>trim($_POST['newpsd']),
				'psd'=>md5(trim($_POST['newpsd']).MD5_PAD_PSD),
			);
			if($db->update('pad_user',$data,"id={$_SESSION['pad_id']}") != false){
				echo "1";exit;
			}else{
				echo "2";exit;
			}
		}else{
			echo '3';exit;
		}
		
		
	}

	//客户管理
	public function manage(){
		$this->check_login();
		$name=!empty($_GET['name'])?$_GET['name']:'';
		$tel=!empty($_GET['tel'])?$_GET['tel']:'';	
        $this->tpl->assign('setup',$this->selectSetUp());
        $this->tpl->assign('staff',$this->isDisplayStaff());
       	$this->tpl->assign('name',$name);
       	$this->tpl->assign('tel',$tel);
		$this->tpl->display('pad/manage.html');
	}

	//客户管理列表分页
	public function managePage(){
		$this->check_login();
		$db=new db();
		$where="u_id = {$_SESSION['pad_id']} ";
		if(!empty($_GET['name'])){
			$where.=" and name = '$_GET[name]' ";
		}
		if(!empty($_GET['tel'])){
			$where.=" and tel = '$_GET[tel]' ";
		}

        $total              = $db->getOne("select count(*) from pad_customer where $where");
        $page               = getAjaxPage($total, 10);
        $data['list']  = $db->getAll("select * from pad_customer where $where order by time desc LIMIT {$page['limit']}");
        foreach ($data['list']  as $k => &$v) {
        	$v['time']=date('Y-m-d H:i:s',$v['time']);
        }
        $data['total']      = $total;
        $data['total_page'] = $page['total_page'];
        echo json_encode($data);die;
	}

	//客户管理查看
	public function check(){
		$this->check_login();
		$db=new db();
		if(!isset($_GET['p'])){
            $p = 1;
        }else{
            $p = $_GET['p'];
        }

        if(!preg_match("/^[0-9]+$/", $_GET['c_id']) || $_GET['c_id']=='0'){
		   echo "<SCRIPT type='text/javascript'>alert('该客户不存在!!!');history.back();</SCRIPT>";exit;
		}
		$c_id=$_GET['c_id'];

		$addr = $db->getRow("select * from pad_customer where id=$c_id");

		if (empty($addr) || $addr['u_id']!=$_SESSION['pad_id']) {
			if (!empty($addr)) $user = $db->getRow("select * from pad_user where id='{$addr['u_id']}'");
			if (empty($addr) || $user['pid']!=$_SESSION['pad_id']) {
				echo "<SCRIPT type='text/javascript'>alert('该客户不存在!!!');history.back();</SCRIPT>";exit;
			}
		}
		

		$total= $db->getOne("select count(*) from pad_customer_quote where c_id=$c_id");
		//var_dump($total);exit;
		$page               = getAjaxPage($total, 10);
		$data['list']= $db->getAll("select * from pad_customer_quote where c_id=$c_id LIMIT {$page['limit']}");
		foreach ($data['list']  as $k => &$v) {

        	$v['time']=date('Y-m-d H:i:s',$v['time']);
        	$v['tijiao'] = $db->getOne("select count(*) from pad_order where q_id='{$v['id']}'");
        }

		$data['total']      = $total;
		$data['p']      = $p;
        $data['total_page'] = $page['total_page'];

		$this->tpl->assign('setup',$this->selectSetUp());
		$this->tpl->assign('staff',$this->isDisplayStaff());
		// $this->tpl->assign('area',$area);
		$this->tpl->assign('data',$data);
		$this->tpl->assign('addr',$addr);
		$this->tpl->assign('c_id',$c_id);
		$this->tpl->display('pad/check.html');
	}
	public function staffinfos(){
		$this->check_login();
		$db=new db();
		$id=$_GET['id'];
		$data['list']= $db->getAll("select r.*,u.name ,u.real_name from pad_remark r join pad_user u on r.u_id=u.id where r.c_id=$id order by r.addtime desc");
		/*foreach ($data['list']  as $key => &$val) {
        	$val['addtime']=date('m-d H:i',$val['addtime']);
        }*/
		$html="<div class='pad-checkbox-clickAddId-center-bk'> 
        <div class='pad-checkbox-clickAddId-center'>
            <div class='pad-checkbox-clickAddId-center-list-bk'>
                <div class='pad-checkbox-clickAddId-center-title'>
					<div class='pad-checkbox-clickAddId-center-title-left'>
						<span class='span01'>
							修改日志
						</span>
					</div>
					
					<div class='pad-checkbox-clickAddId-center-title-right' id='CkOutId01' onclick='clo()'>
						<a href='javascript:;'>
							<img src='images/cha03.png'/>
						</a>
					</div>
				</div>
                
				<div class='clear2'></div>
				
                <div class='pad-checkbox-clickAddId-center-list'>";
		foreach ($data['list'] as $k => $v) {
			$html.="<div class='pad-checkbox-clickAddId-center-demo'>
                        <div class='pad-checkbox-clickAddId-center-demo-01'>
                            <div class='pad-checkbox-clickAddId-center-demo-01-name'>
                                {$v['u_name']}更新了备注
                            </div>
                            
                            <div class='pad-checkbox-clickAddId-center-demo-01-time'>"
                                .date('m-d H:i',$v['addtime']).
                          " </div>
                            
                            <div class='clear'></div>
                        </div>
                        
                        <div class='clear'></div>
                        
                        <div class='pad-checkbox-clickAddId-center-demo-02'>
                            <div class='pad-checkbox-clickAddId-center-demo-02-word'>
                                {$v['content']}
                            </div>
                        </div>
                    </div>
                    
                    <div class='clear27'></div>";
		}    
               $html.="</div>
			   
			   	<div class='clear2'></div>
            </div>
        </div>
        
        <div class='clear'></div>
    </div>" ;
    echo $html;die;

	}
	//客户管理查看备注信息
	public function checkinfo(){
		$this->check_login();
		$db=new db();
		$id=$_GET['id'];
		$data['list']= $db->getAll("select r.*,u.name ,u.real_name from pad_remark r join pad_user u on r.u_id=u.id where r.c_id=$id order by r.addtime desc");
		/*foreach ($data['list']  as $key => &$val) {
        	$val['addtime']=date('m-d H:i',$val['addtime']);
        }*/
		$html="<div class='pad-checkbox-clickAddId-center-bk'> 
        <div class='pad-checkbox-clickAddId-center'>
            <div class='pad-checkbox-clickAddId-center-title'>
                <div class='pad-checkbox-clickAddId-center-title-left'>
                    <span class='span01'>
                        查看备注
                    </span>
                </div>
                
                <div class='pad-checkbox-clickAddId-center-title-right' id='CkOutId01' onclick='clo()'>
                    <a href='javascript:;'>
                        <img src='images/cha03.png'/>
                    </a>
                </div>
            </div>
            
            <div class='clear2'></div>
            <form action='#' method='post' >
            <div class='pad-checkbox-check-center-form'>
            	<div class='pad-checkbox-check-center-form-textarea'>
                	<textarea  name='content' id='content'></textarea>
                </div>
            </div>
            
            <div class='clear36'></div>

            <input type='hidden' name='c_id' id='c_id' value='{$id}'/>

            <div class='pad-checkbox-clickAddId-center-button'>
                <div class='pad-checkbox-clickAddId-center-button-left'>
                	<input type='button' onclick='clo()' value='取消'/>
                	
                </div>
                
                <div class='pad-checkbox-clickAddId-center-button-right'>
                	<input type='button' onclick='sub()' value='保存'/>
                </div>
                
                <div class='clear'></div>
            </div>
            
            <div class='clear2'></div>            
            </form>
            <div class='pad-checkbox-clickAddId-center-list-bk'>
                <div class='pad-checkbox-clickAddId-center-title'>
                    <div class='pad-checkbox-clickAddId-center-title-left'>
                        <span class='span01'>
                            修改日志
                        </span>
                    </div>
                </div>
                
                <div class='pad-checkbox-clickAddId-center-list'>";
		foreach ($data['list'] as $k => $v) {
			$html.="<div class='pad-checkbox-clickAddId-center-demo'>
                        <div class='pad-checkbox-clickAddId-center-demo-01'>
                            <div class='pad-checkbox-clickAddId-center-demo-01-name'>
                                {$v['u_name']}更新了备注
                            </div>
                            
                            <div class='pad-checkbox-clickAddId-center-demo-01-time'>"
                                .date('m-d H:i',$v['addtime']).
                          " </div>
                            
                            <div class='clear'></div>
                        </div>
                        
                        <div class='clear'></div>
                        
                        <div class='pad-checkbox-clickAddId-center-demo-02'>
                            <div class='pad-checkbox-clickAddId-center-demo-02-word'>
                                {$v['content']}
                            </div>
                        </div>
                    </div>
                    
                    <div class='clear27'></div>";
		}    
               $html.="</div>
			   
			   <div class='clear2'></div>
            </div>
        </div>
        
        <div class='clear'></div>
    </div>" ;
    echo $html;die;

	}

	//客户管理查看
	public function addRemark(){
		$this->check_login();
		//var_dump($_POST);exit;
		$db=new db();
		$name=$db->getOne("select real_name from pad_user where id={$_SESSION['pad_id']}");
		$data['u_id']=$_SESSION['pad_id'];
		$data['u_name']=$name;
		$data['c_id']=$_POST['c_id'];
		$data['content']=$_POST['content'];
		$data['addtime']=time();
		$re=$db->add('pad_remark',$data);
		if($re){
			echo 1;die;
		}else{
			echo 2;die;
		}
	}

	//客户管理查看
	public function checkPage(){
		$this->check_login();

		$db=new db();
		$c_id=$_GET['c_id'];
		$total= $db->getOne("select count(*) from pad_customer_quote where c_id=$c_id");
		$page               = getAjaxPage($total, 10);

		$data['list']= $db->getAll("select * from pad_customer_quote where c_id=$c_id LIMIT {$page['limit']}");

		$data['addr'] = $db->getOne("select * from pad_customer where id=$c_id and u_id = {$_SESSION['pad_id']} ");
		echo json_encode($data);die;
	}
	
	//检测是否登录
	public function check_login(){
		$db=new db();
		if (!empty($_SESSION['pad_id'])) {
        	$model = new padModel();
			$info=$model->user();
			if(session_id()!==$info['sessionid']){
				session_destroy();
				setcookie("auto_name","", time()-3600,'/');
				setcookie("auto_psd", "", time()-3600,'/');
				header("Location:pad.php?login");exit;
			}
        }else{
        	header("Location:pad.php?login");exit;
        }
       
	}

	//退出
	public function goOut(){
		$this->check_login();
		$db=new db();
		if($db->update('pad_user',array('sessionid'=>0),"id={$_SESSION['pad_id']}") != false){
			session_destroy();
			setcookie("auto_name","", time()-3600,'/');
			setcookie("auto_psd", "", time()-3600,'/');
			header("Location:pad.php?login");exit;
		}else{
			$db->update('pad_user',array('sessionid'=>0),"id={$_SESSION['pad_id']}");
			session_destroy();
			setcookie("auto_name","", time()-3600,'/');
			setcookie("auto_psd", "", time()-3600,'/');
			header("Location:pad.php?login");exit;
		}
	}

	//员工管理
	public function staff(){
		$this->check_login();
		$model = new padModel();
		$list = $model->staffList();
		$this->tpl->assign('list',$list);
		$this->tpl->assign('setup',$this->selectSetUp());
        $this->tpl->assign('staff',$this->isDisplayStaff());
		$this->tpl->display('pad/staff.html');
	}

	//设置用户子账号是否开启
	public function userOpen(){
		$this->check_login();
		$font = $_GET['font'];
		$id   = $_GET['id'];
		if (!empty($font) && $font=='启用' || !empty($font) && $font=='停用') {
			$model = new padModel();
			$save = $model->updateUserOpen($id,$font);
			echo $save;
		}
	}

	//员工信息
	public function staffInfo(){
		$this->check_login();
		if(empty($_GET['id']) || !preg_match("/^[0-9]+$/", $_GET['id'])){
		   echo "<SCRIPT type='text/javascript'>alert('该用户不存在!!!');history.back();</SCRIPT>";exit;
		}
		$id = $_GET['id'];
		$model = new padModel();
		$data = $model->staffInfo($id);

		$this->tpl->assign('data',$data);
		$this->tpl->assign('setup',$this->selectSetUp());
        $this->tpl->assign('staff',$this->isDisplayStaff());
		$this->tpl->display('pad/staffInfo.html');
	}

	//员工信息查看
	public function staffCheck(){
		$this->check_login();
		$this->tpl->assign('setup',$this->selectSetUp());
        $this->tpl->assign('staff',$this->isDisplayStaff());
		$this->tpl->display('pad/staffCheck.html');
	}

	//修改子账号的详细信息
	public function updateUser(){
		$this->check_login();
		foreach ($_POST as $key => $value) {
			if ($value == '') {
				echo "<SCRIPT type='text/javascript'>alert('请填写完整再提交!!!');history.back();</SCRIPT>";exit;
			}
		}
		if(!preg_match("/^[0-9]+$/", $_POST['id'])){
		   echo "<SCRIPT type='text/javascript'>alert('该用户不存在!!!');history.back();</SCRIPT>";exit;
		}
		$id = $_POST['id'];
		$data = $_POST;
		$model = new padModel();
		$data = $model->updateUser($data,$id);
		if ($data == 1) echo "<SCRIPT type='text/javascript'>alert('修改成功!!!');history.back();</SCRIPT>";
		else echo "<SCRIPT type='text/javascript'>alert('修改失败!!!');history.back();</SCRIPT>";
	}

	//获取某一年某一月的日有几天
	public function calDays(){
		$this->check_login();
		$num = cal_days_in_month(CAL_GREGORIAN, $_GET['y'], $_GET['n']); // 31
		echo $num;
	}
	//删除客户管理系统
	public function del(){
		$this->check_login();
		$db=new db();
		$id=$_GET['id'];
		$rew = $db->query("delete from pad_customer_quote where id=$id");
		if($rew){
			echo "<SCRIPT type='text/javascript'>alert('删除成功!!!');history.back(-1);</SCRIPT>";
		}else{
			echo "<SCRIPT type='text/javascript'>alert('删除失败!!!');history.back(-1);</SCRIPT>";
		}
	}

	public function more(){
		$checkId = $_POST['checkId'];
		$model = new padModel();
		$quote = $model->getCustomerQuoteData($checkId);
		$customer = $model->getCustomer($quote['c_id']);

		$area = explode('|', $quote['area']);

		$bedroom  = explode(',', $area['0']);
		$liveroom = explode(',', $area['1']);
		$kitchen  = explode(',', $area['2']);
		$bathroom = explode(',', $area['3']);
		$gelou    = explode(',', $area['5']);
		$database = explode(',', $area['6']);

		$totalArea = $quote['total_area'];

		unset($_SESSION['pad_post']);

		$_SESSION['pad_post']['name'] 		  = $customer['name'];		//客户名称
		$_SESSION['pad_post']['tel'] 		  = $customer['tel'];		//客户手机号
		$_SESSION['pad_post']['cho_Province'] = $quote['province'];		//省
		$_SESSION['pad_post']['cho_City']     = $quote['city'];			//市
		$_SESSION['pad_post']['cho_Area']     = $quote['district'];		//县
		$_SESSION['pad_post']['diqu']         = $quote['address'];		//地区
		$_SESSION['pad_post']['house']        = $quote['type'];			//房屋类型


		if (array_sum($bedroom)!='0') {
			$_SESSION['pad_post']['type1']        = count($bedroom);		//卧室数量
			$_SESSION['pad_post']['bedroom']      = $bedroom;				//卧室面积
		}
		if (array_sum($liveroom)!='0') {
			$_SESSION['pad_post']['type2']        = count($liveroom);		//客厅数量
			$_SESSION['pad_post']['liveroom']     = $liveroom;				//客厅面积
		}
		if (array_sum($kitchen)!='0') {
			$_SESSION['pad_post']['type3']        = count($kitchen);		//厨房数量
			$_SESSION['pad_post']['kitchen']      = $kitchen;				//厨房面积
		}
		if (array_sum($bathroom)!='0') {
			$_SESSION['pad_post']['type4']        = count($bathroom);		//卫生间数量
			$_SESSION['pad_post']['bathroom']     = $bathroom;				//卫生间面积
		}
		if (array_sum($gelou)!='0') {
			$_SESSION['pad_post']['type6']        = count($gelou);			//阁楼数量
			$_SESSION['pad_post']['gelou']        = $gelou;					//阁楼面积
		}
		if (array_sum($database)!='0') {
			$_SESSION['pad_post']['type7']        = count($database);		//地下室数量
			$_SESSION['pad_post']['database']     = $database;				//地下室面积
		}
		
		$_SESSION['pad_post']['area']         	  = $totalArea;				//总面积

		$_SESSION['pad_post']['people']           = $quote['people'];		//人口
		$_SESSION['pad_post']['build']            = $quote['build'];		//建筑面积

		echo 1;
	}

	public function delSession(){
		unset($_SESSION['pad_post']);
		echo 1;
	}

	//地区三级联动
	public function changeArea(){
		$model = new padModel();
		$area = $model->getPCDFind($_GET['v']);
		echo json_encode($area);
	}

	//地区三级联动
	public function padPostArea(){
		$model              = new padModel();
		$prov               = $_SESSION['pad_post']['cho_Province'];
		$city               = $_SESSION['pad_post']['cho_City'];
		$district           = $_SESSION['pad_post']['cho_Area'];
		$provId             = $model->getPCDId($prov,1);
		$cityId             = $model->getPCDId($city);
		$districtId         = $model->getPCDId($district);

		$data['provId']     = $provId;
		$data['cityId']     = $cityId;
		$data['districtId'] = $districtId;
		$data['city']       = $model->getPCDFind($provId);
		$data['district']   = $model->getPCDFind($cityId);

		echo json_encode($data);
	}
	
}
