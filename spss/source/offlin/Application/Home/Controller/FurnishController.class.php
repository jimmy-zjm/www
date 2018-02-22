<?php
namespace Home\Controller;
use Think\Controller;

class FurnishController extends BaseController {
	public function _initialize() {  
		parent::_initialize();
		$this->assign('now','2');
	}
    //输入报价信息页面 
	public function details(){
		layout(false);
		$catId = I('get.catId');
		if ($catId == 9) {
			echo "<script>alert('此系统需要其他系统一起选择');history.go(-1);</script>";exit;
		}
		$array = explode(',', $catId);
		$userCity = D('Furnish')->userCity();
		foreach ($array as $k => $v) {
			if(!preg_match("/^[0-9]+$/", $v)){
			  	echo "<script>alert('此系统不存在');history.go(-1);</script>";exit;
			}
			$catId = $v;
			//查询出选择的分类下所有系统
			if (!in_array($catId,explode('|', $userCity['system']))) {
				echo "<script>alert('您没有权限查看此类系统！');history.go(-1);</script>";exit;
			}
			$catName = D('Furnish')->getCatName($catId);

			$quoteList[$catName['cat_name']] = D('Furnish')->quoteList($catId);
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
		    $this->assign('moneyAll',$moneyAll); 
		    $this->assign('build',$build); 
    		$this->assign('moneyList',$moneyList); 
		}
		
		$area     = D('Furnish')->getPCD();
    	$this->assign('area',$area); 
		$this->assign('userCity',$userCity);
		$this->assign('quoteList',$quoteList);
		$this->assign('dealerService',$this->dealerService());
		
		$this->display();
	}

	public function dealerService(){
		$data = M('pad_user')->where(['id'=>$_SESSION['dealerId']])->getField('p_service_city_all');
		$list = explode('|', $data);
		foreach ($list as $k => $v) {
			$lists = explode('-', $v);
			if ($lists[1] == '全部') {
				$id   = M('xgj_area')->where(['name'=>$lists[0],'pid'=>100000])->getField('id');
				$data = M('xgj_area')->field('id,name')->where(['pid'=>$id])->select();
				foreach ($data as $v1) {
					$name = M('xgj_area')->field('name')->where(['pid'=>$v1['id']])->select();
					foreach ($name as $v3) {
						$re[$lists[0]][$v1['name']][] = $v3['name'];
					}
				}
			}else{
				$pid  = M('xgj_area')->where(['name'=>$lists[1],'pid'=>['neq',100000]])->getField('id');
				$name = M('xgj_area')->field('name')->where(['pid'=>$pid])->select();
				foreach ($name as $v2) {
					$re[$lists[0]][$lists[1]][] = $v2['name'];
				}
			}
		}
		return $re;
	}


	//地区三级联动
	public function changeArea($c = null,$val = null,$prov = null){
		if (!empty($_GET['v'])) 	$val = I('get.v');
		if (!empty($_GET['city'])) 	$c   = I('get.city');
		
		$data = $this->dealerService();

		if ($c == 'city') {
			$area = array_keys($data[$val]);
		}else{
			if (!empty($_GET['prov'])) $prov = I('get.prov');
			$area = array_values($data[$prov][$val]);
		}

		if (!empty($_GET['v'])) {
			echo json_encode($area);
		}else{
			return $area;
		}
		
	}

	//地区三级联动
	public function padPostArea(){
		$data['prov']     = array_keys($this->dealerService());
		$data['city']     = $this->changeArea('city',$_SESSION['pad_post']['cho_Province']);
		$data['district'] = $this->changeArea('district',$_SESSION['pad_post']['cho_City'],$_SESSION['pad_post']['cho_Province']);

		$data['provName']     	= $_SESSION['pad_post']['cho_Province'];
		$data['cityName']		= $_SESSION['pad_post']['cho_City'];
		$data['districtName']	= $_SESSION['pad_post']['cho_Area'];

		echo json_encode($data);
	}

	public function selectHouse(){
		if(empty($_GET['name']) || empty($_GET['tel']) || !preg_match("/^1[34578]\d{9}$/", $_GET['tel'])) exit;
		
		$data = D('Furnish')->getCustomerRow($_GET['name'],$_GET['tel']);

		if (empty($data)) {
			echo 'false';exit;
		}
		
		// $data['province']     = D('Furnish')->getPCDId($data['province'],1);
		// $data['city']         = D('Furnish')->getPCDId($data['city']);
		// $data['district']     = D('Furnish')->getPCDId($data['district']);

		// $data['cityList']     = D('Furnish')->getPCDFind($data['province']);
		// $data['districtList'] = D('Furnish')->getPCDFind($data['city']);

		$data['provList']     = array_keys($this->dealerService());
		$data['cityList']     = $this->changeArea('city',$data['province']);
		$data['districtList'] = $this->changeArea('district',$data['city'],$data['province']);
		echo json_encode($data);
	}


	//产生报价页面
	public function offer(){

		if (!empty($_POST['cho_Province'])) $_POST['cho_Province'] 	= I('post.cho_Province');
		if (!empty($_POST['cho_City'])) 	$_POST['cho_City'] 		= I('post.cho_City');
		if (!empty($_POST['cho_Area'])) 	$_POST['cho_Area'] 		= I('post.cho_Area');

		if (empty($_GET['checkId'])) {

			if (!empty($_POST['q_id'])) {
				$_POST['quote_id'] = $_POST['q_id'];
			}

			if (!empty($_POST)) $_SESSION['pad_post']=$_POST;

			if (empty($_POST['quote_id'])) {
				echo "<script>alert('请选择报价产品');history.go(-1);</script>";exit;
			}else{
				foreach ($_POST['quote_id'] as $k => $v) {
					if ($v == '100' && count($_POST['quote_id'])==1) {
						echo "<script>alert('此系统需要其他系统一起选择');history.go(-1);</script>";exit;
					}
				}
			}

			if(!preg_match("/^1[34578]\d{9}$/", $_POST['tel'])){
			    echo "<script>alert('手机号码不正确');history.go(-1);</script>";exit;
			}

			if($_POST['cho_Province']=='请选择' || $_POST['cho_City']=='请选择' || $_POST['cho_Area']=='请选择'){
			    echo "<script>alert('请选择地址');history.go(-1);</script>";exit;
			}

			if(empty($_POST['diqu'])){
			    echo "<script>alert('请输入详细地址');history.go(-1);</script>";exit;
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
			$row = D('Furnish')->getCustomerRow($_POST['name'],$_POST['tel']);
			//查询系统名称
			if ($_POST['href'] == 'details') {
				foreach ($_POST['quote_id'] as $key => $val) {
					$quoteNames[] = D('Furnish')->getFurnishQuote($val);
				}
			}else{
				$quoteName = D('Furnish')->getFurnishQuote($_POST['quote_id']);
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
				'u_id'          => $_SESSION['dealerId'],		//用户ID
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
				$customerId = D('Furnish')->addCustomer($addData);
				if (empty($customerId)) $error = true;
			}else{
				$customerId = $row['id'];
				$re = D('Furnish')->updateCustomer($addData,$customerId);
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
			$re = D('Furnish')->CustomerQuote($id,$quoteName['quote_name']);
			$checkId = $re['id'];
			/********************************************/
		}else{

			$customerQuote = D('Furnish')->getCustomerQuoteData($_GET['checkId']);

			if (empty($customerQuote)) {
				echo "<script>alert('没有您要查询的数据');history.go(-1);</script>";exit;
			}

			$customerData  = D('Furnish')->getCustomer($customerQuote['c_id']);
			
			$catId  	   = D('Furnish')->getFurnishQuote($customerQuote['quote_id']);
			
			

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

			

			$this->assign('cqid',$_GET['checkId']);
			$build = $customerQuote['build'];
			

			$_GET['catId'] = $catId['cat_id'];
			$id            = $customerQuote['id'];
			$tableName     = 'pad_customer_quote';

			$checkId = I('get.checkId');

			$lists['list'] = json_decode(stripslashes($customerQuote['info']),true);

			$lists['all']     = $customerQuote['price'];
			$lists['install'] = $customerQuote['install'];
			$lists['fuCai']   = $customerQuote['price']-$customerQuote['zprice']-$customerQuote['install'];
		}

		/*********************/
		//查询是否已提交
		$order = D('Furnish')->getPadOrder($checkId);
		$this->assign('order',$order);
		/*********************/



		/*********************/
		//计算总面积
		$areaAll = 0;
		foreach ($houseArea as $k => $v) {
			if (is_array($v)) {
				$areaAll += array_sum($v);
			}
		}
		$this->assign('areaAll',$areaAll);
		$_SESSION['pad_post']['area'] = $areaAll;
		/*********************/

		$_SESSION['padHouseArea'] = $houseArea;
		$this->assign('houseArea',$houseArea);

		!empty($_GET['checkId'])?$customerQuoteId = $_GET['checkId']:$customerQuoteId = '';

        $houseTable = array('tableName'=>$tableName,'where'=>['id'=>$id]);

        $curl['lv']		  = 1;
        $curl['houseId']  = $houseTable;
        $curl['is_list']  = true;

        if (empty($lists)) {
        	if (!empty($customerQuote['quoteids'])){
	        	$quoteId = explode(',', $customerQuote['quoteids']);
	        }else if (!empty($_SESSION['quoteId']) && $quoteId == 100) {
	        	$quoteId = explode(',', $_SESSION['quoteId']);
	        }
	        
	    	if (is_array($quoteId)) {
	        	foreach ($quoteId as $k => $v) {

	        		$curl['cid']      = $v;

	        		if ($v==100) {
		        		$curl['compare']  = $listArray;
		        	}

		        	$lists = getQuote($curl);

		        	if ($v==100) {
		        		$_SESSION['quoteId'] = implode(',', $quoteId);
		        	}

		        	if ($lists == 'error') {
			        	echo "<script>alert('抱歉！您的房间不支持该系统，请电话咨询或更换系统');history.go(-1);</script>";exit;
			        }

			        $listsArray[$v]['all']     = $lists['all'];
					$listsArray[$v]['install'] = $lists['install'];
					$listsArray[$v]['fuCai']   = $lists['fuCai'];
					if (in_array('100',$quoteId)) {
						$listArray[$v]['list'] = $lists['list'];
		        	}
		        }
	        }else{
	        	$curl['cid'] = $quoteId;
	        	$lists = getQuote($curl);
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
		        $catId = I('post.cid');
		        $this->redirect('details', array('catId' => $catId));
	        }
        }

        $newLists = $lists['list'];

        $newMoney['all']     = $lists['all'];
		$newMoney['install'] = $lists['install'];
		$newMoney['fuCai']   = $lists['fuCai'];

        $_SESSION['pad_newList']  = $newLists;
        $_SESSION['pad_newMoney'] = $newMoney;
        
        $_SESSION['pad_newList_name'] = $quoteName['quote_name'];
        $_SESSION['pad_newList_id'] = $quoteName['quote_id'];


        foreach ($newLists as $k => $v) {
			$reList[$v['type']][] = $v; 
		}

		$keys = array_keys($reList);
		$re = M('xgj_quote_type')->where(['id'=>['in',$keys]])->select();
		foreach ($re as $k => $v) {
			$listType[$v['id']] = $v['text'];
		}

        $this->assign('build',$build);
 		$this->assign('type',1);
    	
        $this->assign('quoteId',$quoteName['quote_id']);
        $this->assign('customerId',$customerId);
        $this->assign('quoteName',$quoteName['quote_name']);
        $this->assign('list',$reList);
        $this->assign('listType',$listType);
        $this->assign('moneyAll',$newMoney);
        $this->assign('count',count($newLists));
		$this->display('offer');
	    
	}

	public function saveTs(){
		$id    = I('get.id');
		$o     = I('get.o');
		$val   = I('get.v');
		$num   = I('get.num');
		$build = I('get.build');
		
		if ($o == 3 && $val=='false') {
			$_SESSION['offerList']['3'][] = $id;
		}else if ($o == 3 && $val=='true') {
			foreach ($_SESSION['offerList']['3'] as $k => $v) {
				if ($v==$id) unset($_SESSION['offerList']['3'][$k]);
			}
			if (count($_SESSION['offerList']['3'])==0) {
				unset($_SESSION['offerList']['3']);
			}
		}else if($o==1){
			if ($_SESSION['offerList']['1'][$id]+1 != 0) {
				$_SESSION['offerList']['1'][$id] = $_SESSION['offerList']['1'][$id]+1;
			}else{
				unset($_SESSION['offerList']['1'][$id]);
			}
		}else if($o==2){
			if ($_SESSION['offerList']['1'][$id]-1 != 0) {
				$_SESSION['offerList']['1'][$id] = $_SESSION['offerList']['1'][$id]-1;
			}else{
				unset($_SESSION['offerList']['1'][$id]);
			}
		}else if($o==4){
			if ($num != 0) {
				$_SESSION['offerList']['1'][$id] = $num;
			}else{
				unset($_SESSION['offerList']['1'][$id]);
			}
		}
		
		$lists         = $_SESSION['pad_newMoney'];
		$lists['list'] = $_SESSION['pad_newList'];
		$lists = $this->chuli($lists);

		$re['zhucai1'] = $lists['all']-$lists['install']-$lists['fuCai'];
		$re['zhucai2'] = (ceil((($lists['all']-$lists['install']-$lists['fuCai'])/$build)*100))/100;
		$re['fucai1']  = $lists['install']+$lists['fuCai'];
		$re['fucai2']  = (ceil((($lists['install']+$lists['fuCai'])/$build)*100))/100;
		echo json_encode($re);
		// echo json_encode($_SESSION['offerList']);
	}


		//添加客户已查询系统信息意向表
	public function addCustomerQuote(){

		$re = D('Furnish')->CustomerQuote($_GET['c_id'],$_GET['name']);
		$customer = D('Furnish')->getCustomer($_GET['c_id']);

		if (!empty($_SESSION['pad_newMoney']) && !empty($_SESSION['pad_newList'])) {
			$lists         = $_SESSION['pad_newMoney'];
			$lists['list'] = $_SESSION['pad_newList'];
			
			// $lists = $this->chuli($lists);
			$price   = $lists['all'];
			$install = $lists['install'];
			$zprice  = $lists['all']-$lists['install']-$lists['fuCai'];
		}else{
			echo 0;
			// $price  = $_GET['money'];
			// $zprice = $_GET['zprice'];
		}
		
		$addCustomerQuoteData = array(
			'u_id'       =>$_SESSION['dealerId'],
			'quote_id'   =>$_SESSION['pad_newList_id'],
			'c_id'       =>$_GET['c_id'],
			'name'       =>$_GET['name'],
			'price'      =>$price,
			'zprice'     =>$zprice,
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
			'time'       =>time(),
			'edit'		 =>json_encode($_SESSION['offerList']),
			'info'		 =>addslashes(json_encode($_SESSION['pad_newList'])),
			'install'	 =>$install,
        	);

		if ($_SESSION['pad_newList_id']==100) {
			$addCustomerQuoteData['quoteids'] = $_SESSION['quoteId'];
		}
		// var_dump($_GET['quote_id']);exit;
		// echo $addCustomerQuoteData['quoteId'];exit;

		if (empty($re)) {
        	$result = D('Furnish')->addCustomerQuote($addCustomerQuoteData);
		}else{
			$result = D('Furnish')->editCustomerQuote($addCustomerQuoteData,$re['id']);
		}

		echo $result;
        
	}

	
	public function order(){

		$checkId = I('get.checkId');

		if (empty($checkId)) {
			$c_id   = I('get.c_id');
			$name   = I('get.name');
			$money  = I('get.money');
			$zprice = I('get.zprice');

			$customer = D('Furnish')->getCustomer($c_id);
			if (empty($c_id) && empty($name) && empty($money) && empty($zprice)) {
				echo '提交失败！';exit;
			}
			$re = D('Furnish')->CustomerQuote($c_id,$name);

			if (empty($re)) {
				
				$addCustomerQuoteData = array(
					'u_id'       =>$_SESSION['dealerId'],
					'quote_id'   =>$_SESSION['pad_newList_id'],
					'c_id'       =>$c_id,
					'name'       =>$name,
					'price'      =>$money,
					'zprice'     =>$zprice,
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
					'time'       =>time(),
					'edit'		 =>json_encode($_SESSION['offerList']),
		        	);
				
				if ($_SESSION['pad_newList_id']==100) {
					$addCustomerQuoteData['quoteids'] = $_SESSION['quoteId'];
				}

	        	$customerQuoteId = D('Furnish')->addCustomerQuote($addCustomerQuoteData);
			}else{
				$customerQuoteId = $re['id'];
			}
		}else{
			$customerQuoteId = $checkId;
			$c_id=M('pad_customer_quote')->where(['id'=>$customerQuoteId])->getField('c_id');
			$customer = D('Furnish')->getCustomer($c_id);
		}
		$uid=M('xgj_users')->where(['mobile_phone'=>$customer['tel']])->getField('user_id');
	
		if(empty($uid)){
			$info=[
				'mobile_phone'=>$customer['tel'],
				'user_name'=>$customer['name'],
				'password'=>md5(substr($customer['tel'],5,6).C(MD5_PASSWORD)),
				'reg_time'=>time(),
				'is_pad'=>'2',
			];

			$uid=M('xgj_users')->add($info);
			if($uid===false){
				echo '用户添加失败！';exit;
			}
		}
		
		$re = D('Furnish')->getPadOrder($customerQuoteId);
			
		if (!empty($re)) {
			echo '该系统已提交,请勿重复提交';exit;
		}
		// !empty($_GET['checkId'])?$customerQuoteId = $_GET['checkId']:$customerQuoteId = '';

		
		$customerQuote = D('Furnish')->getCustomerQuoteData($customerQuoteId);

		if (empty($customerQuote)) {
			echo '提交失败';exit;
		}

		$offer = I('get.offer');
		if (!empty($checkId) && !empty($customerQuote['edit']) && empty($offer)) {
			$_SESSION['offerList'] = json_decode($customerQuote['edit'],true);
		}

		$customerData  = D('Furnish')->getCustomer($customerQuote['c_id']);
		
		$catId  	   = D('Furnish')->getFurnishQuote($customerQuote['quote_id']);

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
		$this->assign('areaAll',$areaAll);
		$_SESSION['pad_post']['area'] = $areaAll;
		/*********************/

		$this->assign('houseArea',$houseArea);

		$houseTable = array('tableName'=>$tableName,'where'=>['id'=>$id]);

		$curl['lv']		  = 1;
        $curl['houseId']  = $houseTable;
        $curl['is_list']  = true;
	    
	    if (!empty($customerQuote['quoteids'])){
        	$quoteId = explode(',', $customerQuote['quoteids']);
        }else if (!empty($_SESSION['quoteId']) && $quoteId == 100) {
        	$quoteId = explode(',', $_SESSION['quoteId']);
        }
        
    	if (is_array($quoteId)) {
        	foreach ($quoteId as $k => $v) {

        		$curl['cid']      = $v;

        		if ($v==100) {
	        		$curl['compare']  = $listArray;
	        	}

	        	$lists = getQuote($curl);

	        	if ($v==100) {
	        		$_SESSION['quoteId'] = implode(',', $quoteId);
	        	}

	        	if ($lists == 'error') {
		        	echo "<script>alert('抱歉！您的房间不支持该系统，请电话咨询或更换系统');history.go(-1);</script>";exit;
		        }
		        
				$listArray[$v]['list'] = $lists['list'];
	        }
        }else{
        	$curl['cid']      = $quoteId;
	        $lists = getQuote($curl);
        	if ($lists == 'error') {
	        	echo "抱歉！您的房间不支持该系统，请电话咨询或更换系统";exit;
	        }
        }
        // var_dump($lists);exit;
        $lists = $this->chuli($lists);

        $newLists = $lists['list'];
        
		$microtime            = explode('.',microtime(true));//转化时间戳 
		$data['info']         = addslashes(json_encode($newLists));
		$data['q_id']         = $customerQuoteId;
		$data['u_id']         = $_SESSION['dealerId'];
		$data['order_status'] = '1';
		$data['add_time']     = time();
		$data['uid']		  = $uid;
		$data['sn']           = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);//订单编号

		$re = D('Furnish')->addPadOrder($data);
		if ($re > 0) {
			echo '1';
		}else{
			echo '提交失败！';
		}
		
	}


	//发送邮件
	public function email(){

		if(!preg_match("/^[0-9a-z_][_.0-9a-z-]{0,32}@([0-9a-z][0-9a-z-]{0,32}\.){1,4}[a-z]{2,4}$/i", $_GET['mail'])){
		    echo '请正确填写联系邮箱！';exit;
		}
		
		$customerQuoteId = trim($_GET['customerQuoteId']);

		/******************************************/
		//列表页发送邮件
		$name     = empty($_GET['name'])?'':$_GET['name'];
		$tel      = empty($_GET['tel'])?'':$_GET['tel'];
		$quote_id = empty($_GET['quote_id'])?'':$_GET['quote_id'];

		if (!empty($name) && !empty($tel) && !empty($quote_id)) {
			$row = D('Furnish')->getCustomerRow($name,$tel);
			$customerId = $row['id'];
	        $houseTable = array('tableName'=>'pad_customer_quote','where'=>['id'=>$customerQuoteId]);

	        $curl['lv']		  = '1';
	        $curl['houseId']  = $houseTable;
	        $curl['is_list']  = true;
	        $curl['cid']      = $quote_id;
	
	        $re = D('Furnish')->getCustomerQuoteData($customerQuoteId);

	    //     if (!empty($re['quoteids'])) {
	    //     	$quoteId = explode(',', $re['quoteids']);
	    //     }

	    // 	if (!empty($quoteId)) {
	    //     	foreach ($quoteId as $k => $v) {

	    //     		$curl['cid']      = $v;

	    //     		if ($v==100) {
		   //      		$curl['compare']  = $listArray;
		   //      	}

		   //      	$lists = getQuote($curl);

		   //      	if ($v==100) {
		   //      		$_SESSION['quoteId'] = implode(',', $quoteId);
		   //      	}

		   //      	if ($lists == 'error') {
			  //       	echo "<script>alert('抱歉！您的房间不支持该系统，请电话咨询或更换系统');history.go(-1);</script>";exit;
			  //       }

					// $listArray[$v]['list'] = $lists['list'];
		   //      }

		   //  }else{
		   //  	$lists = getQuote($curl);
		   //  }

		 //    $offer = I('get.offer');
		 //    if (!empty($re['edit']) && empty($offer)) {
			// 	$_SESSION['offerList'] = json_decode($re['edit'],true);
			// }
	        $_SESSION['pad_newList'] = json_decode(stripslashes($re['info']),true);

	        $newMoney['all']     = $re['price'];
			$newMoney['install'] = $re['install'];
			$newMoney['fuCai']   = $re['price']-$re['install']-$re['zprice'];

	        $_SESSION['pad_newMoney'] = $newMoney;

    		$furnishQuote = D('Furnish')->getFurnishQuote($quote_id);
    		$_SESSION['pad_newList_name'] = $furnishQuote['quote_name'];

		}
		/******************************************/
		$setup = $this->selectSetUp();
		//图片 品牌 名称 产地 型号 单价
		$email[] = $_GET['mail'];
		// require_once(WWW_DIR . "/libs/mail/mail.php");
		// $mail = new Mail();

		$body = '';

		$quoteData  = $_SESSION['pad_newList'];
		$quoteMoney = $_SESSION['pad_newMoney'];

    	if ($customerQuoteId) {
    		$customerQuote = D('Furnish')->getCustomerQuoteData($customerQuoteId);
			$houseAreaArray = explode('|', $customerQuote['area']);
			foreach ($houseAreaArray as $key => $value) {
				$houseArea[$key] = explode(',', $value);
			}
    	}else{
    		$houseArea = $_SESSION['padHouseArea'];
    	}

    	// $quoteDataCopy = $quoteMoney;
    	// $quoteDataCopy['list'] = $quoteData;
    	// $quoteData = $this->chuli($quoteDataCopy);
    	// $quoteMoney['all'] = $quoteData['all'];
    	// $quoteMoney['install'] = $quoteData['install'];
    	// $quoteMoney['fuCai'] = $quoteData['fuCai'];
    	// $quoteData = $quoteData['list'];

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

		vendor('mail.mail');
    	$Mail = new \Mail();

		if ($Mail->send($subject,$body,$to)) {
		    echo "发送成功!";exit;
		}else{
		    echo "发送失败!";exit;
		} 
	}

	//导出
	public function gotos(){
		
		if (!empty($_GET['cqid']) && preg_match("/^[0-9]+$/", $_GET['cqid'])){
			$row = D('Furnish')->getCustomerQuoteData($_GET['cqid']);
			if (empty($row)) {
				echo "<SCRIPT type='text/javascript'>alert('该报价不存在!!!');history.back();</SCRIPT>";exit;
			}
			$area = $row['area'];
			// $rows = D('Furnish')->getCustomer($row['c_id']);
			// $offer = I('get.offer');
			// if (!empty($row['edit']) && empty($offer)) {
			// 	$_SESSION['offerList'] = json_decode($row['edit'],true);
			// }
			$_SESSION['pad_newMoney']['all']     = $row['price'];
			$_SESSION['pad_newMoney']['fuCai']   = $row['price']-$row['install']-$row['zprice'];
			$_SESSION['pad_newMoney']['install'] = $row['install'];

			$_SESSION['pad_newList']      = json_decode(stripslashes($row['info']),true);;
			$_SESSION['pad_newList_name'] = $row['name'];
		}else{
			$padHouseArea  = $_SESSION['padHouseArea'];
			foreach ($_SESSION['padHouseArea'] as $k => $v) {
				if (is_array($v)) $area .= '|'.implode(',', $v);
				else 			  $area .= '|'.$v;
			}
			$area = trim($area,'|');
		}

		// if (!empty($rows)) {
		// 	$name            =$rows['name'];
		// 	$tel             =$rows['tel'];
		// 	$quote_id        =$row['quote_id'];
		// 	$customerQuoteId =$_GET['cqid'];
		// 	$customerId 	 =$rows['id'];
		// 	$table 			 ='pad_customer_quote';
		// 	$id 			 =$_GET['cqid'];

		// 	// json_decode(stripslashes($row['info']),true);
		// }else{
		// 	$name            =empty($_GET['name'])?$_SESSION['pad_post']['name']:$_GET['name'];
		// 	$tel             =empty($_GET['tel'])?$_SESSION['pad_post']['tel']:$_GET['tel'];
		// 	$quote_id        =empty($_GET['quote_id'])?$_SESSION['pad_post']['quote_id']:$_GET['quote_id'];
		// 	$customerQuoteId =empty($_GET['cqid'])?'':$_GET['cqid'];
		// 	$row = D('Furnish')->getCustomerRow($name,$tel);
		// 	$customerId 	 = $row['id'];
		// 	$table 			 ='pad_customer';
		// 	$id 			 = $row['id'];
		// }

		$lists         = $_SESSION['pad_newMoney'];
		$lists['list'] = $_SESSION['pad_newList'];
		$lists['name'] = $_SESSION['pad_newList_name'];
		$lists['userHouseData']['area'] = $area;
		$lists['quote_id'] = I('get.quote_id');

		if (empty($lists)) {
			echo "<SCRIPT type='text/javascript'>alert('请重新报价后导出!!!');history.back();</SCRIPT>";exit;
		}

		$this->excel($lists);

		// $order = A('Furnish');
        // $houseTable = array('tableName'=>$table,'where'=>['id'=>$id]);
        // $curl['lv']		  = '1';
        // $curl['houseId']  = $houseTable;
        // $curl['is_list']  = true;
        // $curl['dump']  	  = '3';
        // $curl['cid']      = $quote_id;


        // if (!empty($row['quoteids'])) {
        // 	$quoteId = explode(',', $row['quoteids']);
        // }else if (!empty($_SESSION['quoteId']) && $quote_id == 100) {
        // 	$quoteId = explode(',', $_SESSION['quoteId']);
        // }

    // 	if (!empty($quoteId)) {
    //     	foreach ($quoteId as $k => $v) {

    //     		$curl['cid']      = $v;

    //     		if ($v==100) {
	   //      		$curl['compare']  = $listArray;
	   //      	}

	   //      	$lists = getQuote($curl);

	   //      	if ($v==100) {
	   //      		$_SESSION['quoteId'] = implode(',', $quoteId);
	   //      	}

	   //      	if ($lists == 'error') {
		  //       	echo "<script>alert('抱歉！您的房间不支持该系统，请电话咨询或更换系统');history.go(-1);</script>";exit;
		  //       }

				// $listArray[$v]['list'] = $lists['list'];
	   //      }
	   //  }else{
	   //  	$lists = getQuote($curl);
	   //  }

	   //  $lists['quote_id'] = $quote_id;

		// $lists = $this->chuli($lists);
		
	    
	}

	public function chuli($lists){
		foreach ($lists["list"] as $k => $v) {
			if (!empty($_SESSION['offerList'][3])) {
    			if (in_array($v['id'], $_SESSION['offerList'][3])) {
    				unset($lists["list"][$k]);
    				$lists['all'] -= ceil($v['num']*$v['price']);
    				if ($v['lv']==2) {
    					$lists['fucai'] -= ceil($v['num']*$v['price']);
    				}else if ($v['lv']==3) {
    					$lists['install'] -= ceil($v['num']*$v['price']);
    				}
    				$v = '';
    			}
    		}

    		if (!empty($_SESSION['offerList'][1])) {
    			foreach ($_SESSION['offerList'][1] as $k1 => $v1) {
    				if ($v['id'] == $k1) {
    					$lists["list"][$k]['num'] = $v['num']+$v1;
    					$lists['all'] += $v1*$v['price'];
	    				if ($v['lv']==2) {
	    					$lists['fucai'] += $v1*$v['price'];
	    				}else if ($v['lv']==3) {
	    					$lists['install'] += $v1*$v['price'];
	    				}
    				}
    			}
			}
		}
		$lists['all']     = ceil($lists['all']);
		$lists['fucai']   = ceil($lists['fucai']);
		$lists['install'] = ceil($lists['install']);
		return $lists;
	}

	public function more(){

		$checkId = $_POST['checkId'];
		
		$quote = D('Furnish')->getCustomerQuoteData($checkId);

		$customer = D('Furnish')->getCustomer($quote['c_id']);
		
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




	public function excel($data){
		$userHouseData = $data['userHouseData'];
		$customerId    = $data['customerId'];
		$customerData  = $data['list'];
		$totalMoneys   = $data['all'];
		$fuCai 		   = $data['fuCai'];
		$filename 	   = $data['name'];

		/*************************************/
		//处理PAD设置后需要导出的数据
		$userSetup = array('1','1','1','1','1','1','1');

        if (!empty($customerId)) {
			$userId    = $userHouseData['u_id'];
			$userData  = M('pad_user')->where(['id'=>$userId])->find();
			$userSetup = explode('|', $userData['setup']);
			//图片 品牌 名称 产地 型号 单价
        }
        /*************************************/

		$name='Excelfile'; 
   		
   		$fucaiMoney = 0;
   		foreach ($customerData as $k => $v) {
   			if ($v['lv'] != '2') {
   				$customerDatas[] = $v;
   			}
   			if ($v['lv'] == '2') {
   				$fucaiMoney += $v['money']*$v['num'];
   			}
   		}

   		/****************************/
		//查询系统分类并处理
		$typeData = M('xgj_quote_type')->where(['quote_id'=>$data['quote_id']])->select();

		foreach ($typeData as $k => $v) {
			$type[$v['id']] = $v['text'];
		}
		/****************************/

   		foreach ($customerDatas as $k => $v) {
   			$customerDatas[$k]['text'] = $type[$v['type']];
   		}
   		
   		$total_area = explode('|', $userHouseData['area']);

   		$houseArea['0'] = explode(',', $total_area['0']);
   		$houseArea['1'] = explode(',', $total_area['1']);
   		$houseArea['2'] = explode(',', $total_area['2']);
   		$houseArea['3'] = explode(',', $total_area['3']);
   		$houseArea['5'] = explode(',', $total_area['5']);
   		$houseArea['6'] = explode(',', $total_area['6']);

   		$houseString = '';
   		foreach ($houseArea as $key => $val) {
			if ($key=='0') $name = '卧 室';
			else if ($key=='1') $name = '客 厅';
			else if ($key=='2') $name = '厨 房';
			else if ($key=='3') $name = '卫生间';
			else if ($key=='5') $name = '阁 楼';
			else if ($key=='6') $name = '地下室';
			if (!empty($houseArea[$key]['0'])) {
				foreach ($houseArea[$key] as $k => $v) {
					$houseString .= $name.($k+1).'：'.$v.'㎡ ';
				}
				$houseString .= "\r\n";
				$areaArray[] = $val;
			}
		}
		vendor('Excel.PHPExcel');
		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->getDefaultStyle()->getFont()->setName('宋体');
    	$objPHPExcel->setActiveSheetIndex(0)
	    	->setCellValue('A1', $filename)
	        ->setCellValue('A3', 'ID')
	        ->setCellValue('B3', empty($userSetup['2'])?'':'材料名称')
	        ->setCellValue('C3', '产品编码')
	        ->setCellValue('D3', empty($userSetup['4'])?'':'型号')
	        ->setCellValue('E3', empty($userSetup['1'])?'':'品牌')
	        ->setCellValue('F3', empty($userSetup['3'])?'':'产地')
	        ->setCellValue('G3', empty($userSetup['6'])?'':'数量')
	        ->setCellValue('H3', empty($userSetup['5'])?'':'单价')
	        ->setCellValue('I3', '总价')
	        ->setCellValue('A2', $houseString);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);//设置列宽
		$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);//设置行高
		$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20*count($areaArray));//设置行高
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);

		
	    $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');//合并单元格
	    $objPHPExcel->getActiveSheet()->mergeCells('A2:I2');//合并单元格
		//设置标题行样式
		$objPHPExcel->getActiveSheet(0)->getStyle('A1')->applyFromArray(
            array(
				  'font' => array ('bold' => true, 'size' => 16),
                  'alignment' => array( 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>\PHPExcel_Style_Alignment::VERTICAL_CENTER)					 
			 )
        );

        $objPHPExcel->getActiveSheet(0)->getStyle('A2')->applyFromArray(
            array(
				  'font' => array ('bold' => true, 'size' => 14),
                  'alignment' => array( 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical'=>\PHPExcel_Style_Alignment::VERTICAL_CENTER)					 
			 )
        );

		$pada=array();
		foreach($customerDatas as $k=>$v){
			$customerDatas[$k]['text']=!empty($customerDatas[$k]['text'])?$customerDatas[$k]['text']:"产品列表";
			$pada[$customerDatas[$k]['text']][$k]=$v;
		}
		//var_dump('<pre>',$pada);die();
		$ii=1;//用于分类序列号			
		$iii=4;

		foreach($pada as $k=>$v){
			
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$iii, $ii."、".$k);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$iii.':'.'I'.$iii);//合并单元格
			//设置背景色
			$objPHPExcel->getActiveSheet(0)->getStyle('A'.$iii)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet(0)->getStyle('A'.$iii)->getFill()->getStartColor()->setARGB('FFCAE8EA');
			$objPHPExcel->getActiveSheet(0)->getRowDimension($iii)->setRowHeight(30);//设置行高
			$objPHPExcel->getActiveSheet()->getStyle('A'.$iii)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$iii++;
			$i=1;
			foreach($v as $key=>$val){								
					$objPHPExcel->getActiveSheet(0)->getRowDimension($iii)->setRowHeight(30);
					$objPHPExcel->getActiveSheet()->getStyle($iii)->applyFromArray(
							array(
								  'alignment' => array( 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>\PHPExcel_Style_Alignment::VERTICAL_CENTER)					 
							 )
					 );
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A".$iii, $ii.'.'.$i)
					->setCellValue("B".$iii, empty($userSetup['2'])?'':$val['name'])
					->setCellValue("C".$iii, "'".$val['sn'])
					->setCellValue("D".$iii, empty($userSetup['4'])?'':$val['model'])
					->setCellValue("E".$iii, empty($userSetup['1'])?'':$val['brand'])
					->setCellValue("F".$iii, empty($userSetup['3'])?'':$val['origin'])
					->setCellValue("G".$iii, empty($userSetup['6'])?'':$val['num'])
					->setCellValue("H".$iii, empty($userSetup['5'])?'':$val['money'])
					->setCellValue("I".$iii, $val['money']*$val['num']);
					$i++;
					$iii++;
			}
			$ii++;
		}

	 	$objPHPExcel->setActiveSheetIndex(0)
		 	->setCellValue("B".$iii, '辅材价格')
		 	->setCellValue("I".$iii, ceil($fuCai))
		 	->setCellValue("B".($iii+1), '合计')
		 	->setCellValue("I".($iii+1), ceil($totalMoneys));
		$objPHPExcel->getActiveSheet(0)->getRowDimension($iii)->setRowHeight(30);
		$objPHPExcel->getActiveSheet()->getStyle($iii)->applyFromArray(
							array(
								  'alignment' => array( 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>\PHPExcel_Style_Alignment::VERTICAL_CENTER)					 
							 )
					 );
		$objPHPExcel->getActiveSheet(0)->getRowDimension($iii+1)->setRowHeight(30);
		$objPHPExcel->getActiveSheet()->getStyle($iii+1)->applyFromArray(
							array(
								  'alignment' => array( 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>\PHPExcel_Style_Alignment::VERTICAL_CENTER)					 
							 )
					 );

		// $objPHPExcel->getActiveSheet()->setTitle("$dataName"); 
	    $objPHPExcel->setActiveSheetIndex(0);   
	    ob_end_clean() ;
	    header('Content-Type: application/vnd.ms-excel;');
	    header('Content-Disposition: attachment;filename='.$filename."材料清单表".'.xls');
	    header('Cache-Control: max-age=0');
	    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output');exit;
	}

}