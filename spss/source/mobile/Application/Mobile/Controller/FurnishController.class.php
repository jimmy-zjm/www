<?php
namespace Mobile\Controller;

class FurnishController extends BaseController {

    public function price(){

    	$cid = I('get.cid');

    	if (!quoteId($cid)) $this->error('请选择系统',U('Index/index'));

    	$prov = getPCD();
    	$city = $this->getOnePCD($prov['0']['id']);
    	$dist = $this->getOnePCD($city['0']['id']);
    	$this->assign('prov',$prov);
    	$this->assign('city',$city);
    	$this->assign('dist',$dist);
    	$this->assign('cid',$cid);
    	$this->assign('header','房屋信息');
        $this->display();
    }

    //查询服务商
    public function service($data=null,$xcd=null){
		$dealer = M('xgj_furnish_dealer')->where('d_runstatus=1')->field('d_service_city_all,d_province,d_city,d_area')->select();

		if (empty($xcd)) {
			$x = $data['province'];
			$c = $data['city'];
			$d = $data['district'];
			$xcd = $x.'-'.$c.'-'.$d;
		}

		$re = false;
		foreach ($dealer as $k => $v) {
			$citys = $v['d_province'].'-'.$v['d_city'].'-'.$v['d_area'];
			$city = explode('|', $v['d_service_city_all']);
	
			if ($citys == $xcd) $re = true;
			else if (in_array($xcd, $city)) $re = true;
		}

		return $re;
    }

    public function getOnePCD($pid){
    	return M('xgj_area')->where(['pid'=>$pid])->field('id,name')->select();
    }

    public function calculation(){

    	$_SESSION['mobile']['info'] = $_POST;
    	if (empty($_SESSION['user']['userId'])) die('login');

		$pcd   = I('post.pcd');
		$addr  = I('post.addr');
		$area  = I('post.area');
		$type1 = I('post.type1');
		$type2 = I('post.type2');

		$service = $this->service('',str_replace("/","-",$pcd));

		$pcd = explode('/', $pcd);

		if (empty($service)) die('很遗憾！您所在的地域没有我们专业的服务商');

		if (count($pcd) != 3 || empty($addr)) die('请认真填写地址');

		if(!preg_match("/^[1-4]{1}$/", $type1)) die('请选择房屋类型');

		if(!preg_match("/^[1-7]{1}$/", $type2)) die('请选择房屋户型');

		if(!preg_match("/^[0-9]{2,4}(.[0-9]{1,2})?$/", $area) || $area < 10) die('请认真填写房屋面积');

		

		$userId = $_SESSION['user']['userId'];

		if ($type2 <= 3) {
			$wsjn = '1';
			$wsj  = '5';
			$wsjs = '5';
			$cf   = '5';
		}elseif ($type2 == 4) {
			$wsjn = '2';
			$wsj  = '8,4';
			$wsjs = '12';
			$cf   = '5';
		}elseif ($type2 == 5 || $type2 == 6) {
			$wsjn = '3';
			$wsj  = '10,6,4';
			$wsjs = '20';
			$cf   = '10';
		}elseif ($type2 > 6) {
			$wsjn = '4';
			$wsj  = '10,6,4,4';
			$wsjs = '24';
			$cf   = '10';
		}

		if ($type2 == 1) {
			$ting  = '20';
			$tingn = '1';
			$tings = '20';
		}elseif ($type2 == 2 || $type2 == 3) {
			$ting  = '20,10';
			$tingn = '2';
			$tings = '30';
		}elseif ($type2 == 4 || $type2 == 5) {
			$ting  = '20,15';
			$tingn = '2';
			$tings = '35';
		}elseif ($type2 >= 6) {
			$ting  = '20,15,10';
			$tingn = '3';
			$tings = '45';
		}

		$wsArea = $area - $wsjs - $cf - $tings;

		$wsArea = floor($wsArea/$type2*100)/100;

		if ($wsArea < 0) $wsArea = 1;

		$wsAreas = '';
		for ($i=0; $i < $type2 ; $i++) { 
			$wsAreas .= $wsArea.',';
		}

		$wsAreas = rtrim($wsAreas,',');

		$layout = $type2.','.$tingn.',1,'.$wsjn.',0,0,0';

		$total_area = $wsAreas.'|'.$ting.'|'.$cf.'|'.$wsj.'|0|0|0';

		$array = array(
			'user_id'    =>$userId,
			'province'   =>$pcd['0'],
			'city'       =>$pcd['1'],
			'district'   =>$pcd['2'],
			'address'    =>$addr,
			'type'       =>$type1,
			'layout'     =>$layout,
			'total_area' =>$area,
			'area'       =>$total_area,
			'people'     =>$type2+1,
			'build'      =>$area,
			'is_mobile'  =>2,
			);

		$data = M('xgj_users_houses')->create($array);

		$house_id = M('xgj_users_houses')->where(['user_id'=>$userId,'is_mobile'=>2])->getField('house_id');
		if (empty($house_id)) {
			$re = M('xgj_users_houses')->data($data)->add();
			if ($re > 0) die($re);
		}else{
			$re = M('xgj_users_houses')->where(['house_id'=>$house_id])->data($data)->save();
			if ($re !== false) die($house_id);
		}
		die('报价失败');
    }

    public function info(){

		$id     = I('get.id');
		$userId = $_SESSION['user']['userId'];
		$cid    = I('get.cid');

		if (!quoteId($cid)) $this->error('请选择系统',U('Index/index'));

    	$re = M('xgj_users_houses')->where(['user_id'=>$userId,'house_id'=>$id,'is_mobile'=>2])->find();

    	$house = $_SESSION['mobile']['info'];

    	if (empty($re) || empty($house)) $this->redirect('price',['cid'=>$cid]);
    	
    	$re = $this->getQuote($cid, 1, $id,false ,false);

    	if ($re == 'error') $this->error('抱歉，此系统不适用您的房屋，请电话咨询或更换系统');

    	$quote = M('xgj_furnish_quote')->field('gift,sale')->where(array('quote_id'=>$cid))->find();
   		
   		$sale = $quote['sale'];
   		$gift = $quote['gift'];

		$money = ceil(($re['all']-$re['install'])*$sale/100+$re['install']);
		
		/*系统总金额-系统的优惠金额*返回积分的比例得到返还积分数值*/
		$gift  = floor(($money - ($re['all']-$money)) * $gift / 100);
		
   		$_SESSION['mobile']['info']['money'] = $money;			//应付总价
   		$_SESSION['mobile']['info']['cost']  = $re['install'];	//安装费
   		$_SESSION['mobile']['info']['gift']  = $gift;			//返还积分
   		$_SESSION['mobile']['info']['sn']    = $re['allStr'];	//材料清单

    	$this->assign('cid',$cid);
    	$this->assign('money',$money);
    	$this->assign('house',$house);
    	$this->assign('house_id',$id);
    	$this->assign('header','房屋信息');
    	$this->display();
    }

    //查询省市县三级联动
    public function area(){
    	$pid = I('get.pid');
    	$return = M('xgj_area')->where("pid=$pid")->field('id,name')->select();
    	echo json_encode($return);
    }



    /**
     *  报价系统处理
     *  $cid 		系统ID
     *  $lv  		系统型号
     *  $houseId    房间ID   如果房屋信息不再默认的表内，请传数组，格式为array('tableName'=>表名,'where'=>查询条件)
     *  $dump       true的时候开启下载EXL，false关闭   默认false
     *  $is_list    true的时候开启展示材料列表，false关闭   默认true
     */
    function getQuote($cid=null , $lv=1, $houseId=null ,$dump=false ,$is_list=true){
		if (!empty($_POST['cid'])) 		$cid     = I('post.cid');
		if (!empty($_POST['lv']))  		$lv      = I('post.lv');
		if (!empty($_POST['houseId']))  $houseId = I('post.houseId');
		if (!empty($_POST['dump'])) 	$dump    = I('post.dump');
		if (!empty($_POST['is_list']))  $is_list = I('post.is_list');
		/**************报价公式处理*****start*********/
		$money = M('xgj_quote_child_list q')->join('xgj_furnish_goods g ON q.goods_sn = g.goods_sn')->field('q.*,g.goods_name,g.shop_price,g.goods_model,g.goods_id,g.goods_sn,g.goods_brand,g.goods_img,g.goods_lv,g.goods_unit,g.features,g.origin')->where("q.quote_id = $cid and q.level = $lv")->order('sort')->select();

		//没有该系统跳转至家居首页
		if(empty($money)) $this->redirect('index');
		
		$host  = 0;		//初始化主机总数
		$price = 0; 	//初始化系统总价

		if (is_array($houseId)) {
			$userHouseData = M($houseId['tableName'])->where($houseId['where'])->find();
		}else{
			$userHouseData = M('xgj_users_houses')->where("house_id=$houseId")->find();
		}

		//计算阳台面积
		$balconyAreaArray = explode('|', $userHouseData['area']);
		$balconyArea = array_sum(explode(',', $balconyAreaArray['4']));

		//房屋总面积 去掉阳台面积
		$totalArea = $userHouseData['total_area'] - $balconyArea;

		$judgeCity = M('xgj_quote_judge')->where("city='{$userHouseData['city']}'")->find();

		foreach ($money as $key => $value) {
			
			/********************************************/
			//初始化浮点类型的0
			if (!empty($value['singlelt']) && $value['singlelt']=='0.00')  $value['singlelt'] = 0;
			if (!empty($value['singlegt']) && $value['singlegt']=='0.00')  $value['singlegt'] = 0;
			if (!empty($value['minarea'])  && $value['minarea'] =='0.00')  $value['minarea']  = 0;
			if (!empty($value['maxarea'])  && $value['maxarea'] =='0.00')  $value['maxarea']  = 0;
			if (!empty($value['housemax']) && $value['housemax'] =='0.00') $value['housemax'] = 0;
			if (!empty($value['housemin']) && $value['housemin'] =='0.00') $value['housemin'] = 0;
			/********************************************/
			$xxnum = 0;
			$error = true;

			//判断南北城市
			if (!empty($value['judge']) && $value['judge'] != $judgeCity['judge']) 										$error = false;
			//判断房屋类型
			if (!empty($value['house_type']) && !in_array($userHouseData['type'],explode(',', $value['house_type']))) 	$error = false;
			//判断常住人口
			if (!empty($value['peoplemax']) && $userHouseData['people'] > $value['peoplemax']) 							$error = false;
			if (!empty($value['peoplemin']) && $userHouseData['people'] <= $value['peoplemin']) 						$error = false;
			//判断房屋面积
			if (!empty($value['housemax']) && $totalArea > $value['housemax']) 											$error = false;
			if (!empty($value['housemin']) && $totalArea <= $value['housemin']) 										$error = false;
			
			//满足以上条件进入报价计算公式
			if (!empty($value['formula']) && $error == true){

				/****************获取房屋信息**************/
				$house_data = explode('|',$userHouseData['area']);

				$attic      = empty($house_data['6'])?0:explode(',',$house_data['6']);   //阁楼
				$basement   = empty($house_data['7'])?0:explode(',',$house_data['7']);   //地下室
				
				$bedroom    = explode(',',$house_data['0']);		//卧室
				//将阁楼地下室放入卧室内
				if (!empty($house_data['6'])) $bedroom = array_merge($bedroom,explode(',',$house_data['6']));
				if (!empty($house_data['7'])) $bedroom = array_merge($bedroom,explode(',',$house_data['7']));

				$living     = explode(',',$house_data['1']);		//客厅
				
				$kitchen    = explode(',',$house_data['2']);		//厨房
				
				$toilet     = explode(',',$house_data['3']);		//卫生间
				
				$balcony    = explode(',',$house_data['4']);		//阳台
				
				$area       = $totalArea;	        				//房屋面积
				/*******************************************/

				/*****************获取关联项的数据*******start*************/
				if ($value['guanlian']=='bedroom') 								$xx = $bedroom;
				else if ($value['guanlian']=='living')  						$xx = $living;
				else if ($value['guanlian']=='kitchen') 						$xx = $kitchen;
				else if ($value['guanlian']=='toilet')  						$xx = $toilet;
				else if ($value['guanlian']=='balcony') 						$xx = $balcony;

				elseif(preg_match("/^\d+$/", $value['guanlian'])){
					//如果关联的是材料获取关联材料的数据
					foreach ($list as $k => $v) {
						if ($v['id'] == $value['guanlian']) {
							$xxnum = $v['num'];
						}
					}
					//如果关联的是材料出现面积大于或者小于时判断的是整个房间去除阳台的面积
					$xxarea = $area;	
				}else if ($value['guanlian']=='area' || empty($value['guanlian'])){
					$xx = $area;
					$xxnum  = count($xx);
					$xxarea = $area;
				}else if($value['guanlian']=='complex'){
					//关联多项材料
					$relation = explode('|', $value['complex']);
					$complex = 0;
					foreach ($list as $k1 => $v1) {
						foreach ($relation as $k2 => $v2) {
							if ($v1['id'] == $v2) {
								$complex += $v1['num'];
							}
						}
					}
					$xxnum   = $complex;
					$xxarea  = $area;
				}else if($value['guanlian']=='room'){

					//关联房屋
					$room  = explode('|', $value['room']);

					$rooms = array();
					if ($room['0'] ==1) $rooms = array_merge($rooms,$bedroom);
					if ($room['1'] ==1) $rooms = array_merge($rooms,$living);
					if ($room['2'] ==1) $rooms = array_merge($rooms,$kitchen);
					if ($room['3'] ==1) $rooms = array_merge($rooms,$toilet);
					if ($room['4'] ==1) $rooms = array_merge($rooms,$balcony);
					if ($room['5'] ==1) {
						$livingCopy = $living;
						$livingCopy['0'] = $livingCopy['0']+array_sum($kitchen);
						$rooms = array_merge($rooms,$livingCopy);
					}

					$xx 	 = $rooms;
					$xxnum   = count($rooms);
					$xxarea  = array_sum($rooms);
				}else{
					$xxnum = count($xx);
					$xxarea  = array_sum($xx);
				}
				/*********************** end *****************************/


				/*************判断面积和数量是否符合要求*****start********/
				//如果满足条件为true 不满足为false   $return1为面积   $return2为数量   $return4为单个面积   
				$return1 = false;	
				
				if (!empty($value['maxarea']) && empty($value['minarea'])  && $xxarea <= $value['maxarea']) 								   	$return1 = true;
				if (empty($value['maxarea'])  && !empty($value['minarea']) && $xxarea >  $value['minarea']) 								   	$return1 = true; 
				if (!empty($value['maxarea']) && !empty($value['minarea']) && $xxarea >  $value['minarea'] && $xxarea <= $value['maxarea']) 	$return1 = true;
				if (empty($value['maxarea'])  && empty($value['minarea'])) 																   		$return1 = true;


				$return2 = false;

				if (!empty($value['maxnum']) && empty($value['minnum'])  && $xxnum <= $value['maxnum']) 										$return2 = true;
				if (empty($value['maxnum'])  && !empty($value['minnum']) && $xxnum >  $value['minnum']) 										$return2 = true;
				if (!empty($value['maxnum']) && !empty($value['minnum']) && $xxnum >  $value['minnum'] && $xxnum <= $value['maxnum']) 			$return2 = true;
				if (empty($value['maxnum'])  && empty($value['minnum'])) 																		$return2 = true;
				/************************* end ***************************/


				/****************获取卧，客，卫，厨，阳总面积****start*****************/
				//2为材料面积  1为数量
				if ($value['numorarea'] == 2) {
					$bedroom = array_sum($bedroom);
					$living  = array_sum($living);
					$kitchen = array_sum($kitchen);
					$toilet  = array_sum($toilet);
					$balcony = array_sum($balcony);
					empty($rooms)?'':$room = array_sum($rooms);
				}else{
					$bedroom = count($bedroom);
					$living  = count($living);
					$kitchen = count($kitchen);
					$toilet  = count($toilet);
					$balcony = count($balcony);
					empty($rooms)?'':$room = count($rooms);
				}
				/**************************** end **************************************/



				/**************************** start **************************************/
				//判断关联材料房屋数量是否满足条件
				$return5 = true;

				if (!empty($value['typenumgt'])) {
					$typenum   = explode('|', $value['typenumgt']);
					$typenumgt = $typenum['0'];
					$decide    = explode(',', $typenum['1']);
					$room = 0;
					if ($decide['0'] ==1) $room += $bedroom;
					if ($decide['1'] ==1) $room += $living;
					if ($decide['2'] ==1) $room += $kitchen;
					if ($decide['3'] ==1) $room += $toilet;
					if ($decide['4'] ==1) $room += $balcony;
					
					if ($typenumgt <= $room) $return5 = false;
				}
				/**************************** end **************************************/

				if ($return1 == true && $return2 == true && $return5 == true) {

					$host += $value['host'];	//主机总数

					/*****************计算单个面积平均等于多少的总数*******start*************/
					if (!empty($rooms) && !empty($value['average'])) {
						$averageNum = 0;
						foreach ($rooms as $k => $v) {
							$averageNum += ceil($v/$value['average']);
						}
						$room = $averageNum;
					}
					/*********************** end ********************************************/

					/*****************计算数量等于多少的总数*******start*************/
					if (!empty($rooms) && !empty($value['usenum'])) {
						$finalNum = 0;
						if ($value['usenum'] == count($rooms)) {
							$str = $value['formula'];
							$finalNum = eval("return $str;");
						}
					/*********************** end ********************************************/

					/*****************计算单个面积获取数量*******start*************/
					}else if (!empty($value['singlelt']) || !empty($value['singlegt'])) {
						$singleNumList = 0;
						foreach ($xx as $k => $v) {
							$return4 = false;
							if (!empty($value['singlelt']) && empty($value['singlegt'])  && $v <= $value['singlelt']) 								$return4 = true;
							if (empty($value['singlelt'])  && !empty($value['singlegt']) && $v >  $value['singlegt']) 								$return4 = true;
							if (!empty($value['singlelt']) && !empty($value['singlegt']) && $v >  $value['singlegt'] && $v <= $value['singlelt']) 	$return4 = true;

							if ($return4 == true) {
								$singleNumList += eval("return {$value['formula']};");
							}
						}
						$finalNum = $singleNumList;
					}else if (!empty($value['average_num']) && $averageNum != $value['average_num']) {
						$finalNum = 0;
					}else if (preg_match("/^\d+$/", $value['guanlian'])){
						/******************************************/
						//如果是关联单个材料的话计算数量
						if ($xxnum==0) $finalNum = 0;
						else if (preg_match("/^[0-9]+$/", $value['gongshi'])) $finalNum = $value['gongshi'];
						else {
							$str = $xxnum.$value['gongshi'];
							$finalNum = eval("return $str;");
						}
						if (substr($value['formula'], 0,4)=='ceil')  $finalNum = ceil($finalNum);
						if (substr($value['formula'], 0,5)=='floor') $finalNum = floor($finalNum);
						/******************************************/
					}else{
						$str = $value['formula'];
						$finalNum = eval("return $str;");
					}

					/****************************************************/
					//对公式的结果进行追加公式计算
					if (!empty($finalNum) && !empty($value['append'])) {
						$str = $finalNum.$value['append'];
						$finalNum = eval("return $str;");
					}
					/****************************************************/



					/****************************************************/
					//对公式的结果进行取整判断
					if (!empty($finalNum) && !empty($value['gongshiceil'])) {
						$finalNum = ceil($finalNum/$value['gongshiceil'])*$value['gongshiceil'];
					}
					/****************************************************/

					/*********************** end *****************************/
				}
				
				/******************获取数据集********start**********/
				//如果有自定义数量 判断自定义数量是否满足条件 如不满足数量为0
				if (!empty($value['maxcustom']) || !empty($value['mincustom'])) {
					$return3 = false;
					$countCustom = array_sum($custom);	//所有自定义数量和
					if (!empty($value['maxcustom']) && empty($value['mincustom'])  && $countCustom <= $value['maxcustom']) $return3 = true;
					if (empty($value['maxcustom'])  && !empty($value['mincustom']) && $countCustom >  $value['mincustom']) $return3 = true;
					if (!empty($value['maxcustom']) && !empty($value['mincustom']) && $countCustom >  $value['mincustom'] && $countCustom <= $value['maxcustom']) $return3 = true;
					if ($return3==false) $finalNum=0;
				}
				
				if ($return1 == true && $return2 == true && $return5 == true && $finalNum > 0) {
					$list[$key]['id']        = $value['child_id'];									//材料ID
					$list[$key]['name']      = $value['goods_name'];								//材料名称
					$list[$key]['model']     = $value['goods_model'];								//型号
					$list[$key]['level']     = $value['level'];										//1经济型 2舒适型 3豪华型
					$list[$key]['sn']        = $value['goods_sn'];									//唯一识别码
					$list[$key]['price']     = $value['shop_price'];								//单价
					$list[$key]['money']     = $value['shop_price'];								//单价
					$list[$key]['batch']     = $value['batch'];										//批次
					$list[$key]['num']       = $finalNum;											//数量
					$list[$key]['host']      = $value['host'];										//是否是主机
					$list[$key]['brand']     = $value['goods_brand'];								//品牌
					$list[$key]['img']       = $value['goods_img'];									//图片
					$list[$key]['unit']      = $value['goods_unit'];								//单位
					$list[$key]['lv']        = $value['goods_lv'];									//1主材 2辅材 3安装费
					$list[$key]['type']      = $value['type'];										//分类
					$list[$key]['origin']    = $value['origin'];									//产地
					$list[$key]['features']  = $value['features'];									//特性

					$list[$key]['maxcustom'] = $value['maxcustom'];									//自定义小于使用
					$list[$key]['mincustom'] = $value['mincustom'];									//自定义大于使用
					$custom[]                = $value['custom']*$finalNum;							//自定义
					
					
				}
				/********************************** end ************/
			}
		}


		/****************start*******************/
		//如果数据集或者主机总数为空，零 返回页面并提示
		if (empty($list) || empty($host)) {
			if (!empty($adjust_id) || !empty($customerId)) {
				return 'error';exit;
			}
		}
		/*******************end******************/
		foreach ($list as $k => $v) {
			if ($v['level'] >= 1 && $v['level'] <= 4) {
				$data[$v['level']][]   	= $v;							//所有材料数据集
				$dataSn[$v['level']][] 	= $v['sn'];						//所有材料的编码
				$dataNum[$v['level']][] = $v['num'];					//所有材料的数量
				$dataBatch[$v['level']][] = $v['batch'];				//所有材料的批次
				//初始化该系统类型总价
				if (empty($priceAll[$v['level']])) $priceAll[$v['level']] = 0;			
				$priceAll[$v['level']] += $v['price']*$v['num'];		//总价
				//初始化安装费
				if (empty($install[$v['level']])) $install[$v['level']] = 0;		
				if ($v['lv'] == 3) $install[$v['level']] += $v['price']*$v['num'];	//安装费
		
				if ($v['lv'] != 2) $mainData[$v['level']][]   	= $v;	//出去辅材的材料数据集
			}
		}


		/*****************start********************/
		//对材料编码和数量进行处理并组成字符串
		foreach ($dataSn as $k => $v) {
			$sn      = implode(',',$v);
			$num     = implode(',',$dataNum[$k]);
			$batch   = implode(',',$dataBatch[$k]);
			$all[$k] = $sn.';'.$num.';'.$batch;
		}
		/****************** end *******************/


		$count  = count($all);	//获取该系统类型的实际数量
		$yprice = $priceAll;	//给该系统类型实际总价赋值


		/***************** start ********************/
		//将没有的系统类型填上0
		for ($i=1; $i < 4 ; $i++) { 
			if (empty($all[$i])) $all[$i]           = '0';
			if (empty($priceAll[$i])) $priceAll[$i] = '0';
		}
		/******************  end  *******************/


		//对补上0的数组内的键值排序
		ksort($all);
		ksort($priceAll);


		/***************** start ********************/
		//让系统价格数组内的键值从0开始排序  此步骤为了产品清单等页面使用，这些页面在活的报价系统出现之前就存在，根据这些页面对数据的需求重新排序
		for ($i=1; $i < count($priceAll) ; $i++) { 
			$priceAlls[$i-1]['all'] = ceil($priceAll[$i]);
			$priceAlls[$i-1]['install'] = ceil($install[$i]);
		}
		/******************  end  *******************/

		//提取需要的系统
		for ($i=1; $i < 4 ; $i++) { 
			if ($i==$lv) {
				$allStr = $all[$i];
				$allPrice = $priceAlls[$i-1];
			}
		}
		//将该系统（材料编码和数量）数组转换成一个字符串
		// $allStr = implode('|', $opAll);

		if ($dump==false) {
			//将数组放入SESSION中并跳转页面
			// $_SESSION['quote']['data']     = $data;
			// $_SESSION['quote']['count']    = $count;
			$_SESSION['quote']['allStr']   = $allStr;
			$_SESSION['quote']['allPrice'] = $allPrice;

			$result = $allPrice;
			$result['allStr'] = $allStr;
			if ($is_list == true) $result['list'] = $mainData[$lv];
			return $result;exit;
		}

		/**************报价公式处理*****end***************/



		
		/**************Excel*****start*********************/

		/****************************/
		//查询系统名称并处理
		$name = M('xgj_furnish_quote')->where("quote_id=".$cid)->getField('quote_name');
		
		/****************************/
		


		/****************************/
		//查询系统分类并处理
		$typeData = M('xgj_quote_type')->where("quote_id = $cid")->select();
		foreach ($typeData as $k => $v) {
			$type[$v['id']] = $v['text'];
		}
		/****************************/


		/****************************/
		//处理要导出的数据并导出
		foreach ($data[$lv] as $k => $v) {
			$lists[$k][] = $type[$v['type']];
			$lists[$k][] = '\''.$v['sn'];
			$lists[$k][] = $v['name'];
			$lists[$k][] = $v['model'];
			$lists[$k][] = $v['brand'];
			$lists[$k][] = $v['price'];
			$lists[$k][] = $v['num'];
		}
		$exlData['data']  = $lists;
		$exlData['key']   = array('分类','编码','材料名称','型号','品牌','单价','数量');
		$exlData['width'] = array('B'=>'25','C'=>'15','D'=>'30','E'=>'20','F'=>'20');
		exl($exlData,$name);
		/**************exl*****end*********/
		
	}

}