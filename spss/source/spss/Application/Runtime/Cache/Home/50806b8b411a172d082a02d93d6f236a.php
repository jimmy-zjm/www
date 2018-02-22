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
 <link rel="stylesheet" type="text/css" href="/spss/source/spss/Public/css/produceline.css"/>
<meta http-equiv="x-ua-compatible" content="IE=9;" />
	<!--content-->
	<div class="content">
		<!--produceline-banner-->
		<div class="produceline-banner">
			<img src="<?php echo ($img); ?>"/>
			<!-- <p class="banner-zi1">上&nbsp;品&nbsp;标&nbsp;准&nbsp;线</p>
			<p class="banner-zi2"></p>
			<p class="banner-zi3"><a href="" style="color: #d7d7d7;">了解新风系统</a></p> -->
		</div>
		<!--produceline-main-->
		<div class="produceline-main">
			<div class="produceline-tab1 produceline-tab">
				<a href="javascript:;" style="color:#00a0e9;" id="producelineping" onclick="producetab(1)">上品系列豪华型</a>
			</div>
			<div class="produceline-tab2 produceline-tab">
				<a href="javascript:;" style="color: #000;cursor: pointer;" id="producelinesheng" onclick="producetab(2)">上生系列经济型</a>
			</div>
			<div class="produceline-tab1-show clear" id="producelinetab1" >
				<h5>更舒适更安全更智能更豪华的家居系统，您的高端之选。</h5>
				<div class="produceline-tab1-show-main">
					<div class="produceline-tab1-showcont">
					<img src="/spss/source/spss/Public/img/produceline-computer1.png" class="produceline-computer1">
					<p class="produceline-yuanb">
						<img src="/spss/source/spss/Public/img/produceline-yuanb.png" alt=""  class="produceline-imgb">
						<p class="produceline-yuans">
							<img src="/spss/source/spss/Public/img/produceline-yuans.png" alt=""  class="produceline-imgs">
						</p>
					</p>
				</div>

				<div class="produceline-tab1-showcont-go">
				<?php foreach ($list as $key => $v): ?>
					<?php if ($key<7): ?>
						<a href="<?php echo U('Furnish/detail',array('quote_id'=>$v['quote_id'],'lv'=>'1'));?>"><span ></span><?php echo ($v["quote_name"]); ?></a>
					<?php endif ?>
				<?php endforeach ?>
					<a href="javascript:;"><span ></span>上品上生智能化系统</a>
				</div>
				</div>
				
			</div>
			<div class="produceline-tab1-show clear" id="producelinetab2" style="display: block;">
				<h5>更简洁更划算更优惠更经济的家居系统，您的实用之选。</h5>
				<div class="produceline-tab1-show-main">
					<div class="produceline-tab1-showcont">
					<img src="/spss/source/spss/Public/img/produceline-computer2.png" class="produceline-computer1">
					<p class="produceline-yuanb">
						<img src="/spss/source/spss/Public/img/produceline-yuanb.png" alt=""  class="produceline-imgb">
						<p class="produceline-yuans">
							<img src="/spss/source/spss/Public/img/produceline-yuans.png" alt=""  class="produceline-imgs">
						</p>
					</p>
				</div>
				<div class="produceline-tab1-showcont-go">
				<?php foreach ($list as $key => $v): ?>
					<?php if ($key>=7): ?>
						<a href="<?php echo U('Furnish/detail',array('quote_id'=>$v['quote_id'],'lv'=>'1'));?>"><span ></span><?php echo ($v["quote_name"]); ?></a>
					<?php endif ?>
				<?php endforeach ?>
				</div>
				</div>
			</div>
		</div>
	</div>

<script>
	function homeloginhover(){
		var hover=document.getElementById("loginhover");
		hover.style.display="block";
	}
	function homeloginhoverout(){
		var hover=document.getElementById("loginhover");
		hover.style.display="none";
		
	}
	function producetab(index){
		var producelinetab1=document.getElementById("producelinetab1");
		var producelinetab2=document.getElementById("producelinetab2");
		var producelineping=document.getElementById('producelineping');
		var producelinesheng=document.getElementById('producelinesheng');
		if(index==2){
			producelinetab2.style.display="block";
			producelinetab1.style.display="none";
			producelineping.style.color="#000";
			producelinesheng.style.color="#00a0e9";
			
		}else{
			producelinetab2.style.display="none";
			producelinetab1.style.display="block";
			producelineping.style.color="#00a0e9";
			producelinesheng.style.color="#000";
		}
	}
</script>


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