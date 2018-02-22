<?php
require_once(WWW_DIR."/model/xgj_furnishlist.php");
require_once(WWW_DIR."/model/xgj_furnish.php");
/*
*健康舒适家居页面
*/
class furnish{
	function host($area){
		global $str;
		$area-=230;
			if($area>0){
				$this->host($area);
			}else{
				$str=230+$area;
			}
			return $str;
		}
		
/*
* @param $cid 分类id
* @param $type 系统类型
* @param $area 面积
* @param $MBR 主卧
* @param $MBR_NUM 主卧数量
* @param $BR 次卧
* @param $BR_NUM 次卧数量
* @param $LR 客厅
* @param $LR_NUM 客厅数量
* @param $WR 化妆间
* @param $WR_NUM 化妆间数量
* @param $KR 厨房
* @param $KR_NUM 厨房数量
* @param $GR 阳台
* @param $GR_NUM 阳台数量
* @param $TP 房型
* @return $result 报价
*/
	 function get_price_final($cid=0,$type=0,$area=0,$MBR=0,$MBR_NUM=0,$BR=0,$BR_NUM=0,$LR=0,$LR_NUM=0,$WR=0,$WR_NUM=0,$KR=0,$KR_NUM=0,$GR=0,$GR_NUM=0,$TP=0){
		if($_GET['quote_id']){
			$cid=$_GET['quote_id'];
		}else{
			echo " 非常抱歉，无法得到您所选的类型！";
			return false;
		}
		if($_GET['level']){
			$type=$_GET['level'];
		}
		$gg=new furnishlist();
		$re=$gg->getInfoFromId($cid,$type);
		$rs=$gg->hostmax();
		if(!empty($area)){
			if($area>230){
			$num=floor($area/230);
			//var_dump($re);exit;
			$absarea=$this->host($area);
			//echo $absarea;
			foreach ($re as $k=>$v){
				if($v['formula']){
				if($v['minarea']<$absarea&&$v['maxarea']>=$absarea){//通过面积判断主机型号 id
						$res[]='('.$v['formula'].')'.'*'.$v['promote_price'];//选择面积相关的产品
						$pid[]=$v['quote_id'];
					}
					//.$gg->getChildrenIds($v['id']); 
				}
			}
			//var_dump($pid);exit;
			foreach($rs as $k=>$v){
				$res[]='('.$v['formula'].')'.'*'.$v['promote_price'].'*'.$num;
			}
			
			foreach($re as $v){
				if($v['formula']){
					if(empty($v['maxarea'])){
						 foreach($pid as $v){
						$res['child']='('.substr($gg->getChildId($v['id']),1).')'.'*'.($num+1);//得到父类id下所有子类公式的方法
						 }
					}
				}
			}
					foreach($re as $v){
				if(empty($v['maxarea'])&&$v['chf_id']=="[0]"){
							$res[]='('.$v['formula'].')'.'*'.$v['promote_price'];
						 } 
			}
			// var_dump($res);
			// echo "<br/>";
			// var_dump($pid);
			// exit;
			if(!empty($res)){
			$result=implode('+',$res);//通过"+"号连接成字符串
			//var_dump($result);exit;
			//echo "<br/>";
				$paramater=array("/area/","/MBRA/","/MBR_NUM/","/BRA/","/BR_NUM/","/LRA/","/LR_NUM/","/WRA/","/WR_NUM/","/KRA/","/KR_NUM/","/GRA/","/GR_NUM/");//被替换的内容
				$variable=array("$area","$MBR","$MBR_NUM","$BR","$BR_NUM","$LR","$LR_NUM","$WR","$WR_NUM","$KR","$KR_NUM","$GR","$GR_NUM");//替换的内容
				
				$str=preg_replace($paramater,$variable,$result);//使用正则替换
				//var_dump($str);exit;
				$exp=$str;
				$key=array('+','-','*','/','(',')');//将四则运算符放入数组
				foreach($key as $v){
				$exp=str_replace($v,','.$v.',',$exp);//替换数组内的四位运算符
				$array=array();
				}
				
				//var_dump($exp);exit;
				$arr=explode(',',$exp);//将结果分割成数组
				foreach($arr as $v){
					if(!empty($v)){
						$array[]=$v;
					}
				}
				//var_dump($array);exit;
				$arr_exp = array();
				for($i=0;$i<count($array);$i++){
					$arr_exp[] = $array[$i];
				} 
					$result = $gg->calcexp( array_reverse($arr_exp) );//calcexp将数组内的数值通过四则运算得到结果
					if(!empty($result)){
					echo $str . '=' . $result;  exit;
					}else{
						echo "系统出错啦！";
					return false;}
			}else{
				echo "无法得到匹配的数据！";
				return false;
			}
				//var_dump($res);exit;
	/*----面积超过230平房屋end--------------------------------------------------------------------------------------------------------*/
			}else{
				//var_dump($re);exit;
				foreach ($re as $k=>$v){
				if($v['formula']){
					if($v['minarea']<$area&&$v['maxarea']>=$area){
						$res[]='('.$v['formula'].')'.'*'.$v['promote_price'];//选择面积相关的产品
						$pid[]=$v['quote_id'];
					}
					//.$gg->getChildrenIds($v['id']);
				}
			}
				
			// var_dump($res);
			// echo "<br/>";
			// var_dump($pid);
			// exit;
			foreach($re as $v){
				if($v['formula']){
					if(empty($v['maxarea'])){
						 foreach($pid as $v){
						$res['child']=substr($gg->getChildrenIds($v['id']),1);
						 }
					}
				}
			}
			foreach($re as $v){
				if(empty($v['maxarea'])&&$v['chf_id']==0){
							$res[]='('.$v['formula'].')'.'*'.$v['promote_price'];
						 } 
			}
			// var_dump($res);
			// echo "<br/>";
			// var_dump($pid);
			// exit;
			if(!empty($res)){
			$result=implode('+',$res);
			//var_dump($result);exit;
			//echo "<br/>";
				$paramater=array("/area/","/MBRA/","/MBR_NUM/","/BRA/","/BR_NUM/","/LRA/","/LR_NUM/","/WRA/","/WR_NUM/","/KRA/","/KR_NUM/","/GRA/","/GR_NUM/");//被替换的内容
				$variable=array("$area","$MBR","$MBR_NUM","$BR","$BR_NUM","$LR","$LR_NUM","$WR","$WR_NUM","$KR","$KR_NUM","$GR","$GR_NUM");//替换的内容
				
				$str=preg_replace($paramater,$variable,$result);//使用正则替换
				//var_dump($str);exit;
				$exp=$str;
				$key=array('+','-','*','/','(',')');
				foreach($key as $v){
				$exp=str_replace($v,','.$v.',',$exp);
				$array=array();
				}
				
				//var_dump($exp);exit;
				$arr=explode(',',$exp);
				foreach($arr as $v){
					if(!empty($v)){
						$array[]=$v;
					}
				}
				//var_dump($array);exit;
				$arr_exp = array();
				for($i=0;$i<count($array);$i++){
					$arr_exp[] = $array[$i];
				} 
					$result = $gg->calcexp( array_reverse($arr_exp) );
					if(!empty($result)){
					echo $str . '=' . $result;  exit;
					}else{
						echo "系统出错啦！";
					return false;}
			}else{
				echo "无法得到匹配的数据！";
				return false;
			}
			//$p=implode(';',$price);
			//var_dump($price);
			//exit;
			}
		 } 
	 }
	 
	 //可以删
	// function category(){
		
	// 	$cat_id=$_GET['cat_id'];//得到地址栏传值
		
	// 	$gg=new furnishlist();
		
	// 	$cate=$gg->getcategory($cat_id);//通过分类id得到分类名称
		
	// 	$product=$gg->getproduct($cat_id);//通过分类id得到该分类下的产品
				
	// 	$tpl = get_smarty();
		
	// 	$tpl->assign("cate",$cate);//模板传值
		
	// 	$tpl->assign("product",$product);//模板传值
		
	// 	$tpl->display('furnishlist.tpl.html');
		
	// }
	
	function quote(){
		
		$quote_id=$_GET['quote_id'];//得到地址栏传值
		
		$cate_id=$quote_id;
		
		$tpl = get_smarty();
		
		$gg=new furnishlist();
		
		$str=file_get_contents('js/area_data.js');
		
		$data=json_decode($str);
		
		$quote=$gg->getproductinfo($quote_id);
		
		if(!empty($_COOKIE['pre_order'])){
			$array=explode(";", $_COOKIE['pre_order']);
			foreach ($array as $v){
				$pre_order[] = explode("|", $v);
			}
			$tpl->assign('pre_order',$pre_order);
		}
		
		$tpl->assign('data',$data);
		
		$tpl->assign('quote',$quote);
		
		$tpl->assign('quote_id',$quote_id);
		
		$tpl->assign('cate_id',$cate_id);
		
		$tpl->display('quote.tpl.html');
	}
	
	function quote1(){
		user_check_logon();
		$tpl = get_smarty();
		$tpl->display('quote1.tpl.html');
	}

	function area_select(){
		
		$aid=$_GET["aid"];
		
		$str=file_get_contents('js/area_data.js');
		
		$data=json_decode($str);
		
		$arr=$data->a->{$aid};
		
		echo  "[";
		for($i=0;$i<count($arr)-1;$i++){
			echo '{"text":"'.$arr[$i][0].'",'.'"value":"'.$arr[$i][1].'"},'."";
		}
		echo  '{"text":"'.$arr[count($arr)-1][0].'",'.'"value":"'.$arr[count($arr)-1][1].'"}';
		echo  "]";
	}
	//得到报价价格
	function get_price(){
		
		$cid=$_GET['cate_id'];
		
		$quote_id=trim($_POST['id']);

		if(isset($_POST)){
		
		$house_type=$_POST['HouseType'];
		
		$area=$_POST['HouseArea'];
	
		$m_room=$_POST['Room'];
		
		$room=0;
		
		$living=$_POST['Living'];
		
		$w_room=$_POST['Wash'];
		
		$veranda=$_POST['Gallery'];
		
		}
		
		$gg=new furnishlist();
		
		$pid=$gg->getparentid($cid);
		
		$cat=$pid['parent_id'];
		
		$tpl = get_smarty();
		
		switch ($pid['parent_id']){
			
			case 1 :
			
			$allprice=$gg->getwindquote($cid,$area,$m_room,$room,$living,$w_room,$veranda);
			//var_dump($allprice,'bb');
			
			$tpl->assign('price',$allprice);
			
			$tpl->assign('id',$quote_id);
			
			$tpl->assign('cat',$cat);
			
			$tpl->display('quote_result.tpl.html');
			
			break;
				
			case 2 :
			
			$arr=$gg->getwaterquote($cid,$house_type);
			
			$allprice=array(); //商品总价的数组
			
			if(!empty($area)&&$area>0){
			
			for($i=0;$i<count($arr);$i++){
			
			$price=array();
			
			$price[]=array_product(explode('||',$arr[$i]['prefilter']));
			
			$price[]=array_product(explode('||',$arr[$i]['c_water_prefilter']));
			
			$price[]=array_product(explode('||',$arr[$i]['c_softner']));
			
			$price[]=array_product(explode('||',$arr[$i]['ultrafilter']));
			
			$price[]=array_product(explode('||',$arr[$i]['water_prefilter']));
			
			$price[]=array_product(explode('||',$arr[$i]['hot_drink']));
			
			$price[]=array_product(explode('||',$arr[$i]['assist']));
			
			$price[]=array_product(explode('||',$arr[$i]['soft_salt']));
			
			$allprice[]=ceil(array_sum($price));
			
				}
			
			}else{
				$allprice[]=0;
			}
			//var_dump($allprice,'aa');
			$tpl->assign('price',$allprice);
			
			$tpl->assign('id',$quote_id);
			
			$tpl->assign('cat',$cat);

			$tpl->display('quote_result.tpl.html');
			
			break;

		}	
}
	function pre_order(){
		$gg=new furnishlist();
		$pre_id=intval($_GET['pre_id']);
		
		$price=trim($_GET['price']);
		
		$quote_id=trim($_GET['id']);
		
		$quote=$gg->getproductinfo($quote_id);
		
		if(empty($_COOKIE['pre_order'])){
			$content=$pre_id.'|'.$quote_id.'|'.$price.'|'.$quote['cat_name'];
		}else{
            $str=$pre_id.'|'.$quote_id.'|'.$price.'|'.$quote['cat_name'];
            $content = $_COOKIE['pre_order'].';'.$str;
		}
		//var_dump($content);exit;
        setcookie("pre_order",$content,time()+3600*7*24);
        echo lan_aa();exit;
        //header("location:?pre_order_list"); exit;

        if(isset($_COOKIE['pre_order'])){
        	$array=explode(";", $_COOKIE['pre_order']);
        	foreach ($array as $v){
        		$pre_order[] = explode("|", $v);
        	} 
        	//var_dump($pre_order);exit;
        echo	$heredoc = 
'<div class="collect-tip" id="collect-tip" >
    <div class="collect-tip-bk">
        <div class="collect-tip-titile">
        	我的健康舒适家
        </div>
        
        <div class="collect-tip-shopping">
        	<div class="collect-tip-shopping-title">
            	预购总汇
            </div>
        
            <div class="collect-tip-shopping-table">
            	<div class="collect-tip-shopping-table-title">
                	<div class="collect-tip-shopping-table-title-01">
                    	系统名称
                    </div>
        
                    <div class="collect-tip-shopping-table-title-02">
                    	总价（单位：元）
                    </div>
        
                    <div class="collect-tip-shopping-table-title-03">
                    	抵扣券（单位：元）
                    </div>
        
                    <div class="collect-tip-shopping-table-title-04">
                    	操作
                    </div>
        
                    <div class="clear"></div>
                </div>
        
                <div class="clear"></div>';
        foreach($pre_order as $v){
             echo  $heredoc.=" <div class='collect-tip-shopping-table-list'>
                	<div class='collect-tip-shopping-table-list-01'>
                	{$v[2]}
                    </div>
        
                    <div class='collect-tip-shopping-table-list-02'>
                    	{$v[1]}元
                    </div>
        
                    <div class='collect-tip-shopping-table-list-03'>
                    	2000
                    </div>
        
                    <div class='collect-tip-shopping-table-list-04'>
                    	<a href=''>
                        	移除
                        </a>
                    </div>
        
                    <div class='clear'></div>
                </div>
        
                <div class='clear'></div>";
        }
        
             echo $heredoc.='   <div class="collect-tip-shopping-table-collect">
                	<div class="collect-tip-shopping-table-collect-01">
                    	总计
                    </div>
        
                    <div class="collect-tip-shopping-table-collect-02">
                    	12000
                    </div>
        
                    <div class="collect-tip-shopping-table-collect-03">
                    	4000
                    </div>
                </div>
            </div>
        
            <div class="collect-tip-shopping-buy">
            	<a href="#">
                	放进购物车
                </a>
            </div>
        </div>
       <div class="clear2"></div>
    </div>
</div>';	
        }else{
        echo	'<div class="collect-tip" id="collect-tip" >
    <div class="collect-tip-bk">
        <div class="collect-tip-titile">
        	我的健康舒适家
        </div>
        	
        <div class="collect-tip-shopping">
        	<div class="collect-tip-shopping-title">
            	预购总汇
            </div>
        	
            <div class="collect-tip-shopping-table">
            	<div class="collect-tip-shopping-table-title">
                	<div class="collect-tip-shopping-table-title-01">
                    	系统名称
                    </div>
        	
                    <div class="collect-tip-shopping-table-title-02">
                    	总价（单位：元）
                    </div>
        	
                    <div class="collect-tip-shopping-table-title-03">
                    	抵扣券（单位：元）
                    </div>
        	
                    <div class="collect-tip-shopping-table-title-04">
                    	操作
                    </div>
        	
                    <div class="clear"></div>
                </div>
        	
                <div class="clear"></div>
 				<div class="collect-tip-shopping-table-list">
        		<div class="collect-tip-shopping-table-list-01">
        		'.$quote['cat_name'].'
        		</div>
        	
        		<div class="collect-tip-shopping-table-list-02">
        		'.$price.'元
        		</div>
        	
        		<div class="collect-tip-shopping-table-list-03">
        		2000
        		</div>
        	
        		<div class="collect-tip-shopping-table-list-04">
        		<a href="">
        		移除
        		</a>
        		</div>
        	
        		<div class="clear"></div>
        		</div>
        	
        		<div class="clear"></div>
   				<div class="collect-tip-shopping-table-collect">
                	<div class="collect-tip-shopping-table-collect-01">
                    	总计
                    </div>
        	
                    <div class="collect-tip-shopping-table-collect-02">
                    	12000
                    </div>
        	
                    <div class="collect-tip-shopping-table-collect-03">
                    	4000
                    </div>
                </div>
            </div>
        	
            <div class="collect-tip-shopping-buy">
            	<a href="#">
                	放进购物车
                </a>
            </div>
        </div>
       <div class="clear2"></div>
    </div>
</div>';
        }
        //echo  $heredoc;
        
		/* $tpl = get_smarty();
		
		$tpl->assign('content',$content);
		
		$tpl->display('quote.tpl.html'); */
	}
	function pre_order_list(){
		
		if(!empty($_COOKIE['pre_order'])){
			$array=explode(";", $_COOKIE['pre_order']);
			foreach ($array as $v){
				$pre_order[] = explode("|", $v);
			}
			$heredoc = <<<END
<div class="collect-tip" id="collect-tip" style="display: none;">	
    <div class="collect-tip-bk">	
        <div class="collect-tip-titile">
        	我的健康舒适家
        </div>        
        
        <div class="collect-tip-shopping">
        	<div class="collect-tip-shopping-title">
            	预购总汇
            </div>
            
            <div class="collect-tip-shopping-table">
            	<div class="collect-tip-shopping-table-title">
                	<div class="collect-tip-shopping-table-title-01">
                    	系统名称
                    </div>
                    
                    <div class="collect-tip-shopping-table-title-02">
                    	总价（单位：元）
                    </div>
                    
                    <div class="collect-tip-shopping-table-title-03">
                    	抵扣券（单位：元）
                    </div>
                    
                    <div class="collect-tip-shopping-table-title-04">
                    	操作
                    </div>
                    
                    <div class="clear"></div>
                </div>
                
                <div class="clear"></div>
                
                <div class="collect-tip-shopping-table-list">
                	<div class="collect-tip-shopping-table-list-01">
                    	中央除尘
                    </div>
                    
                    <div class="collect-tip-shopping-table-list-02">
                    	8000
                    </div>
                    
                    <div class="collect-tip-shopping-table-list-03">
                    	2000
                    </div>
                    
                    <div class="collect-tip-shopping-table-list-04">
                    	<a href="#">
                        	移除
                        </a>
                    </div>
                    
                    <div class="clear"></div>
                </div>
                
                <div class="clear"></div>
                
                <div class="collect-tip-shopping-table-list">
                	<div class="collect-tip-shopping-table-list-01">
                    	循环热水（壁挂炉）
                    </div>
                    
                    <div class="collect-tip-shopping-table-list-02">
                    	9000
                    </div>
                    
                    <div class="collect-tip-shopping-table-list-03">
                    	2000
                    </div>
                    
                    <div class="collect-tip-shopping-table-list-04">
                    	<a href="#">
                        	移除
                        </a>
                    </div>
                    
                    <div class="clear"></div>
                </div>
                
                <div class="clear"></div>
                
                <div class="collect-tip-shopping-table-collect">
                	<div class="collect-tip-shopping-table-collect-01">
                    	总计
                    </div>
                    
                    <div class="collect-tip-shopping-table-collect-02">
                    	12000
                    </div>
                    
                    <div class="collect-tip-shopping-table-collect-03">
                    	4000
                    </div>
                </div>
            </div>
            
            <div class="collect-tip-shopping-buy">
            	<a href="#">
                	放进购物车
                </a>
            </div>
        </div>
       <div class="clear2"></div> 
    </div>
</div>
			
			
END;
			echo  $heredoc;
			
		}
	}
	
	/**
	 * 删除购物车商品
	 */
	function del(){
		$key=intval($_GET['key']);
		
		$quote_id=$_GET['q_id'];
		
		$string=explode(";", $_COOKIE['pre_order']);
		
		foreach ($string as $v){
			
			$pre_orders[] = explode("|", $v);
			
		}
		unset($pre_orders[$key]);
		
		$pre_order=arr2str($pre_orders,"|",";");
		
		setcookie("pre_order",$pre_order,time()+3600*7*24);
		
		header("location:?quote_id=$quote_id"); exit;
	}
	

	//健康家居列表页面
	function furnishList(){
		
		$tpl = get_smarty();
		//获取值
		$id=intval($_GET['id']);
		$cat_id=intval($_GET['cat_id']);
		//实例化数据模型
		$furnishOb=new furnishlist();
		//获取系统列表
		$data=$furnishOb->get_furnish_list($id,$cat_id);
		//var_dump($data);exit;
		//页面传值
		$tpl->assign("id",$id);
		$tpl->assign("list",$data['list']);
		$tpl->assign("content",$data['info']);
		$pn= new home();
		$eu_tree=$pn->category(1);
		$tpl->assign("eu_tree", $eu_tree);
		//显示页面
		$tpl->display("homelist.tpl.html");		
	}
	//功能介绍页面产品列表
	function content(){
		$id=trim($_GET['id'],"'");
		//var_dump($id);exit;
		//实例化数据模型
		$furnishOb=new furnishlist();
		//获取系统列表
		$data=$furnishOb->get_quote_list($id);
		$html='';
		foreach ($data as $k=>$v){
			$html.="<div class='home_furnish-center-right-product-demo'><!--循环该demo-->
            	<div class='home_furnish-center-right-product-demo-img'>
                	<a href='price.php?cid=".$v['quote_id']."'>
                    	<img src='".$v['img']."'/>
                    </a>
                </div>
                <div class='home_furnish-center-right-product-demo-name' title='".$v['quote_name']."'>
                	<a href='price.php?cid=".$v['quote_id']."'>".$v['quote_name']."
                    </a>
                </div>
                <div class='home_furnish-center-right-product-demo-a'>
                	<a href='price.php?cid=".$v['quote_id']."'>
                    	了解详情
                    </a>
                </div>
            </div>";
		}
	echo $html;
	}

	//机电攻略
	function strategy()
	{
		$tpl = get_smarty();
	
		$tpl->display('strategy.tpl.html');
	}
	/**
	 *	跳转404页面
	 **/
	public function error(){
		$tpl = get_smarty();
		$tpl->display('error.tpl.html');
	}
}

?>

	