<?php
namespace Home\Controller;

class FurnishController extends BaseController {

    
    public function index(){
    	$list = M('xgj_furnish_quote')->field('quote_name,quote_id')->where(['is_putaway'=>1])->select();

		$map['is_on']     = 1;
		$map['ad_pos_id'] = 13;
		$img = M('xgj_ad')->where($map)->getField('image');
		$this->assign('img',getimage($img)); 

        $this->assign('list',$list); 
        $this->display();
    }

    public function detail(){
    	$quote_id = I('get.quote_id');
    	//查询系统信息
    	$info = M('xgj_furnish_quote')->field('puzzled,product,video_img,video')->where(array('quote_id' => $quote_id))->find();
    	//系统信息不存在返回
 		if(empty($info)) {
 			layout(false);
 			$this->error('对不起，该系统不存在!');
 		}
 		//获取省信息
    	$area = getPCD();
    	$this->assign('info',$info); 
    	$this->assign('area',$area); 
        $this->display();
    }

    public function quote(){

    	$qId = I('get.quote_id');
    	$lv  = I('get.lv');

    	/*************************************/
    	//查看系统是否存在，不存在返回家居首页
    	if (empty($qId) || empty($lv)) $this->redirect('index');
    	$map = array(
			'quote_id'	=>$qId,
			'level'		=>$lv,
			);
    	$is = M('xgj_quote_child_list')->where($map)->find();
    	if (empty($is)) $this->redirect('index');
    	/*************************************/

    	//获取房屋信息
    	$return['house'] = M('xgj_users_houses')->where(array('user_id'=>$_SESSION['user']['userId'],'is_mobile'=>1))->select();
    	//查询省级城市
    	$return['area'] = getPCD();

    	if (!empty($return['house'])) {
    		//处理房屋信息
    		$return['houseInfo'] = houseInfo($return['house']['0']);
    		if ($_SESSION['house_id']=='')  $_SESSION['house_id'] 	= $return['house'][0]['house_id'];

    		if (!empty($_GET['quote_id'])) 	$_SESSION['quote_id'] 	= $qId;
    		else 							$_GET['quote_id'] 		= $_SESSION['quote_id'];

    		if (!empty($_GET['lv'])) 	   	$_SESSION['lv'] 		= $lv;
    		else 							$_GET['lv'] 			= $_SESSION['lv'];

    		//获取系统信息
    		$return['quote'] = M('xgj_furnish_quote')->where(array('quote_id'=>$_SESSION['quote_id']))->getField('sale');
    		//获取系统报价
    		$return['price'] = $this->getQuote($_SESSION['quote_id'],$_SESSION['lv'],$_SESSION['house_id']);
    	}else{
    		unset($_SESSION['house_id']);
    	}

    	$this->assign('return',$return); 	
        $this->display();
    }

    //房屋信息
    public function house(){
    	layout(false);

    	/*******************************/
    	//还没登录的时候先将数据暂时先放入session内，登录后删除
    	if (empty($_SESSION['user']['userId'])) {
    		$_SESSION['houseInfo'] = $_POST;
    		$this->redirect('User/login');
    	}
    	
    	if (empty($_POST) && !empty($_SESSION['houseInfo'])) {
    		$_POST = $_SESSION['houseInfo'];
    		unset($_SESSION['houseInfo']);
    	}
    	/*******************************/

    	$house = I('post.house');
    	if (empty($house) && !empty($_SESSION['user']['userId'])) {
    		$house = M('xgj_users_houses')->where(array('user_id'=>$_SESSION['user']['userId'],'is_mobile'=>1))->order('house_id')->limit(1)->getField('house_id');
    	}
    	
    	//给$_SESSION['house_id']赋值 用于前台JS查询房屋信息
    	$_SESSION['house_id'] = $house;
    	
    	//查看信息是否填写完整
    	$error = false;
    	if (!empty($_POST['room1']) && array_null($_POST['room1'])==true || count($_POST['room1']) != $_POST['select1']) $error = true;
    	else $room1 = implode(',',$_POST['room1']);
    	if (!empty($_POST['room2']) && array_null($_POST['room2'])==true || count($_POST['room2']) != $_POST['select2']) $error = true;
    	else $room2 = implode(',',$_POST['room2']);
    	if (!empty($_POST['room3']) && array_null($_POST['room3'])==true || count($_POST['room3']) != $_POST['select3']) $error = true;
    	else $room3 = implode(',',$_POST['room3']);
    	if (!empty($_POST['room4']) && array_null($_POST['room4'])==true || count($_POST['room4']) != $_POST['select4']) $error = true;
    	else $room4 = implode(',',$_POST['room4']);
    	if (!empty($_POST['room5']) && array_null($_POST['room5'])==true || count($_POST['room5']) != $_POST['select5']) $error = true;
    	else $room5 = implode(',',$_POST['room5']);
    	if (!empty($_POST['room6']) && array_null($_POST['room6'])==true || count($_POST['room6']) != $_POST['select6']) $error = true;
    	else $room6 = implode(',',$_POST['room6']);
    	if (!empty($_POST['room7']) && array_null($_POST['room7'])==true || count($_POST['room7']) != $_POST['select7']) $error = true;
    	else $room7 = implode(',',$_POST['room7']);

		if (empty($_POST['province']) || empty($_POST['city']) || empty($_POST['district']) || empty($_POST['address']) || empty($_POST['type']) || empty($_POST['people']) || $error == true) $this->error('请认真填写信息并填写完整');

    	//每个房屋面积
    	$area = $room1.'|'.$room2.'|'.$room3.'|'.$room4.'|'.$room5.'|'.$room6.'|'.$room7;

    	//几室几厅
    	$layout = I('post.select1').','.I('post.select2').','.I('post.select3').','.I('post.select4').','.I('post.select5').','.I('post.select6').','.I('post.select7');
    	
    	//全部面积
    	$total_area = array_sum($_POST['room1'])+array_sum($_POST['room2'])+array_sum($_POST['room3'])+array_sum($_POST['room4'])+array_sum($_POST['room5'])+array_sum($_POST['room6'])+array_sum($_POST['room7']);

    	$people = I('post.people');

    	if(!preg_match("/^[0-9]*$/", $people) && $people < 1){
		    $this->error('常住人口只能为整数');
		}
    	
		//处理添加或修改的数据
    	$houseData = array(
			'user_id'    =>$_SESSION['user']['userId'],
			'province'   =>M('xgj_area')->where(array('id'=>I('post.province')))->getField('name'),
			'city'       =>M('xgj_area')->where(array('id'=>I('post.city')))->getField('name'),
			'district'   =>M('xgj_area')->where(array('id'=>I('post.district')))->getField('name'),
			'address'    =>I('post.address'),
			'type'       =>I('post.type'),
			'layout'     =>$layout,
			'total_area' =>$total_area,
			'area'       =>$area,
			'people'     =>I('post.people'),
    		); 

    	
    	$count  = M('xgj_users_houses')->where(array('user_id'=>$_SESSION['user']['userId'],'is_mobile'=>1))->count();			//查询现在几条房屋信息

    	/*******************************/
    	//判断是否登录状态下保存房屋信息是添加还是修改
    	$is_new = I('post.is_new');

    	if (!empty($is_new)) {
    		//登录状态下
    		if ($is_new == 2 || $count>=6) $dispose = 'save';
	    	else if ($is_new == 1) 		   $dispose = 'add';
    	}else{
    		//未登录状态下
    		if ($count>=6) 				   $dispose = 'save';
	    	else 		   				   $dispose = 'add';
    	}
    	/*******************************/



    	/*******************************/
    	//执行添加或者修改
    	if ($dispose == 'save') {
    		$re = M('xgj_users_houses')->where(array('house_id'=>$house))->save($houseData);
    	} else if ($dispose == 'add') {
    		$re = M('xgj_users_houses')->add($houseData);
    		//如果是添加 将$_SESSION['house_id']重新赋值
	    	$_SESSION['house_id'] = $re;
    	}
    	/*******************************/

    	$_SESSION['quote_id'] = I('post.quote_id');
    	$_SESSION['lv']		  = I('post.lv');

    	$this->redirect('quote',array('quote_id'=>$_SESSION['quote_id'],'lv'=>1));
    }

    //查询省市县三级联动
    public function area(){
    	$id = $_GET['v'];
    	$return = M('xgj_area')->where("pid=$id")->field('id,name')->select();
    	echo json_encode($return);
    }


    public function selectHouse(){
    	$id = I('get.id');
    	$data = M('xgj_users_houses')->where("house_id=".$id)->find();
    	$_SESSION['house_id']=$id;
		$re['data']    = houseInfo($data);
		$re['area']    = getPCD();
		$re['price']   = $this->getQuote($_SESSION['quote_id'],$_SESSION['lv'],$id);
		$re['sale']    = M('xgj_furnish_quote')->where("quote_id=".$_SESSION['quote_id'])->getField('sale');
		$re['service'] = $this->service($data);
    	echo json_encode($re);
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

    public function services(){
    	foreach ($_POST as $k => $v) {
    		$id[]=$v;
    		$pcd[] = M('xgj_area')->where('id='.$v)->getField('name');
    	}
    	$data = implode('-', $pcd);
    	$_SESSION['pcd']['p']=$pcd[0];
    	$_SESSION['pcd']['c']=$pcd[1];
    	$_SESSION['pcd']['d']=$pcd[2];
    	$_SESSION['pcd']['pv']=$id[0];
    	$_SESSION['pcd']['cv']=$id[1];
    	$_SESSION['pcd']['dv']=$id[2];
    	$re = $this->service('',$data);
    	echo  $re;
    }

    //加入购物车
    public function goCart(){

    	/***************************/
    	//查询系统是否存在
    	$map = array(
    		'quote_id'=>$_SESSION['quote_id']
    		);

    	$quote = M('xgj_furnish_quote')->field('sale,quote_name,img')->where($map)->find();
    	/***************************/

    	if (!empty($quote)) {

    		/***************************/
    		//查询购物车内是否已存在此系统
    		$maps = array(
				'user_id'  =>$_SESSION['user']['userId'],
				'cat_id'   =>$_SESSION['quote_id'],
				'house_id' =>$_SESSION['house_id'],
				);

    		$re = M('xgj_furnish_cart')->where($maps)->count();
		    /***************************/

			if ($re == '0') {
		    	$house = M('xgj_users_houses')->where("house_id = ".$_SESSION['house_id'])->find();
		    	$data= array(
					'user_id'         =>$_SESSION['user']['userId'],
					'cat_id'          =>$_SESSION['quote_id'],
					'material'        =>$_SESSION['quote']['allStr'],
					'discount_amount' =>0,
					'price'           =>ceil(($_SESSION['quote']['allPrice']['all']-$_SESSION['quote']['allPrice']['install'])/100*$quote['sale'])+$_SESSION['quote']['allPrice']['install'],
					'shop_name'       =>$quote['quote_name'],
					'house_id'        =>$_SESSION['house_id'],
					'homebill_num'    =>$_SESSION['lv'],
					'cost'            =>$_SESSION['quote']['allPrice']['install'],
					'area'            =>$house['area'],
					'total_area'      =>$house['total_area'],
					'people'		  =>$house['people'],
					'city'            =>$house['province'].'-'.$house['city'].'-'.$house['district'].'-'.$house['address'],
					'house_layout'    =>$house['layout'],
					'house_type'      =>$house['type'],
					'class'           =>1,
					'img'             =>$quote['img'],
					);

				$return = M('xgj_furnish_cart')->add($data);
				if ($return>0) echo '1';
				else echo '添加失败';
			}else{
				echo '购物车内该系统已存在';
			}
    	}else{
    		echo '没有该系统';
    	}
    }


    public function exec(){
    	layout(false);
		$quote_id = I('quote_id');
		$lv       = I('lv');
		$house_id = I('house_id');
		$dump 	  = true;
    	$this->getQuote($quote_id,$lv,$house_id,$dump);
    }

    public function materialList(){
		$quote_id = I('quote_id');
		$lv       = I('lv');
		$house_id = I('house_id');
		$dump 	  = false;

		$map['quote_id'] = $quote_id;
		$quoteName = M('xgj_furnish_quote')->where($map)->getField('quote_name');

    	$list = $this->getQuote($quote_id,$lv,$house_id,$dump);
    	//分页  
		$count 	 = count($list['list']);    //总数
		$pageNum = 10;						//每页显示数
		$page  	 = getPage($count,$pageNum);	 

		//获取当前分页数
		$p = I('get.p');	
		$pmax = ceil($count/$pageNum);
		if ($p<1 || empty($p)) $p = 1;
		else if($p>$pmax) 	   $p = $pmax;

		//截取当前分页对应的分页数据
		$start = $p*$pageNum;
		$data = array_slice($list['list'], $start-$pageNum,$pageNum);
	   
		$this->assign('quoteName',$quoteName);
		$this->assign('page',$page['page']);
		$this->assign('list',$data);
		$this->display();
    }

    /**
     *  报价系统处理
     *  $cid 		系统ID
     *  $lv  		系统型号
     *  $houseId    房间ID   如果房屋信息不再默认的表内，请传数组，格式为array('tableName'=>表名,'where'=>查询条件)
     *  $dump       true的时候开启下载EXL，false关闭   默认false
     *  $is_list    true的时候开启展示材料列表，false关闭   默认true
     */
    function getQuote($cid=null , $lv=1, $houseId=null ,$dump=false ,$is_list=true ,$compare=false){

    	if (!empty($_POST['data'])) {
    		$post_data = json_decode($_POST['data'],true);
			$cid     = $post_data['cid'];
			$lv      = $post_data['lv'];
			$houseId = $post_data['houseId'];
			$dump    = $post_data['dump'];
            $is_list = $post_data['is_list'];
			$compare = $post_data['compare'];
    	}
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

        if ($cid == 100) {
            foreach ($compare as $k => $v) {
                foreach ($v['list'] as $key => $val) {
                    $compareList[] = $val;
                }
            }
        }

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

                    if ($cid == 100 && empty($xxnum)) {
                        foreach ($compareList as $k => $v) {
                            if ($v['id'] == $value['guanlian']) {
                                $xxnum = $v['num'];
                            }
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
					$xxarea  = array_sum($rooms);

                    // $xxnum = count($rooms);     6-26改
                    if ($xxarea!=0) $xxnum = count($rooms);
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

					// empty($rooms)?'':$room = array_sum($rooms);  //6-26 改
                    $room = empty($rooms)?'0':array_sum($rooms);
				}else{
                    $bedroom = array_sum($bedroom)!=0?count($bedroom):0;
                    $living  = array_sum($living) !=0?count($living):0;
                    $kitchen = array_sum($kitchen)!=0?count($kitchen):0;
                    $toilet  = array_sum($toilet) !=0?count($toilet):0;
                    $balcony = array_sum($balcony)!=0?count($balcony):0;
                    $room    = array_sum($rooms)  !=0?count($rooms):0;
                    
                    //6-26 改
					// $bedroom = count($bedroom);
					// $living  = count($living);
					// $kitchen = count($kitchen);
					// $toilet  = count($toilet);
					// $balcony = count($balcony);
					// empty($rooms)?'':$room = count($rooms);
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
			if (empty($all[$i])) $all[$i]           = '0';
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
			$priceAlls[$i-1]['all'] = ceil($priceAll[$i]);
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
		// $allStr = implode('|', $opAll);

		if ($dump==false || $dump==3) {
			//将数组放入SESSION中并跳转页面
			$_SESSION['quote']['data']     = $data;
			// $_SESSION['quote']['count']    = $count;
			$_SESSION['quote']['allStr']   = $allStr;
			$_SESSION['quote']['allPrice'] = $allPrice;

			$result = $allPrice;
			$result['allStr'] = $allStr;

			if ($dump == 3) {
				$result['name']          = M('xgj_furnish_quote')->where(["quote_id"=>$cid])->getField('quote_name');
				$result['customerId']    = $customerId;
				$result['userHouseData'] = $userHouseData;
			}

			if ($is_list == true) $result['list'] = $mainData[$lv];

			if (!empty($_POST['data'])) {
				echo json_encode($result);exit;
			}

			return $result;exit;
		}
		

		/**************报价公式处理*****end***************/



		
		/**************Excel*****start*********************/

		/****************************/
		//查询系统名称并处理
		$name = M('xgj_furnish_quote')->where(["quote_id"=>$cid])->getField('quote_name');
		
		/****************************/

		/****************************/
		//查询系统分类并处理
		$typeData = M('xgj_quote_type')->where(['quote_id'=>$cid])->select();
		foreach ($typeData as $k => $v) {
			$type[$v['id']] = $v['text'];
		}
		/****************************/

		if ($dump == 2) {
			$filename = $name;

			/*************************************/
			//处理PAD设置后需要导出的数据
			$userSetup = array('1','1','1','1','1','1','1');

	        if (!empty($customerId)) {
				$userId    = $userHouseData['u_id'];
				$sql = "select * from pad_user where id=".$userId;
				$userData  = M('pad_user')->where(['id'=>$userId])->find();
				$userSetup = explode('|', $userData['setup']);
				//图片 品牌 名称 产地 型号 单价
	        }
	        /*************************************/

			$name='Excelfile'; 

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
    		vendor('Excel.PHPExcel');
    		$objPHPExcel = new \PHPExcel();
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
			 	->setCellValue("I".$iii, ceil($fuCai[$lv]))
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
		    header('Content-Type: application/vnd.ms-excel');
		    header('Content-Disposition: attachment;filename='.$filename."材料清单表".'.xls');
		    header('Cache-Control: max-age=0');
		    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		    $objWriter->save('php://output');exit;
		}



		


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