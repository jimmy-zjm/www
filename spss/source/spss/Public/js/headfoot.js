//导航
function homeloginhover(t){
	$(".nav-bg").animate({height:'108px'},200)
	$("#changeH").animate({height:'108px'},200)
	$("#changeLine").show();
//	$("#iframebg").attr("height","130px");
	// $("#changeH").css("box-shadow","0px 3px 10px rgba(155, 160, 165, 0.61)");
	if($(t).attr("index")==1){
		$(".home-nav-hover-left-1").css("display","block")
	}else if($(t).attr("index")==2){
		$(".home-nav-hover-left-2").css("display","block")
	}else if($(t).attr("index")==3){
		$(".home-nav-hover-left-3").css("display","block")
	}else if($(t).attr("index")==4){
		$(".home-nav-hover-left-4").css("display","block")
	}
}
function homeloginleave(t){
	$("#changeH").stop(true).animate({height:'68px'},200)
	$(".nav-bg").stop(true).animate({height:'68px'},200)
	$("#changeLine").hide();
	// $("#changeH").css("box-shadow","none");
//	$("#iframebg").attr("height","78px");
	if($(t).attr("index")==1){
		$(".home-nav-hover-left-1").css("display","none")
	}else if($(t).attr("index")==2){
		$(".home-nav-hover-left-2").css("display","none")
	}else if($(t).attr("index")==3){
		$(".home-nav-hover-left-3").css("display","none")
	}else if($(t).attr("index")==4){
		$(".home-nav-hover-left-4").css("display","none")
	}
}
//购物车
function homeshopcarhover(){
	$("#changeLine").show();
	$("#changeH").animate({height:'308px'},200);
	$(".nav-bg").animate({height:'308px'},200);
//	$("#iframebg").attr("height","318px");
	$('.nav3-shopcar').css('display','block');
	// $("#changeH").css("box-shadow","0px 3px 10px rgba(155, 160, 165, 0.61)");
}
function homeshopcarleave(){
	$("#changeLine").hide();
	$("#changeH").stop(true).animate({height:'68px'},200);
	$(".nav-bg").stop(true).animate({height:'68px'},200);
//	$("#iframebg").attr("height","78px");
	$('.nav3-shopcar').css('display','none');
	// $("#changeH").css("box-shadow","none");
}

		
$(function(){
	var $hnh=$(".home-nav-hover");
	$hnh.each(function(){
	var $a=$(this).find("a");
	$(this).width($a.length*$a.width());
	})
})