<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>			
		<meta charset="utf-8" />
		<meta name="renderer" content="webkit"  />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>上品上生</title>
		<link rel="shortcut icon" href=" /spss/source/spss/Public/img/ico.ico" /> 
		<link rel="stylesheet" href="/spss/source/spss/Public/css/public.css" />
		<link rel="stylesheet" type="text/css" href="/spss/source/spss/Public/css/headfoot.css"/>
		<script src="/spss/source/spss/Public/js/jquery-1.11.1.js"></script>
		<!--[if (gte IE 6)&(lte IE 8)]>
		      <script type="text/javascript" src="/spss/source/spss/Public/js/selectivizr.js"></script>
		      
		<![endif]-->
	</head>
	<body> 
		<div class="home-nav">
		<div id="changeH">
		   <p id="changeLine""></p>
			<ul class="nav-bg">
				
					<li class="home-nav-logo"><a href="/"><img src="/spss/source/spss/Public/img/newlogo.png" alt="" /></a></li>
					<li class="nav-daohang"><a href="<?php echo U('Furnish/index');?>">家用机电</a><span class="btmLine"></span></li>
					<li class="nav-hover-left nav-daohang " index="1" onmousemove="homeloginhover(this)" onmouseleave="homeloginleave(this)">
						<a href="<?php echo U('Material/floorList');?>" class="home-hover-left home-nav-list">品质建材</a>
						<div class="home-nav-hover  home-nav-hover-left-1" >
							<a href="<?php echo U('Material/floorList',array('pId'=>198));?>">无醛地板</a>
							<a href="<?php echo U('Material/floorList',array('pId'=>100));?>">厨房卫浴</a>
						</div>
						<span class="btmLine">
					</li>
					<li class="nav-hover-left nav-daohang nav-yanchi" index="2" onmousemove="homeloginhover(this)" onmouseleave="homeloginleave(this)">
						<a href="<?php echo U('Customer/index');?>"  class="home-hover-left home-nav-list">售后服务</a>
						<div class="home-nav-hover home-nav-hover-left-2">
							<a href="/spss/source/spss/index.php/Customer/upkeep.html">机电保养</a>
							<a href="/spss/source/spss/index.php/Customer/consumable.html">机电耗材</a>
							<a href="/spss/source/spss/index.php/Customer/maintain.html">机电维修</a>
						</div>
						<span class="btmLine">
					</li>
					<li class="nav-daohang"><a href="<?php echo U('Knowledge/index');?>">家居知识</a><span class="btmLine"></li>
					
				
					<li class="nav-daohang"><a href="<?php echo U('Index/aboutus');?>">关于我们</a><span class="btmLine"></li>
					<li class="nav-daohang"><a href="/spss/source/spss/index.php/Index/show.html">全国体验馆</a><span class="btmLine"></li>
					<!-- <li class="nav-daohang"><a href="javascript:;"onclick="qimoChatClick();">在线客服</a></li> -->
				
				
					<li class="kfdh"><span class="service-number"></span><span>400-800-1027</span></li>
					
				
				<div class="home-nav3">
						<li class="home-nav3-li2 home-nav3-li1 nav-yanchi" index="3" onmousemove="homeloginhover(this)" onmouseleave="homeloginleave(this)">
							<a href="#"></a>
							<div class="home-login-hover home-input-hover home-nav-hover-left-3">
								<form action="<?php echo U('Index/search');?>">
									<input type="text" name='search' value='' placeholder="请输入..."/>
									<input type="submit" class="home-input-hover-seach" value="搜索">
								</form>
							</div>
						</li>
						<li class="home-nav3-li2 nav-yanchi" index="4" onmousemove="homeloginhover(this)" onmouseleave="homeloginleave(this)">
							<a  href="javascript:;" >
							<div class="home-login-hover home-nav-hover-left-4">
								<?php if (!empty($_SESSION['user']['userId'])): ?>
									<a class="home-denglu-hou" href="<?php echo U('User/outLogin');?>">退出登录</a>
									<a class="home-denglu-hou" href="/spss/source/spss/index.php/User/changePsd.html">修改密码</a>
									<a class="home-denglu-hou" href="/spss/source/spss/index.php/User/address.html">我的地址</a>
									<a class="home-denglu-hou" href="<?php echo U('User/homeOrder');?>">我的订单</a>
									<a class="home-denglu-hou" href="/spss/source/spss/index.php/User/concern.html">我的收藏</a>
									<a class="home-denglu-hou" href="<?php echo U('User/index');?>">个人中心</a>
								<?php else: ?>
									<a class="home-denglu-qian home-denglu-qian01"  href="<?php echo U('User/login');?>">登录</a>
									<a class="home-denglu-qian home-denglu-qian02"  href="<?php echo U('User/register');?>">注册</a>
								<?php endif ?>
							</div>
							</a>
						</li>
						<li class="home-nav3-li3" onmousemove="homeshopcarhover()" onmouseleave="homeshopcarleave()">
							<a href="<?php echo U('Cart/index');?>"></a>
							
								<div class="nav3-shopcar">
								<?php if (!empty($cartData)): ?>
									<p class="leftright leftright1" onclick="qiehuan1()"></p>
									<p class="leftright leftright2" onclick="qiehuan2()"></p>
									<div class="nav-shop">
										<?php foreach ($cartData as $k => $v): ?>
											<div class="nav-shop1">
												<p class="nav-shop-p nav-shop-p1"><img class="nav-shop-p1-img" src="<?php echo (getimage($v["img"])); ?>" alt="" /></p>
												<p class="nav-shop-p nav-shop-p2"><?php echo ($v["shop_name"]); ?></p>
												<p class="nav-shop-p nav-shop-p3">
													<span class="nav-shop-p3-tit">价格</span>
													<span class="nav-shop-p3-con"><a><?php echo ($v["price"]); ?></a>元</span>
												</p>
												<p class="nav-shop-p nav-shop-p3"> 
													<span class="nav-shop-p3-tit">数量</span>
													<span class="nav-shop-p3-con"><a><?php echo ($v["num"]); ?></a>个</span>
												</p>
											</div>
										<?php endforeach ?>
											
									</div>
									<div class="nav3-jiesuan">
										<p class="nav3-jiesuan-p nav3-jiesuan-p1"></p>
										<p class="nav3-jiesuan-p nav3-jiesuan-p2">共计<span><?php echo ($cartNum); ?></span>件商品</p>
										<p class="nav3-jiesuan-p nav3-jiesuan-p3">合计<span><?php echo ($cartPrice); ?></span>元</p>
										<a class="nav3-jiesuan-p nav3-jiesuan-p4" href="<?php echo U('Cart/index');?>">去购物车结算</a>
									</div>
									<?php  else: ?>
										<span style="color:#5c6670;font-size:14px;">您的购物车是空的</span>
									<?php endif ?>
								</div>
							
						</li>
						
				</div>
			</ul>
			</div>
		</div>
		<!--<div class=" home-login-bg-hover" id="navkongbai"></div>-->
		<?php if (!empty($cartData)): ?>
			<div class=" home-login-bg-hover home-shopcar-kong" id="navshopcar" onmousemove="homeshopcarhover()" onmouseleave="homeshopcarleave()"></div>
		<?php endif ?>

		<script type="text/javascript">
//		导航购物车的右切换
			function qiehuan2(){
				var leng=$(".nav-shop1").length;
				var size=$(".nav-shop").css("right");
				size=parseInt(size.replace(/[^0-9]/ig,""));
				leng=(Math.ceil(leng/2)-1)*1200;
				var y = parseInt(size/1200); 
				var z = size/1200;
				if(size<leng && y==z){
					size=size+1200;
					$(".nav-shop").animate({right:size+"px"},600);
				}		
			}
//		导航购物车的左切换
			function qiehuan1(){
				var leng=$(".nav-shop1").length;
				var size=$(".nav-shop").css("right");
				size=parseInt(size.replace(/[^0-9]/ig,""));	
				var y = parseInt(size/1200); 
				var z = size/1200;
				if(size>0 && y==z){
					size=size-1200;
					$(".nav-shop").animate({right:size+"px"},600);
				}		
			}
		</script>
 <link rel="stylesheet" href="/spss/source/spss/Public/css/baojia.css" />
  <!--main-->
<div class="main">
	<div class="content">
		<form action='<?php echo U("house");?>' method='post'>
			<div class="content-head">
				<div class="content-head-main">
					<div>房屋信息</div>
					<div>
					<?php foreach ($return['house'] as $k => $v): ?>
						<li><label><input style="" class='houseList' onclick='selectHouse(<?php echo ($v["house_id"]); ?>)' type="radio" name="house" <?= $v['house_id']==$_SESSION['house_id']?'checked':''; ?>  value='<?php echo ($v["house_id"]); ?>'/><?= $v['province'].$v['city'].$v['district'].$v['address'] ?></label></li>
					<?php if ($k==2): ?>
					</div>
					<div>
					<?php endif ?>
					<?php endforeach ?>
					</div>
					<?php if (count($return['house']) < 6 && !empty($_SESSION['user']['userId'])): ?>
						<div class="jiaxinxi" onclick='newHouse()'>
							<img src="/spss/source/spss/Public/img/jiahao.png">
						</div>
					<?php endif ?>
				</div>
			
			</div>
			<div class="baojia-jingquebaojia">
				<img src="/spss/source/spss/Public/img/baojia-fengzhi.jpg" />
			</div>
		
			<div class="baojia-sel">
				<div class="baojia-sel-left">
					<div class="baojia-sel-left-top" >
						<div class="baojia-home1">
							<div>
								<select required class='prov' name="province" onchange='area("city",this.value)'>
									<option value="" >-请选择-</option>
									<?php foreach ($return['area'] as $key => $v): ?>
										<option value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?></option>
									<?php endforeach ?>
								</select>
								<span>省</span>
							</div>
							<div>
								<select required class='city' name="city" onchange='area("district",this.value)'>
									<option value="">-请选择-</option>
								</select>
								<span>市</span>
							</div>
							<div>
								<select required class='district' name="district" onchange='service()'>
									<option value="">-请选择-</option>
								</select>
								<span>县/区</span>
							</div>

							<div class="baojia-else">
								<p class="baojia-xiangxidizhi" >详细地址</p>
								<input type="text" required class="shurudizhi" placeholder="请输入地址" name='address' value='' >
								<p class="baojia-gongxi" >
								
								</p>
							</div>

						</div>
						<div class="baojia-home2" >
							<div class="baojia-hometype">
								<li ><input id='type1' required type="radio" name="type" value='1' onclick="selroom(1)"/>&nbsp;&nbsp;公寓</li>
								<li ><input id='type2' required type="radio" name="type" value='2' onclick="selroom(2)"/>&nbsp;&nbsp;叠加</li>
								<li><input id='type3' required type="radio" name="type" value='3' onclick="selroom(3)"/>&nbsp;&nbsp;联排</li>
								<li ><input id='type4' required type="radio" name="type" value='4' onclick="selroom(4)"/>&nbsp;&nbsp;独栋</li>
							</div>
							<div class="baojia-hometype-room" >
								<div class="baojia-room-sel" >
									<select name="select1" id="sel1" onchange="roomnum(this,1)">
										<option value="0">-请选择-</option>
										<?php for ($i=1; $i <= 8; $i++) { ?>
											<option value="<?php echo ($i); ?>"><?php echo ($i); ?></option>
										<?php } ?>
									</select>
									<span>卧室</span>
								</div>
								<div class="baojia-room-sel" >
									<select name="select2" id="sel2" onchange="roomnum(this,2)">
										<option value="0">-请选择-</option>
										<?php for ($i=1; $i <= 8; $i++) { ?>
											<option value="<?php echo ($i); ?>"><?php echo ($i); ?></option>
										<?php } ?>
									</select>
									<span>餐厅</span>
								</div>
								<div class="baojia-room-sel" >
									<select name="select3" id="sel3" onchange="roomnum(this,3)" >
										<option value="0">-请选择-</option>
										<?php for ($i=1; $i <= 8; $i++) { ?>
											<option value="<?php echo ($i); ?>"><?php echo ($i); ?></option>
										<?php } ?>
									</select>
									<span>厨房</span>
								</div>
								<div class="baojia-room-sel" >
									<select name="select4" id="sel4" onchange="roomnum(this,4)">
										<option value="0">-请选择-</option>
										<?php for ($i=1; $i <= 8; $i++) { ?>
											<option value="<?php echo ($i); ?>"><?php echo ($i); ?></option>
										<?php } ?>
									</select>
									<span>卫浴</span>
								</div>
								<div class="baojia-room-sel" >
									<select name="select5" id="sel5" onchange="roomnum(this,5)">
										<option value="0">-请选择-</option>
										<?php for ($i=1; $i <= 8; $i++) { ?>
											<option value="<?php echo ($i); ?>"><?php echo ($i); ?></option>
										<?php } ?>
									</select>
									<span>阳台</span>
								</div>
								<div class="baojia-room-sel baojiaroomsel6"  id="sel6fa">
									<select name="select6" id="sel6" onchange="roomnum(this,6)">
										<option value="0">-请选择-</option>
										<?php for ($i=1; $i <= 8; $i++) { ?>
											<option value="<?php echo ($i); ?>"><?php echo ($i); ?></option>
										<?php } ?>
									</select>
									<span>阁楼</span>
								</div>
								<div class="baojia-room-sel baojiaroomsel6" id="sel7fa">
									<select name="select7" id="sel7" onchange="roomnum(this,7)">
										<option value="0">-请选择-</option>
										<?php for ($i=1; $i <= 8; $i++) { ?>
											<option value="<?php echo ($i); ?>"><?php echo ($i); ?></option>
										<?php } ?>
									</select>
									<span>地室</span>
								</div>
							</div>
						</div>
					</div>
					<div class="baojia-sel-left-img">
						<img src="/spss/source/spss/Public/img/baojia-fangzi.jpg"/>
					</div>
				</div>
				<div class="baojia-sel-right" id="tijiao">
					<div class="baojia-selnum">
						<div class="baojia-selnum-1" id="baojiaoroom1">
							<li></li>
						</div>
						<div class="baojia-selnum-1" id="baojiaoroom2">
							<li></li>
						</div>
						<div class="baojia-selnum-1" id="baojiaoroom3">
							<li></li>
						</div>
						<div class="baojia-selnum-1" id="baojiaoroom4">
							<li></li>
						</div>
						<div class="baojia-selnum-1" id="baojiaoroom5">
							<li></li>
						</div>
						<div class="baojia-selnum-1" id="baojiaoroom6">
							<li></li>
						</div>
						<div class="baojia-selnum-1" id="baojiaoroom7">
							<li></li>
						</div>
						<div class="allarea">
							<p><span style="display: block;float: left;">全部面积(m²)</span><span id='total' style	="display: block;float: left; margin-left:10px ;text-align:center; border-radius: 3px;border:1px solid rgb(169,169,169);width: 68px;height: 28px;"></span></p>
							<p class="oftenrenkou">常住人口(人)<input type="text" id='people' name='people' required /></p>
						</div>
					</div>

					<div class="baojia-shengcheng" id="tijiao">
						<input class="baocun" type="submit" name="" value='保存填写内容'>
						<input class="shencheng but1" <?php if (empty($_SESSION['user']['userId'])) { ?> style='display:none;' <?php } ?> type="button" name="" onclick="but()" value='生成精确报价'>
						<input class="shencheng but2" <?php if (!empty($_SESSION['user']['userId'])) { ?> style='display:none;' <?php } ?>  type="button" name="" value='生成精确报价'>
					</div>
					
				</div>
			<input type="hidden" name="quote_id" value="<?php echo ($_GET["quote_id"]); ?>">
			<input type="hidden" name="lv" value="<?php echo ($_GET["lv"]); ?>">
			<input type="hidden" name="is_new" id='is_new' value="">	
		</form>
		<div class="baojia-content-zhoubian"  style='display:none;'>
			<div class="baojia-content-price">
				<div>
					<span>材料原价 :&nbsp;<a id='price1'>￥<?php echo ($return["price"]["0"]["all"]); ?>.00</a></span>
					<span>优惠价格:&nbsp;<a id='price2'>￥<?= ceil(($return['price']['0']['all']-$return['price']['0']['install'])/100*$return['quote']) ?>.00</a></span>
					<span>施工费用:&nbsp;<a id='price3'>￥<?php echo ($return["price"]["0"]["install"]); ?>.00</a></span>
					<span>仅需支付 :&nbsp;<a id='price4'>￥<?= ceil(($return['price']['0']['all']-$return['price']['0']['install'])/100*$return['quote'])+$return['price']['0']['install'] ?>.00</a></span>
					<span><a href="<?php echo U('materialList',array('quote_id' =>$_GET['quote_id'],'lv' =>$_GET['lv'],'house_id' =>$_SESSION['house_id'] ));?>">查看清单</a></span>
					<span class="goshopcar"><a onclick='goCart()'  name="加入购物车">前往购物车结算</a></span>
				</div>
			</div>
			<!--<div class="baojia-content-tit"><p>售后周边</p></div>
			<div class="baojia-content-cont1">
				<div class="baojia-content-cont1-left"></div>
				<div class="baojia-content-cont1-right">
					<p>保养</p>
					<p style="border: 1px solid #ff9200;">上品线原价￥<a>1500</a> <span>现价：￥1000</span></p>
					<p style="border: none;">*所有系统产品自交付之日起两年内为正常质保时间</p>
				</div>
			</div>
			<div class="baojia-content-cont1 baojia-content-cont2">
				<div class="baojia-content-cont1-left"></div>
				<div class="baojia-content-cont1-right">
					<p>保养</p>
					<p style="border: 1px solid #ff9200;width: 50%;margin-left: 0.6rem;">上品线五次上门费用￥200 &nbsp;原价：￥500</p>
					<p>上品线五次上门费用￥200 &nbsp;原价：￥500</p>
				</div>
			</div>
			<div class="baojia-content-cont1 baojia-content-cont3" >
				<div class="baojia-content-cont1-left"></div>
				<div class="baojia-content-cont1-right">
					<p>保养</p>
					<p style="border: 1px solid #ff9200;width: 50%;margin-left: 0.6rem;">上品线五次上门费用￥200 &nbsp;原价：￥500</p>
					<p>上品线五次上门费用￥200 &nbsp;原价：￥500</p>
				</div>
			</div>-->
			 <div class="clear3">
				<!--<p>推荐商品</p>-->
			</div> 
		
					
		</div>
  	</div>
  	<!-- <div class="baojia-content-jiesuan">
		<div>
		<p>客服热线：400-800-1027</p>
		<p>周一至周五9：00-18：00 &nbsp;&nbsp;<a >在线客服</a> <span class="zhanweizhi"></span>RMB:<span id='price5'></span> &nbsp;&nbsp;<a onclick='goCart()'>前往购物车结算</a></p>
		</div>
	</div> -->
</div>

	<script >
		function homejiancaihover(){
			var hover=document.getElementById("jiancaihover");
			hover.style.display="block";
		}
		function homeshouhouhover(){
			var hover=document.getElementById("shouhouhover");
			hover.style.display="block";
		}
		function homeloginhover(){
			var hover=document.getElementById("loginhover");
			hover.style.display="block";
		}
		function homeloginleave(){
			var hover=document.getElementById("loginhover");
			hover.style.display="none";
		}
		function homeshouhouleave(){
		var hover=document.getElementById("shouhouhover");
			hover.style.display="none";
		}
		function homejiancaileave(){
		var hover=document.getElementById("jiancaihover");
			hover.style.display="none";
		}
	</script>
	<script type="text/javascript">
		function selroom(index){
			if(index==1){
				$("#sel6fa").addClass("baojiaroomsel6");
				$("#sel7fa").addClass("baojiaroomsel6");
			}else if(index==2||index==3||index==4){
				$("#sel6fa").removeClass("baojiaroomsel6");
				$("#sel7fa").removeClass("baojiaroomsel6");
			}
		}
		function roomnum(n,index,o){
			var tijiao=document.getElementById("tijiao");
			tijiao.style.display="block";
			if (o==undefined) n=$(n).val();
			if(index==1){
				$("#baojiaoroom1").html("");
				for(var i=0;i<n;i++){
					if (o==undefined) $("#baojiaoroom1").append("<li ><input name='room1[]' type='text' value='' required typename='area' onkeyup='totalArea(this.value)'></li>")
					else if(o[i]!='') $("#baojiaoroom1").append("<li ><input name='room1[]' type='text' value='"+o[i]+"' typename='area' onkeyup='totalArea(this.value)'></li>")
				}
				$("#baojiaoroom1 li:first-child").prepend("卧室(m²)");
			}else if(index==2){
				$("#baojiaoroom2").html("");
				for(var i=0;i<n;i++){
					if (o==undefined) $("#baojiaoroom2").append("<li ><input name='room2[]' type='text' value='' required typename='area' onkeyup='totalArea(this.value)'></li>")
					else if(o[i]!='') $("#baojiaoroom2").append("<li ><input name='room2[]' type='text' value='"+o[i]+"' typename='area' onkeyup='totalArea(this.value)'></li>")
				}	
				$("#baojiaoroom2 li:first-child").prepend("餐厅(m²)");
			}else if(index==3){
				$("#baojiaoroom3").html("");
				for(var i=0;i<n;i++){
					if (o==undefined) $("#baojiaoroom3").append("<li ><input name='room3[]' type='text' value='' required typename='area' onkeyup='totalArea(this.value)'></li>")
					else if(o[i]!='') $("#baojiaoroom3").append("<li ><input name='room3[]' type='text' value='"+o[i]+"' typename='area' onkeyup='totalArea(this.value)'></li>")
				}	
				$("#baojiaoroom3 li:first-child").prepend("厨房(m²)");
			}else if(index==4){
				$("#baojiaoroom4").html("");
				for(var i=0;i<n;i++){
					if (o==undefined) $("#baojiaoroom4").append("<li ><input name='room4[]' type='text' value='' required typename='area' onkeyup='totalArea(this.value)'></li>")
					else if(o[i]!='') $("#baojiaoroom4").append("<li ><input name='room4[]' type='text' value='"+o[i]+"' typename='area' onkeyup='totalArea(this.value)'></li>")
				}	
				$("#baojiaoroom4 li:first-child").prepend("卫浴(m²)");
			}else if(index==5){
				$("#baojiaoroom5").html("");
				for(var i=0;i<n;i++){
					if (o==undefined) $("#baojiaoroom5").append("<li ><input name='room5[]' type='text' value='' required typename='area' onkeyup='totalArea(this.value)'></li>")
					else if(o[i]!='') $("#baojiaoroom5").append("<li ><input name='room5[]' type='text' value='"+o[i]+"' typename='area' onkeyup='totalArea(this.value)'></li>")
				}
				$("#baojiaoroom5 li:first-child").prepend("阳台(m²)");
			}else if(index==6){
				$("#baojiaoroom6").html("");
				for(var i=0;i<n;i++){
					if (o==undefined) $("#baojiaoroom6").append("<li ><input name='room6[]' type='text' value='' required typename='area' onkeyup='totalArea(this.value)'></li>")
					else if(o[i]!='') $("#baojiaoroom6").append("<li ><input name='room6[]' type='text' value='"+o[i]+"' typename='area' onkeyup='totalArea(this.value)'></li>")
				}
				$("#baojiaoroom6 li:first-child").prepend("阁楼(m²)");
			}else if(index==7){
				$("#baojiaoroom7").html("");
				for(var i=0;i<n;i++){
					if (o==undefined) $("#baojiaoroom7").append("<li ><input name='room7[]' type='text' value='' required typename='area' onkeyup='totalArea(this.value)'></li>")
					else if(o[i]!='') $("#baojiaoroom7").append("<li ><input name='room7[]' type='text' value='"+o[i]+"' typename='area' onkeyup='totalArea(this.value)'></li>")
				}	
				$("#baojiaoroom7 li:first-child").prepend("地室(m²)");
			}
		}
	</script>
	<script>
		function homeloginhover(){
			var hover=document.getElementById("loginhover");
			hover.style.display="block";
		}
		function homeloginleave(){
			var hover=document.getElementById("loginhover");
			hover.style.display="none";
		}
	</script>

	<!-- 省市县三级联动 -->
	<script type="text/javascript">	
		function area(o,v,c,d){
			$.getJSON("<?php echo U('area');?>",{'v':v},function(data){
				if (o=='city') {
					$('.district').html('');
					$('.district').append('<option value="">-请选择-</option>');
				}
				$('.'+o).html('');
				$('.'+o).append('<option value="">-请选择-</option>');
				for (var i = 0; i < data.length; i++) {
					if (c==data[i]['name']) {
						$('.'+o).append('<option selected value="'+data[i]['id']+'">'+data[i]['name']+'</option>');
					}else{
						$('.'+o).append('<option value="'+data[i]['id']+'">'+data[i]['name']+'</option>');
					}
				}

				<?php if (!empty($return['houseInfo']['district'])) { ?>
					var city = $('.city').val();
					$.getJSON("<?php echo U('area');?>",{'v':city},function(data){
						$('.district').html('');
						$('.district').append('<option value="">-请选择-</option>');
						for (var i = 0; i < data.length; i++) {
							if (d==data[i]['name']) {
								$('.district').append('<option selected value="'+data[i]['id']+'">'+data[i]['name']+'</option>');
							}else{
								$('.district').append('<option value="'+data[i]['id']+'">'+data[i]['name']+'</option>');
							}
						}
					})
				<?php } ?>
			})
		}
	</script>

	<!-- 选择房屋 -->
	<script type="text/javascript">
		function selectHouse(hId){
			$.getJSON("<?php echo U('selectHouse');?>",{'id':hId},function(data){
				$('#is_new').val('2');
				/*****************************/
				//地址
				$('.prov').html('');
				for (var i = 0; i < data.area.length; i++) {
					if (data.data.province==data.area[i]['name']) {
						$('.prov').append('<option selected value="'+data.area[i]['id']+'">'+data.area[i]['name']+'</option>');
					}else{
						$('.prov').append('<option value="'+data.area[i]['id']+'">'+data.area[i]['name']+'</option>');
					}
				}
				var prov = $('.prov').val();
				area('city',prov,data.data.city,data.data.district);
				$('.shurudizhi').val(data.data.address);
				/*****************************/


				/*****************************/
				//房屋类型
				for (var i = 1; i <= 4; i++) {
					document.getElementById('type'+i).checked = false;
				}
				document.getElementById('type'+data.data.type).checked = true;
				selroom(data.data.type);
				/*****************************/


				/*****************************/
				//几室几厅
				for (var i = 1; i <= data.data.layout.length; i++) {
					$('#sel'+(i)).val(data.data.layout[i-1]);
				}
				/*****************************/


				/*****************************/
				//每个房间面积
				for (var i = 1; i <= 7; i++) {
					roomnum(data.data.area[i-1].length,i,data.data.area[i-1])
				}
				/*****************************/

				$('#total').html(data.data.total_area);

				$('#people').val(data.data.people);

				var	sale = data.sale;

				$('#price1').html('￥'+data['price']['all']+'.00');
				$('#price2').html('￥'+Math.ceil((data['price']['all'] - data['price']['install'])/100*data.sale)+'.00');
				$('#price3').html('￥'+data['price']['install']+'.00');
				$('#price4').html('￥'+(Math.ceil((data['price']['all'] - data['price']['install'])/100*data.sale)+data['price']['install'])+'.00');
				$('#price5').html('￥'+(Math.ceil((data['price']['all'] - data['price']['install'])/100*data.sale)+data['price']['install'])+'.00');

				$('.shencheng').css('display','none');
				$('.baojia-content-zhoubian').css('display','none');
				if (data.service==true) {
					$('.baojia-gongxi').html('恭喜!您所在的地域有我们专业的服务商提供服务');
					$('.but1').css('display','block');
				}else{
					$('.baojia-gongxi').html('<span style="color:red">很遗憾！您所在的地域没有我们专业的服务商</span>');
					$('.but2').css('display','block');
				}
			})
		}

		function service(){
			var	p = $('.prov').val();
			var	c = $('.city').val();
			var	d = $('.district').val();
			$.post('<?php echo U("services");?>',{'p':p,'c':c,'d':d},function(data){
				if (data == 1) $('.baojia-gongxi').html('恭喜!您所在的地域有我们专业的服务商提供服务');
				else $('.baojia-gongxi').html('<span style="color:red">很遗憾！您所在的地域没有我们专业的服务商</span>');
			})
		}

		//添加新的房屋信息  点击+ 后将信息全部清除
		function newHouse(){
			$('#is_new').val('1');
			$.getJSON("<?php echo U('area');?>",{'v':100000},function(data){
				$('.prov').html('<option value="">-请选择-</option>');
				for (var i = 0; i < data.length; i++) {
					$('.prov').append('<option value="'+data[i]['id']+'">'+data[i]['name']+'</option>');
				}
				$('.city').html('<option value="">-请选择-</option>');
				$('.district').html('<option value="">-请选择-</option>');
				$('.shurudizhi').val('');
				for (var i = 1; i <= 4; i++) {
					document.getElementById('type'+i).checked = false;
				}
				for (var i = 1; i <= 5; i++) {
					$('#sel'+(i)).val('0');
				}
				for (var i = 1; i <= 7; i++) {
					roomnum('',i,'');
				}
				$('#total').html('');
				$('#people').val('');
				$('.houseList').attr('checked',false);
			})
		}
	</script>

	<!-- 计算全部面积 -->
	<script type="text/javascript">
		function totalArea(){
			var num = 0;
			var lis = document.getElementsByTagName('input');
			for (var i = 0; i < lis.length; i++) {
				if (lis[i].getAttribute('typename')=='area') {
	                if (lis[i].value=='') {
	                    var a = 0;
	                }else{
	                    var a = lis[i].value;
	                }
	                var num = (parseFloat(num) + parseFloat(a));
	            }
			}

			$('#total').html((Math.floor(num*100))/100);
		}
	</script>	

	<script type="text/javascript">
		function but(){
			$('.baojia-content-zhoubian').css('display','block');
			// $('.baojia-content-jiesuan').css('display','block');
		}
	</script>

	<script type="text/javascript">
		function goCart(){
			$.post("<?php echo U('goCart');?>",function(data){
				if (data==1) {
					window.location.href="<?php echo U('Cart/index');?>";
				}else{
					alert(data);
				}
			})
		}
	</script>

	<?php if (!empty($_SESSION['house_id'])): ?>
	<script type="text/javascript">
		$(document).ready(function(){ 
			selectHouse(<?php echo ($_SESSION['house_id']); ?>);
			totalArea();
		}); 
	</script>
	<?php endif ?>
<!--foot-->
		<div class="home-footer">
			<div class="home-footer1">
				<ul>
					<li><a href="javascript:;">服务与支持</a></li>
					<li><a href="<?php echo U('Index/show');?>">全国展示体验馆</a></li>
					<li><a href="<?php echo U('Index/aboutus');?>#a6">合作伙伴</a></li>
					<li><a href="<?php echo U('Dealer/index');?>">安装服务商登录</a></li>
				</ul>
				<ul>
					<li><a href="<?php echo U('Index/checkArticle',array('cat_id'=>53));?>">帮助中心</a></li>
					<li><a href="<?php echo U('Index/checkArticle',array('cat_id'=>53));?>">购物流程</a></li>
					<li><a href="<?php echo U('Index/checkArticle',array('cat_id'=>54));?>">配送政策</a></li>
					<li><a href="<?php echo U('Index/checkArticle',array('cat_id'=>55));?>">支付方式</a></li>
					<li><a href="<?php echo U('Index/checkArticle',array('cat_id'=>52));?>">购买指南</a></li>
				</ul>
				<ul>
					<li><a href="<?php echo U('Index/checkArticle',array('cat_id'=>56));?>">售后服务</a></li>
					<li><a href="<?php echo U('Index/checkArticle',array('cat_id'=>56,a_id=>146));?>">保修政策</a></li>
					<li><a href="<?php echo U('Index/checkArticle',array('cat_id'=>56,a_id=>147));?>">取消订单</a></li>
					<li><a href="<?php echo U('Index/checkArticle',array('cat_id'=>56,a_id=>148));?>">退换货政策</a></li>
					<li><a href="<?php echo U('Index/checkArticle',array('cat_id'=>56,a_id=>149));?>">常见问题</a></li>
				</ul>
				<ul>
					<li><a href="#">关注微信号</a></li>
					<!-- <li><span class="home-weixin"></span><a href="#">微信公众号</a></li> -->
					<li><img src="/spss/source/spss/Public/img/weixin.jpg" width="100"></li>
				</ul>
				<ul>
					<li><a href="#">客服热线</a></li>
					<li class="home-service"><span class="service-number"></span><a href="#">400-800-1027</a></li>
					<li><a href="#">周一至周五9：00-18：00</a></li>
					<!-- <li><a href="javascript:;"onclick="qimoChatClick();" class="service-online">在线客服</a></li> -->
				</ul>
			</div>
			<div class="clear2"></div>
			<div class="home-footer2">
				备案号：沪ICP备16030510号-1
			</div>
		</div>
		<script src="/spss/source/spss/Public/js/headfoot.js"></script>

		<!-- 7moor在线客服开始 -->	
		<?php if(!empty( $_SESSION['user']['userId']) and !empty( $_SESSION['user']['userName'])): ?><script type='text/javascript'>
				var qimoClientId = {userId:"<?php echo ($_SESSION['user']['userId']); ?>", nickName:"<?php echo ($_SESSION['user']['userName']); ?>"};// 自定义用户的唯一id,userId由英文字母,数字,下划线组成
		</script><?php endif; ?>
		<script type='text/javascript' src='http://webchat.7moor.com/javascripts/7moorInit.js?accessId=ecdab400-a94d-11e5-bbcd-fbb9b9d68830&autoShow=false' async='async'></script>
		<!-- 7moor在线客服结束 -->	

		<!-- 99click网站统计开始 -->
		<input type="hidden" id='o_userId' value='<?php if(!empty( $_SESSION['user']['userId']) ): echo ($_SESSION['user']['userId']); endif; ?>'>
		<script type='text/javascript' src='/spss/source/spss/Public/js/jquery.cookie.js'></script>
		<script type="text/javascript">
		try{
			var _ozuid;
			var _user=$('#o_userId').val();//需传值，用户登陆后的用户id，如果没有登录传空值，即_user=’’;
			var _domain=document.domain.match(/\.[a-zA-Z0-9.-]+/);
			if($.cookie("ozuid") &&(_user==''|| null==_user)){  //cookie有值，但是用户尚未登录 ;那么取cookie值
				_ozuid=$.cookie("ozuid");
			}else if($.cookie("ozuid") &&(null!= _user)){ //cookie有值，但是用户已登录 ;那么更新cookie值，再取cookie值
				$.cookie("ozuid",_user,{path:"/",expires:365,domain:_domain});
				_ozuid=$.cookie("ozuid");
			}else if(!$.cookie("ozuid") &&(_user==''|| null==_user)){//cookie无值，用户也未登录，不能记录会员行为
				//无动作
			}else if(!$.cookie("ozuid") &&(null!= _user)){
				$.cookie("ozuid",_user,{path:"/",expires:365,domain:_domain}); //cookie无值，但是用户已登录 ;那么存储cookie值，再取cookie值
				_ozuid=$.cookie("ozuid");
			}
		}catch(e){
		}
		</script>
		<script type='text/javascript' src='/spss/source/spss/Public/js/o_code.js'></script>
		<!-- 99click网站统计结束 -->
	</body>

</html>