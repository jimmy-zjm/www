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
 <link rel="stylesheet" type="text/css" href="/spss/source/spss/Public/css/public.css"/>
<link rel="stylesheet" href="/spss/source/spss/Public/css/dibandetail.css" />
	
<!--content-->
<div class="content" >
	<div class="zhankong"></div>
	<div class="con-top">
		<div class="con-top-left">
			<p>
				<img id="img1" src="<?= C(TP_APP_URL).'/Public/Uploads/'.$img['0']['url'] ?>" alt="" />
			</p>
			<p>
				<?php foreach ($img as $k => $v): ?>
					<a class="simg"  onclick="addborder(<?php echo ($k); ?>,'<?= C(TP_APP_URL).'/Public/Uploads/'.$v['url'] ?>')"><img src="<?= C(TP_APP_URL).'/Public/Uploads/'.$v['url'] ?>" alt="" /></a>
				<?php endforeach ?>
			</p>
			<p>
				<a href="javascript:;" onclick='fenxiang()'>分享</a>
				<span onclick="changecolor()"><a id="shoucang" <?php if(!empty($collect)){ ?> class='bianse' <?php } ?> href="javascript:;">★</a>收藏商品</span>
				<span>(<a href="javascript:;" id='count'><?php echo ($count); ?></a>人气)</span>
				<div style='display: none;margin-left: 20px;' class="bdsharebuttonbox" data-tag="share_1">
					<a class="bds_mshare" data-cmd="mshare"></a>
					<a class="bds_qzone" data-cmd="qzone" href="#"></a>
					<a class="bds_tsina" data-cmd="tsina"></a>
					<a class="bds_baidu" data-cmd="baidu"></a>
					<a class="bds_renren" data-cmd="renren"></a>
					<a class="bds_tqq" data-cmd="tqq"></a>
					<a data-cmd="more">更多</a>
				</div>
			</p>
		</div>
		<div class="con-top-right">
			<p class="con-top-right-1"><?php echo ($data["goods_title"]); ?></p>
			<p class="con-top-right-2"><?php echo ($data["pro_info"]); ?></p>
			<p class="con-top-right-3"><span>价格</span><span>￥：<span id='price1'><?php echo ($data["market_price"]); ?></span></span></p>
			<p class="con-top-right-4"><span>促销价</span><span>￥：<span id='price2'><?php echo ($data["shop_price"]); ?></span></span></p>
			<!-- <p></p> -->
			
			<?php foreach ($list as $k => $v): ?>
				<?php if ($v['mode'] == 1 && !empty($v['attr']) && !empty($v['name'])): ?>
				<p class="con-top-right-5-left key<?php echo ($k); ?>"><span><?php echo ($v["name"]); ?></span>
					<?php foreach ($v['attr'] as $key=>$val): ?>
						<?php if($v['mode']==1){ ?>
						<span class=" val<?php echo ($k); ?>" <?php if (!empty($price[$val['id']])): ?> price="<?php echo ($price[$val['id']]); ?>" <?php endif ?> onclick="imgaddborder(this,<?php echo ($k); ?>,<?php echo ($val["id"]); ?>)" ><?php echo ($val['attr_value']); ?></span>
						<?php }else{ ?>
						<i class="fukuanhou" ><?php echo ($val["attr_value"]); ?></i>
						<?php } ?>
					<?php endforeach ?>
				</p>
				<?php endif ?>
			<?php endforeach ?>
			
			<p class="con-top-right-6"><span>数量</span><span class="jiajiancount"><input type="text" class="inputcount" id='num' value='1' onchange="num(this.value)" /><span class="jiajianbtn"><a class="jia" onclick="addcount()">+</a><a class="jian" onclick="jiancount()">-</a></span>件<!-- <?php echo ((isset($data["after_sales_support"]) && ($data["after_sales_support"] !== ""))?($data["after_sales_support"]):"件"); ?> --></span></p>
			<p class="fuwu fuwu1 con-top-right-7"><span>服务说明</span></p>
			<p class="fuwu con-top-right-8"><?php echo ($data["package_list"]); ?></p>
			<p class="con-top-right-9"><a href="javascript:;" id='ljg' onclick='shopCar(1)'>立即购买</a><a href="javascript:;" name="加入购物车" onclick='shopCar(2)'>加入购物车</a></p>
		</div>
	</div>
	
	<div class="con-bottom">
		<ul class="con-bottom-tab">
			<li onclick="dibandetailtabs(this,0)" class="tabli" style="color: red;border-color: red;">产品详情</li>
			<li onclick="dibandetailtabs(this,1)" class="tabli">累计评价 <span><?= $commentCount ?></span></li>
			<li onclick="dibandetailtabs(this,2)" class="tabli">服务说明</li>
		</ul>
		<div class="tabs tabs1" id="tab1">
			
			<div class="tab1-con1" id="tab1con1" style='padding-top:25px '>
				<h4 style='padding-left:10px;padding-bottom:15px;'>产品参数：</h4>
					<?php foreach ($list as $k => $v): ?>
						<?php if ($v['mode'] == 0 && !empty($v['attr']) && !empty($v['name'])): ?>
						<div class="con-top-right-5-left key<?php echo ($k); ?>" style='width:30%;float:left;'><i class="fukuanhou"><?php echo ($v["name"]); ?>:</i>
							<?php foreach ($v['attr'] as $key=>$val): ?>
								<i class="fukuanhou" ><?php echo ($val["attr_value"]); ?></i>
							<?php endforeach ?>
						</div>
						<?php endif ?>
					<?php endforeach ?>
					<div class="clear"></div>
					<?= html_entity_decode($data['goods_detail'],ENT_QUOTES,'UTF-8'); ?>
			</div>
			<!-- <div class="tab1-con2 tab1-con-img" id="tab1con2">
				<?= html_entity_decode($data['particulars'],ENT_QUOTES,'UTF-8'); ?>
			</div> -->
			<!-- <div class="tab1-con3 tab1-con-img" id="tab1con3">
				<fieldset>
				    <legend align="center">&nbsp;商品实拍&nbsp;</legend>
				</fieldset>
				
			</div>
			<div class="tab1-con4 tab1-con-img" id="tab1con4">
				<fieldset>
				    <legend align="center">&nbsp;商品细节&nbsp;</legend>
				</fieldset>
			</div>
			<div class="tab1-con5 tab1-con-img" id="tab1con5">
				<fieldset>
				    <legend align="center">&nbsp;服务质量&nbsp;</legend>
				</fieldset>
			</div>
			<div class="tab1-con6 tab1-con-img" id="tab1con6">
				<fieldset>
				    <legend align="center">&nbsp;买家须知&nbsp;</legend>
				</fieldset>
			</div> -->
			<div class="tab1-con7 tab1-con-img" id="tab1con7">
				<fieldset>
				   <legend align="center">&nbsp;促销专区&nbsp;</legend>
				</fieldset>
				<?php foreach ($goods as $k => $v): ?>
					<a href="<?php echo U('info',array('id'=>$v['id']));?>" style="display: block;">
					<li>
						<p><img src="<?= C(TP_APP_URL).'/Public/Uploads/'.$v['face_image'] ?>" alt="" /></p>
						<p><?php echo ($v["goods_title"]); ?></p>
						<p>原价<span><?php echo ($v["market_price"]); ?></span>元/㎡</p>
						<p><span><?php echo ($v["shop_price"]); ?></span>元/㎡</p>
					</li>
					</a>
				<?php endforeach ?>
			</div>
			<!-- <div class="tab1-fixed">
				<p onclick="tabfixed(0)"><span ><img class="fixspan" src="/spss/source/spss/Public/img/danxuan-btn.jpg"/></span><a class="fixa" href="#tab1con1">商品参数</a></p>
				<p onclick="tabfixed(1)"><span ><img class="fixspan" src="/spss/source/spss/Public/img/danxuan-btn.jpg"/></span><a class="fixa" href="#tab1con2">商品情景</a></p>
				<p onclick="tabfixed(2)"><span ><img class="fixspan" src="/spss/source/spss/Public/img/danxuan-btn.jpg"/></span><a class="fixa" href="#tab1con3">商品实拍</a></p>
				<p onclick="tabfixed(3)"><span ><img class="fixspan" src="/spss/source/spss/Public/img/danxuan-btn.jpg"/></span><a class="fixa" href="#tab1con4">商品细节</a></p>
				<p onclick="tabfixed(4)"><span ><img class="fixspan" src="/spss/source/spss/Public/img/danxuan-btn.jpg"/></span><a class="fixa" href="#tab1con5">服务质量</a></p>
				<p onclick="tabfixed(5)"><span ><img class="fixspan" src="/spss/source/spss/Public/img/danxuan-btn.jpg"/></span><a class="fixa" href="#tab1con6">买家须知</a></p>
				<p onclick="tabfixed(6)"><span ><img class="fixspan" src="/spss/source/spss/Public/img/danxuan-btn.jpg"/></span><a class="fixa" href="#tab1con7">促销专区</a></p>
			</div> -->
		</div>
		<div class="tabs tabs2" >
			<div>
				<?php if(empty($star)){ ?>
				暂无评价
				<?php }else{ ?>
				<span><?php echo ($star); ?>%</span>好评度
				<?php } ?>
			</div>
			<div id='page'>
			<?php foreach ($comment as $k => $v): ?>
				<div class="tabs2-con2">
					<p class="tabs2-con2-1">
						<span class="tabs2-con2-1-txt1"><?php echo ($v["content"]); ?></span>
						<span class="tabs2-con2-1-img1">
							<?php foreach ($v['imgArray'] as $key => $val): ?>
								<img class="activeimg" index="0" onclick="activeimg(this)" src="/spss/source/spss/Public/Uploads/<?php echo ($val); ?>" alt="" />
							<?php endforeach ?>
						</span>
						<span class="tabs2-con2-1-img2"><img class="bigimg" src="" alt="" /></span>
					</p>
					<p class="tabs2-con2-2">
						<span><?php for ($i=0; $i < $v['star']; $i++) { ?>★<?php } ?></span>
						<span><?= date('Y-m-d H:i:s',$v['add_time']) ?></span>
					</p>
					<p class="tabs2-con2-3">
						<span><img src="/spss/source/spss/Public/Uploads/<?php echo ($v["face"]); ?>" width='60' height='60' alt="" /></span>
						<span><?php echo ($v["user_name"]); ?></span>
					</p>
				</div>
			<?php endforeach ?>
			<?php echo ($page); ?>
			</div>
		</div>
		
		<div class="tabs tabs3" id="tab3">
			<div class="tab1-con1" id="tab1con1">
				<?= html_entity_decode($data['goods_explain'],ENT_QUOTES,'UTF-8'); ?>
			</div>
		</div>
	</div>
</div>
<input type="hidden"  id='market_price' value='<?php echo ($data["market_price"]); ?>'>
<input type="hidden"  id='shop_price' value='<?php echo ($data["shop_price"]); ?>'>
<?php foreach ($list as $k => $v): ?>
	<?php if ($v['mode']==1): ?>
		<input type="hidden" key='<?php echo ($k); ?>' class='attr' id="attr<?php echo ($k); ?>" value=''>
	<?php endif ?>
<?php endforeach ?>

<script src="/spss/source/spss/Public/js/headfoot.js"></script>

<script>
//	加减数量bg
	function addcount(){
		var add=parseInt($("#num").val());
		add=add+1;
		if(add<=0){
			$("#num").val(0);
		}else{
			$("#num").val(add);
		}
	}
	function jiancount(){
		var add=parseInt($("#num").val());
		add=add-1;
		if(add<=0){
			$("#num").val(0);
		}else{
			$("#num").val(add);
		}
	}
//	加减数量end
	function shopCar(tz){
		var attr   = new Array();
		var length = $('.attr').length;
		for (var i = 0; i < length; i++) {
			var k   = $('.attr').eq(i).attr('key');
			var v  = $('#attr'+k).val();
			attr[i] = [k,v];
			if (v=='') {
				var key = $('.key'+k+' span').html();
				alert('请选择'+key);
				return;
			}
		}	
		var num = $('#num').val();
		if (num < 1) {
			alert('数量不能小于1');
			return;
		}
		$.post("<?php echo U('goCart');?>",{'attr':attr,'id':<?php echo ($_GET["id"]); ?>,'num':num,'tz':tz},function(data){
			if(tz==1){
				if(isNaN(data)) {
					if (data=='login') window.location.href="<?php echo U('User/login');?>";
					else alert(data);
				}else  window.open("<?php echo U('Order/index');?>?goods_id="+data+"&num="+num);
			}else{
				if(!isNaN(data)) alert('添加成功');
				else alert(data);
			}
		})
	}


	$('.page a').click(function(){
		var p = $(this).attr('data-page');
		$.get("<?php echo U('page');?>",{'id':<?php echo ($_GET["id"]); ?>,'p':p},function(data){
			$('#page').html(data);
		})
	})

	window.onscroll=function(){
			var tabtop=$("#tab1")[0].offsetTop;
			var doctop=$(window).scrollTop();
			var stop=tabtop-doctop;
			if(doctop>=stop){
				$(".tab1-fixed").css("position","fixed");
				$(".tab1-fixed").css("top",300+"px");
				$(".tab1-fixed").css("right",208+"px");
			}else{
				$(".tab1-fixed").css("position","absolute");
				$(".tab1-fixed").css("top",0+"px");
				$(".tab1-fixed").css("right",0+"px");
			}
			
		}
	function tabfixed(n){
		for(var i=0;i<7;i++){
			if(i==n){
				$(".fixspan").eq(i).attr("src","/spss/source/spss/Public/img/danxuan-btn2.jpg");
				$(".fixa").eq(i).css("color","red");
			}else{
				$(".fixspan").eq(i).attr("src","/spss/source/spss/Public/img/danxuan-btn.jpg");
				$(".fixa").eq(i).css("color","#666");
			}
		}
	}
	function dibandetailtabs(n,z){
		$(".tabli").css("border-color","#666").css("color","#aaa");
		$(n).css("border-color","red").css("color","red");
		if(z==0){
			$(".tabli").eq(1).css("border-left","none");
			$(".tabli").eq(0).css("border-right","1px solid red");
			$(".tabs1").css("display","block");
			$(".tabs2").css("display","none");
			$(".tabs3").css("display","none");
		}else if(z==1){
			$(".tabli").eq(0).css("border-right","none");
			$(".tabli").eq(1).css("border-left","1px solid red");
			$(".tabli").eq(2).css("border-left","1px solid red");
			$(".tabli").eq(1).css("border-right","none");
			$(".tabs1").css("display","none");
			$(".tabs2").css("display","block");
			$(".tabs3").css("display","none");
		}else{
			$(".tabli").eq(0).css("border-right","none");
			$(".tabli").eq(1).css("border-left","1px solid #666");			
			$(".tabli").eq(1).css("border-right","none");
			$(".tabli").eq(2).css("border-left","1px solid red");
			$(".tabs1").css("display","none");
			$(".tabs2").css("display","none");
			$(".tabs3").css("display","block");
		}
	}
	function addborder(n,dizhi){
		for(var i=0;i<5;i++){
			if(i==n){
				$(".simg").eq(i).css("border-color","red")
				$("#img1").attr("src",dizhi)
			}else{
				$(".simg").eq(i).css("border-color","#fff")
			}
			
		}
	}
	function changecolor(){
		var attr   = new Array();
		var length = $('.attr').length;
		for (var i = 0; i < length; i++) {
			var k   = $('.attr').eq(i).attr('key');
			var v  = $('#attr'+k).val();
			attr[i] = [k,v];
			if (v=='') {
				var key = $('.key'+k+' span').html();
				alert('请选择'+key);
				return;
			}
		}	
		$.post('<?php echo U("collect");?>',{'attr':attr,'id':<?= $_GET['id'] ?>},function(data){
			if (data == 1) {
				$("#shoucang").addClass("bianse");
				var count = parseInt($("#count").text()) + parseInt(1);
				$("#count").text(count);
				alert('收藏成功');
			}else alert(data);
		})
	}
	function imgaddborder(t,k,id){
		$('.val'+k).removeClass("imgborder");
		$(t).addClass("imgborder");
		$('#attr'+k).val(id);
		var length = $('.imgborder').length;
		var price  = 0;
		for (var i = 0; i < length; i++) {
			var pr = $('.imgborder').eq(i).attr('price')
			if (pr != undefined) price += parseFloat(pr);
		}
		var market_price = $('#market_price').val();
		var shop_price   = $('#shop_price').val();
		var price1 		 = parseFloat(market_price) + price;
		var price2   	 = parseFloat(shop_price) + price;
		$('#price1').html(price1.toFixed(2));
		$('#price2').html(price2.toFixed(2));
	}


	function num(t){
		if (t<1) $('#num').val('1');
	}

	function activeimg(t){	
		var count = $(t).attr("index");
		var img   = $(t).attr("src");
		var reimg = $(t).parent().next().children().attr("src");
		// $(".bigimg").attr("src",img);
		$(t).parent().next().children().attr("src",img);
		if (img == reimg) {
			$(t).parent().next().children().attr("src",'');
			$(t).parent().next().css("display","none");
			$(t).parent().parent().next().next().css("margin-top","30px");
			$(t).parent().parent().next().css("margin-top","30px");
		}else{
			$(t).parent().next().css("display","block");
			$(t).parent().parent().next().next().css("margin-top","150px");
			$(t).parent().parent().next().css("margin-top","150px");
		}
	}

	function fenxiang(){
		$('.bdsharebuttonbox').toggle();
	}

</script>

<script>

	window._bd_share_config = {
		common : {
			bdUrl : "http://www.myspss.com/spss/source/spss/index.php/<?php echo ($_SESSION['url']); ?>", 
		},
		share : [{
			"bdSize" : 16
		}],
	}
	with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='+~(-new Date()/36e5)];
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