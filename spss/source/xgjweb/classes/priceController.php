<?php

/**
 * @package WWW
 * @see feed_center, user_cace, photo_lib, notification_center, user_application, user_relations, user_register
 */
require_once(WWW_DIR . "/model/price_model.php");
include_once(WWW_DIR . "/Excel/PHPExcel.php");

class priceController {
    
	function get_price($adjust=null,$adjust_id=null,$lv=null,$dump=false,$tableName=null){

		$priceOb = new price();
		$money = $priceOb->money($adjust,$lv);
		

		//没有该系统跳转至家居首页
		// if(empty($money)) $this->redirect('index');
		
		$host  = 0;		//初始化主机总数
		$price = 0; 	//初始化系统总价

		if (!empty($tableName)) {
			$userHouseData = $priceOb->find($tableName,"id = $adjust_id");
			$userHouseData['type_area']    = $userHouseData['area'];
			$userHouseData['house_type']   = $userHouseData['type'];
			$userHouseData['house_layout'] = $userHouseData['layout'];
		}else{
			$userHouseData = $priceOb->getDealerAdjustInfo($adjust_id);
		}

		//计算阳台面积
		$balconyAreaArray = explode('|', $userHouseData['type_area']);
		$balconyArea = array_sum(explode(',', $balconyAreaArray['4']));

		//房屋总面积 去掉阳台面积
		$totalArea = $userHouseData['total_area'] - $balconyArea;


		$judgeCity = $priceOb->judgeCity($userHouseData['city']);

		foreach ($money as $key => $value) {
			/********************************************/
			//初始化浮点类型的0
			if (!empty($value['singlelt']) && $value['singlelt']=='0.00') $value['singlelt'] = 0;
			if (!empty($value['singlegt']) && $value['singlegt']=='0.00') $value['singlegt'] = 0;
			if (!empty($value['minarea'])  && $value['minarea'] =='0.00') $value['minarea']  = 0;
			if (!empty($value['maxarea'])  && $value['maxarea'] =='0.00') $value['maxarea']  = 0;
			if (!empty($value['housemax']) && $value['housemax'] =='0.00') $value['housemax'] = 0;
			if (!empty($value['housemin']) && $value['housemin'] =='0.00') $value['housemin'] = 0;
			/********************************************/
			$xxnum = 0;
			$error = true;

			//判断南北城市
			if (!empty($value['judge']) && $value['judge'] != $judgeCity['judge']) 										$error = false;
			//判断房屋类型
			if (!empty($value['house_type']) && !in_array($userHouseData['house_type'],explode(',', $value['house_type']))) 	$error = false;
			//判断常住人口
			if (!empty($value['peoplemax']) && $userHouseData['people'] > $value['peoplemax']) 							$error = false;
			if (!empty($value['peoplemin']) && $userHouseData['people'] <= $value['peoplemin']) 						$error = false;
			//判断房屋面积
			if (!empty($value['housemax']) && $totalArea > $value['housemax']) 											$error = false;
			if (!empty($value['housemin']) && $totalArea <= $value['housemin']) 										$error = false;
			
			//满足以上条件进入报价计算公式
			if (!empty($value['formula']) && $error == true){

				/****************获取房屋信息**************/
				$house_data = explode('|',$userHouseData['type_area']);

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
					
					if ($typenumgt <= $room) $return5 == false;
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

					//*****************计算数量等于多少的总数*******start*************/
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
					$list[$key]['sort']      = $value['sort'];									    //排序
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
				//初始化辅材费用
				if (empty($fuCai[$v['level']])) $fuCai[$v['level']] = 0;		
				if ($v['lv'] == 2) $fuCai[$v['level']] += $v['price']*$v['num'];	//辅材
		
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
			if (empty($all[$i]))      $all[$i]      = '0';
			if (empty($priceAll[$i])) $priceAll[$i] = '0';
			if (empty($install[$i]))  $install[$i]  = '0';
			if (empty($fuCai[$i]))    $fuCai[$i]    = '0';
		}
		/******************  end  *******************/


		//对补上0的数组内的键值排序
		ksort($all);
		ksort($priceAll);

		/***************** start ********************/
		//让系统价格数组内的键值从0开始排序  此步骤为了产品清单等页面使用，这些页面在活的报价系统出现之前就存在，根据这些页面对数据的需求重新排序
		for ($i=1; $i < count($priceAll) ; $i++) { 
			$priceAlls[$i-1]['all']     = ceil($priceAll[$i]);
			$priceAlls[$i-1]['install'] = ceil($install[$i]);
			$priceAlls[$i-1]['fuCai']   = ceil($fuCai[$i]);
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
		// $allstring = implode('|', $all);

		if ($dump==false) {
			$lists['1'] = $allStr;
			$lists['2'] = $allPrice;
			$lists['3'] = $count;
			if (!empty($tableName)) $lists['4'] = $mainData[$lv];
			return $lists;
		}
		
		/**************报价公式处理*****end*********/



		/**************Excel*****start*********/
		if ($dump == true) {

			$filename = $priceOb->filename($adjust);

			/****************************/
			//查询系统分类并处理
			$typeData = $priceOb->feilei($adjust);
			foreach ($typeData as $k => $v) {
				$type[$v['id']] = $v['text'];
			}
			/****************************/


			/*************************************/
			//处理PAD设置后需要导出的数据
			$userSetup = array('1','1','1','1','1','1','1');

	        if (!empty($customerId)) {
				$userId    = $userHouseData['u_id'];
				$userData  = $priceOb->userData($userId);
				$userSetup = explode('|', $userData['setup']);
				//图片 品牌 名称 产地 型号 单价
	        }
	        /*************************************/

			$name='Excelfile'; 
			include_once(WWW_DIR . "/Excel/PHPExcel.php");
		    $objPHPExcel = new PHPExcel();   
		    // var_dump($allPrice);exit;
		    if ($dump == true) {

		    	$customerData = $mainData[$lv];
		    	$totalMoneys = $allPrice['all'];
		   		
		   		$fucaiMoney = 0;
		   		foreach ($customerData as $k => $v) {
		   			if ($v['lv'] != '2') {
		   				$customerDatas[] = $v;
		   			}
		   			if ($v['lv'] == '2') {
		   				$fucaiMoney += $v['money']*$v['num'];
		   			}
		   		}

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
	    		
		    	$objPHPExcel->setActiveSheetIndex(0)
			    	->setCellValue('A1', $filename['quote_name'])
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
		                  'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER)					 
					 )
		        );

		        $objPHPExcel->getActiveSheet(0)->getStyle('A2')->applyFromArray(
		            array(
						  'font' => array ('bold' => true, 'size' => 14),
		                  'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER)					 
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
					$objPHPExcel->getActiveSheet()->getStyle('A'.$iii)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$iii++;
					$i=1;
					foreach($v as $key=>$val){								
							$objPHPExcel->getActiveSheet(0)->getRowDimension($iii)->setRowHeight(30);
							$objPHPExcel->getActiveSheet()->getStyle($iii)->applyFromArray(
									array(
										  'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER)					 
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
				 	->setCellValue("I".$iii, ceil($fuCai[$lv]))
				 	->setCellValue("B".($iii+1), '合计')
				 	->setCellValue("I".($iii+1), ceil($totalMoneys));
				$objPHPExcel->getActiveSheet(0)->getRowDimension($iii)->setRowHeight(30);
				$objPHPExcel->getActiveSheet()->getStyle($iii)->applyFromArray(
									array(
										  'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER)					 
									 )
							 );
				$objPHPExcel->getActiveSheet(0)->getRowDimension($iii+1)->setRowHeight(30);
				$objPHPExcel->getActiveSheet()->getStyle($iii+1)->applyFromArray(
									array(
										  'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER)					 
									 )
							 );

				// $objPHPExcel->getActiveSheet()->setTitle("$dataName"); 
			    $objPHPExcel->setActiveSheetIndex(0);   
			    ob_end_clean() ;
			    header('Content-Type: application/vnd.ms-excel');
			    header('Content-Disposition: attachment;filename='.$filename['quote_name']."材料清单表".'.xls');
			    header('Cache-Control: max-age=0');
			    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			    $objWriter->save('php://output');
			}

			/**************exl*****end*********/
			

		}
	    
	    
	    

	}
}