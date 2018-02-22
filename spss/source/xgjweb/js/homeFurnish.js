//等待dom元素加载完毕.
$(document).ready(function(){
	var j     = '+';
	var dome1   = $("#dome1").html();  
	var dome2   = $("#dome2").html(); 
	var dome3   = $("#dome3").html(); 
	var dome4   = $("#dome4").html(); 
	var dome5   = $("#dome5").html();
	var dome6   = $("#dome6").html();
	//jQuery对象 新风/除霾/除湿/加湿  系列
	var $xilie5 = $("#xilie5");  
	var $xl_5_1 = $("#xl_5_1");  
	var $xl_5_2 = $("#xl_5_2"); 
	var $xl_5_3 = $("#xl_5_3"); 
	var $xl_5_4 = $("#xl_5_4"); 
	var $xl_5_5 = $("#xl_5_5");
	
	$xilie5.click(function(){
		if($('#xl_5_1').is(':checked')&& $('#xl_5_2').is(':checked') && $('#xl_5_4').is(':checked')){
			$('#x1').html(dome1+j+dome2+j+dome4);
			$("#content").load("furnish.php?content&id=5");
		}else if($('#xl_5_1').is(':checked')&& $('#xl_5_2').is(':checked') && $('#xl_5_5').is(':checked')){
			$('#x1').html(dome1+j+dome2+j+dome5);
			$("#content").load("furnish.php?content&id=6");
		}else if($('#xl_5_1').is(':checked')&& $('#xl_5_3').is(':checked') && $('#xl_5_4').is(':checked')){
			$('#x1').html(dome1+j+dome3+j+dome4);
			$("#content").load("furnish.php?content&id=8");
		}else if($('#xl_5_1').is(':checked')&& $('#xl_5_3').is(':checked') && $('#xl_5_5').is(':checked')){
			$('#x1').html(dome1+j+dome3+j+dome5);
			$("#content").load("furnish.php?content&id=9");
		}else if($('#xl_5_1').is(':checked')&& $('#xl_5_2').is(':checked')){
			$('#x1').html(dome1+j+dome2);
			$("#content").load("furnish.php?content&id='4,5,6'");
		}else if($('#xl_5_1').is(':checked')&& $('#xl_5_3').is(':checked') ){
			$('#x1').html(dome1+j+dome3);
			$("#content").load("furnish.php?content&id='7,8,9'");
		}else if($('#xl_5_1').is(':checked')&& $('#xl_5_4').is(':checked') ){
			$('#x1').html(dome1+j+dome4);
			$("#content").load("furnish.php?content&id='2,5,8'");
		}else if($('#xl_5_1').is(':checked')&& $('#xl_5_5').is(':checked') ){
			$('#x1').html(dome1+j+dome5);
			$("#content").load("furnish.php?content&id='3,6,9'");
		}else if($('#xl_5_2').is(':checked') && $('#xl_5_4').is(':checked')){
			$('#x1').html(dome2+j+dome4);
			$("#content").load("furnish.php?content&id=5");
		}else if($('#xl_5_2').is(':checked') && $('#xl_5_5').is(':checked')){
			$('#x1').html(dome2+j+dome5);
			$("#content").load("furnish.php?content&id=6");
		}else if($('#xl_5_3').is(':checked') && $('#xl_5_4').is(':checked')){
			$('#x1').html(dome3+j+dome4);
			$("#content").load("furnish.php?content&id=8");
		}else if($('#xl_5_3').is(':checked') && $('#xl_5_5').is(':checked')){
			$('#x1').html(dome3+j+dome5);
			$("#content").load("furnish.php?content&id=9");
		}else if($('#xl_5_1').is(':checked')){
			$('#x1').html(dome1);
			$("#content").load("furnish.php?content&id='1,2,3,4,5,6,7,8,9'");
		}else if($('#xl_5_2').is(':checked')){
			$('#x1').html(dome2);
			$("#content").load("furnish.php?content&id='4,5,6'");
		}else if($('#xl_5_3').is(':checked')){
			$('#x1').html(dome3);
			$("#content").load("furnish.php?content&id='7,8,9'");
		}else if($('#xl_5_4').is(':checked')){
			$('#x1').html(dome4);
			$("#content").load("furnish.php?content&id='2,5,8'");
		}else if($('#xl_5_5').is(':checked')){
			$('#x1').html(dome5);
			$("#content").load("furnish.php?content&id='3,6,9'");
		}

	
		if($('#xl_5_1').is(':checked')!=true && $('#xl_5_2').is(':checked')!=true && $('#xl_5_3').is(':checked')!=true && $('#xl_5_4').is(':checked')!=true && $('#xl_5_5').is(':checked')!=true) {
			$('#x1').html('');
			$("#content").load("furnish.php?content&id='1,2,3,4,5,6,7,8,9'");
			$('#xl_5_1').removeAttr("disabled");
			$('#xl_5_2').removeAttr("disabled");
			$('#xl_5_3').removeAttr("disabled");
			$('#xl_5_4').removeAttr("disabled");
			$('#xl_5_5').removeAttr("disabled");
		}
		
		if($('#xl_5_1').is(':checked')) {
			$('#xl_5_1').removeAttr("disabled");
			$('#xl_5_2').removeAttr("disabled");
			$('#xl_5_3').removeAttr("disabled");
			$('#xl_5_4').removeAttr("disabled");
			$('#xl_5_5').removeAttr("disabled");
		}
		if($('#xl_5_1').is(':checked')&&$('#xl_5_2').is(':checked')) {
			$("#xl_5_3").attr("disabled","disabled");
		}		
		if($('#xl_5_1').is(':checked')&&$('#xl_5_2').is(':checked')&&$('#xl_5_4').is(':checked')) {
			$("#xl_5_5").attr("disabled","disabled");
		}
		if($('#xl_5_1').is(':checked')&&$('#xl_5_2').is(':checked')&&$('#xl_5_5').is(':checked')) {
			$("#xl_5_4").attr("disabled","disabled");
		}
		if($('#xl_5_1').is(':checked')&&$('#xl_5_3').is(':checked')) {
			$("#xl_5_2").attr("disabled","disabled");
		}
		if($('#xl_5_1').is(':checked')&&$('#xl_5_3').is(':checked')&&$('#xl_5_4').is(':checked')) {
			$("#xl_5_5").attr("disabled","disabled");
		}
		if($('#xl_5_1').is(':checked')&&$('#xl_5_3').is(':checked')&&$('#xl_5_5').is(':checked')) {
			$("#xl_5_4").attr("disabled","disabled");
		}	
		if($('#xl_5_2').is(':checked')) {
			
			$('#xl_5_1').removeAttr("disabled");
			$('#xl_5_2').removeAttr("disabled");
			$('#xl_5_3').attr("disabled","disabled");
			$('#xl_5_4').removeAttr("disabled");
			$('#xl_5_5').removeAttr("disabled");
		}
		if($('#xl_5_2').is(':checked')&&$('#xl_5_4').is(':checked')){
			$('#xl_5_5').attr("disabled","disabled");
		}
		if($('#xl_5_2').is(':checked')&&$('#xl_5_5').is(':checked')){
			$('#xl_5_4').attr("disabled","disabled");
		}
		if($('#xl_5_3').is(':checked')) {
			
			$('#xl_5_1').removeAttr("disabled");
			$('#xl_5_2').attr("disabled","disabled");
			$('#xl_5_3').removeAttr("disabled");
			$('#xl_5_4').removeAttr("disabled");
			$('#xl_5_5').removeAttr("disabled");
		}
		if($('#xl_5_3').is(':checked')&&$('#xl_5_4').is(':checked')){
			$('#xl_5_5').attr("disabled","disabled");
		}
		if($('#xl_5_3').is(':checked')&&$('#xl_5_5').is(':checked')){
			$('#xl_5_4').attr("disabled","disabled");
		}
		if($('#xl_5_4').is(':checked')) {
			
			$('#xl_5_1').removeAttr("disabled");
			$('#xl_5_2').removeAttr("disabled");
			$('#xl_5_3').removeAttr("disabled");
			$('#xl_5_4').removeAttr("disabled");
			$('#xl_5_5').attr("disabled","disabled");
		}
		if($('#xl_5_4').is(':checked')&&$('#xl_5_2').is(':checked')){
			$('#xl_5_3').attr("disabled","disabled");
		}
		if($('#xl_5_4').is(':checked')&&$('#xl_5_3').is(':checked')){
			$('#xl_5_2').attr("disabled","disabled");
		}
		if($('#xl_5_5').is(':checked')) {
			$('#xl_5_1').removeAttr("disabled");
			$('#xl_5_2').removeAttr("disabled");
			$('#xl_5_3').removeAttr("disabled");
			$('#xl_5_4').attr("disabled","disabled");
			$('#xl_5_5').removeAttr("disabled");
		}
		if($('#xl_5_5').is(':checked')&&$('#xl_5_2').is(':checked')){
			
			$('#xl_5_3').attr("disabled","disabled");
		}
		if($('#xl_5_5').is(':checked')&&$('#xl_5_3').is(':checked')){
			$('#xl_5_2').attr("disabled","disabled");
		}
	})
	
	
	
	
	
	
	
	
	
	
	
	//jQuery对象   制冷/制热  系列
	var $xilie1 = $("#xilie1");  
	var $xl_1_1 = $("#xl_1_1");  
	var $xl_1_2 = $("#xl_1_2"); 
	var $xl_1_3 = $("#xl_1_3");
	
	$xilie1.click(function(){
		if($('#xl_1_1').is(':checked')){
			$('#x1').html(dome1);
			$("#content").load("furnish.php?content&id=16");
		}else if($('#xl_1_2').is(':checked')){
			$('#x1').html(dome2);
			$("#content").load("furnish.php?content&id=17");
		}else if($('#xl_1_3').is(':checked')){
			$('#x1').html(dome3);
			$("#content").load("furnish.php?content&id=18");
		}

		if($('#xl_1_1').is(':checked')!=true && $('#xl_1_2').is(':checked')!=true && $('#xl_1_3').is(':checked')!=true) {
			$('#x1').html('');
			$("#content").load("furnish.php?content&id='16,17,18'");
			$('#xl_1_1').removeAttr("disabled");
			$('#xl_1_2').removeAttr("disabled");
			$('#xl_1_3').removeAttr("disabled");
		}
		
		if($('#xl_1_1').is(':checked')) {
			$('#xl_1_1').removeAttr("disabled");
			$('#xl_1_2').attr("disabled","disabled");
			$('#xl_1_3').attr("disabled","disabled");
		}
		
		if($('#xl_1_2').is(':checked')) {
			$('#xl_1_1').attr("disabled","disabled");
			$('#xl_1_2').removeAttr("disabled");
			$('#xl_1_3').attr("disabled","disabled");
		}
		if($('#xl_1_3').is(':checked')) {
			$('#xl_1_1').attr("disabled","disabled");
			$('#xl_1_2').attr("disabled","disabled");
			$('#xl_1_3').removeAttr("disabled");
		}
	})
	
	
	
	//jQuery对象   制冷/采暖  系列
	var $xilie2 = $("#xilie2");
	var $xl_2_1 = $("#xl_2_1");
	var $xl_2_2 = $("#xl_2_2");
	var $xl_2_3 = $("#xl_2_3");
	var $xl_2_4 = $("#xl_2_4");
	
	$xilie2.click(function(){
		if($('#xl_2_1').is(':checked') && $('#xl_2_3').is(':checked')) {
			$('#x1').html(dome1+j+dome3);
			$("#content").load("furnish.php?content&id=19");
		}else if($('#xl_2_1').is(':checked') && $('#xl_2_4').is(':checked')) {
			$('#x1').html(dome1+j+dome4);
			$("#content").load("furnish.php?content&id=20");
		}else if($('#xl_2_2').is(':checked') && $('#xl_2_3').is(':checked')) {
			$('#x1').html(dome2+j+dome3);
			$("#content").load("furnish.php?content&id=21");
		}else if($('#xl_2_2').is(':checked') && $('#xl_2_4').is(':checked')) {
			$('#x1').html(dome2+j+dome4);
			$("#content").load("furnish.php?content&id=22");
		}else if($('#xl_2_1').is(':checked')) {
			$('#x1').html(dome1);
			$("#content").load("furnish.php?content&id='19,20'");
		}else if($('#xl_2_2').is(':checked')) {
			$('#x1').html(dome2);
			$("#content").load("furnish.php?content&id='21,22'");
		}else if($('#xl_2_3').is(':checked')) {
			$('#x1').html(dome3);
			$("#content").load("furnish.php?content&id='19,21'");
		}else if($('#xl_2_4').is(':checked')) {
			$('#x1').html(dome4);
			$("#content").load("furnish.php?content&id='20,22'");
		}

		if($('#xl_2_1').is(':checked')!=true && $('#xl_2_2').is(':checked')!=true && $('#xl_2_3').is(':checked')!=true && $('#xl_2_4').is(':checked')!=true) {
			$('#x1').html('');
			$("#content").load("furnish.php?content&id='19,20,21,22'");
			$('#xl_2_1').removeAttr("disabled");
			$('#xl_2_2').removeAttr("disabled");
			$('#xl_2_3').removeAttr("disabled");
			$('#xl_2_4').removeAttr("disabled");
		}
		
		if($('#xl_2_1').is(':checked')) {
			$('#xl_2_1').removeAttr("disabled");
			$('#xl_2_2').attr("disabled","disabled");
			$('#xl_2_3').removeAttr("disabled");
			$('#xl_2_4').removeAttr("disabled");
		}
		
		if($('#xl_2_1').is(':checked')&& $('#xl_2_3').is(':checked')) {
			$('#xl_2_1').removeAttr("disabled");
			$('#xl_2_2').attr("disabled","disabled");
			$('#xl_2_3').removeAttr("disabled");
			$('#xl_2_4').attr("disabled","disabled");
		}
		
		if($('#xl_2_1').is(':checked')&& $('#xl_2_4').is(':checked')) {
			$('#xl_2_1').removeAttr("disabled");
			$('#xl_2_2').attr("disabled","disabled");
			$('#xl_2_3').attr("disabled","disabled");
			$('#xl_2_4').removeAttr("disabled");
		}
		
		if($('#xl_2_2').is(':checked')) {
			$('#xl_2_1').attr("disabled","disabled");
			$('#xl_2_2').removeAttr("disabled");
			$('#xl_2_3').removeAttr("disabled");
			$('#xl_2_4').removeAttr("disabled");
		}
		
		if($('#xl_2_2').is(':checked')&& $('#xl_2_3').is(':checked')) {
			$('#xl_2_1').attr("disabled","disabled");
			$('#xl_2_2').removeAttr("disabled");
			$('#xl_2_3').removeAttr("disabled");
			$('#xl_2_4').attr("disabled","disabled");
		}
		
		if($('#xl_2_2').is(':checked')&& $('#xl_2_4').is(':checked')) {
			$('#xl_2_1').attr("disabled","disabled");
			$('#xl_2_2').removeAttr("disabled");
			$('#xl_2_3').attr("disabled","disabled");
			$('#xl_2_4').removeAttr("disabled");
		}
		
		if($('#xl_2_3').is(':checked')) {
			$('#xl_2_1').removeAttr("disabled");
			$('#xl_2_2').removeAttr("disabled");
			$('#xl_2_3').removeAttr("disabled");
			$('#xl_2_4').attr("disabled","disabled");
		}
		
		if($('#xl_2_3').is(':checked')&& $('#xl_2_1').is(':checked')) {
			$('#xl_2_1').removeAttr("disabled");
			$('#xl_2_2').attr("disabled","disabled");
			$('#xl_2_3').removeAttr("disabled");
			$('#xl_2_4').attr("disabled","disabled");
		}
		
		if($('#xl_2_3').is(':checked')&& $('#xl_2_2').is(':checked')) {
			$('#xl_2_1').attr("disabled","disabled");
			$('#xl_2_2').removeAttr("disabled");
			$('#xl_2_3').removeAttr("disabled");
			$('#xl_2_4').attr("disabled","disabled");
		}
		
		if($('#xl_2_4').is(':checked')) {
			$('#xl_2_1').removeAttr("disabled");
			$('#xl_2_2').removeAttr("disabled");
			$('#xl_2_3').attr("disabled","disabled");
			$('#xl_2_4').removeAttr("disabled");
		}
		
		if($('#xl_2_4').is(':checked')&& $('#xl_2_1').is(':checked')) {
			$('#xl_2_1').removeAttr("disabled");
			$('#xl_2_2').attr("disabled","disabled");
			$('#xl_2_3').attr("disabled","disabled");
			$('#xl_2_4').removeAttr("disabled");
		}
		
		if($('#xl_2_4').is(':checked')&& $('#xl_2_2').is(':checked')) {
			$('#xl_2_1').attr("disabled","disabled");
			$('#xl_2_2').removeAttr("disabled");
			$('#xl_2_3').attr("disabled","disabled");
			$('#xl_2_4').removeAttr("disabled");
		}
		
	})
	
	
	
	
	//jQuery对象  采暖/非循环热水  系列
	var $xilie3 = $("#xilie3");
	var $xl_3_1 = $("#xl_3_1");
	var $xl_3_2 = $("#xl_3_2");
	var $xl_3_3 = $("#xl_3_3");
	var $xl_3_4 = $("#xl_3_4");
	
	$xilie3.click(function(){
		if($('#xl_3_1').is(':checked') && $('#xl_3_4').is(':checked')) {
			$('#x1').html(dome1+j+dome4);
			$("#content").load("furnish.php?content&id=29");
		}else if($('#xl_3_2').is(':checked') && $('#xl_3_4').is(':checked')) {
			$('#x1').html(dome2+j+dome4);
			$("#content").load("furnish.php?content&id=30");
		}else if($('#xl_3_3').is(':checked') && $('#xl_3_4').is(':checked')) {
			$('#x1').html(dome3+j+dome4);
			$("#content").load("furnish.php?content&id=31");
		}else if($('#xl_3_1').is(':checked')) {
			$('#x1').html(dome1);
			$("#content").load("furnish.php?content&id=29");
		}else if($('#xl_3_2').is(':checked')) {
			$('#x1').html(dome2);
			$("#content").load("furnish.php?content&id=30");
		}else if($('#xl_3_3').is(':checked')) {
			$('#x1').html(dome3);
			$("#content").load("furnish.php?content&id=31");
		}else if($('#xl_3_4').is(':checked')) {
			$('#x1').html(dome4);
			$("#content").load("furnish.php?content&id='29,30,31'");
		}
	
		if($('#xl_3_1').is(':checked')!=true && $('#xl_3_2').is(':checked')!=true && $('#xl_3_3').is(':checked')!=true && $('#xl_3_4').is(':checked')!=true) {
			$('#x1').html('');
			$("#content").load("furnish.php?content&id='29,30,31'");
			$('#xl_3_1').removeAttr("disabled");
			$('#xl_3_2').removeAttr("disabled");
			$('#xl_3_3').removeAttr("disabled");
			$('#xl_3_4').removeAttr("disabled");
		}
		
		if($('#xl_3_1').is(':checked')) {
			$('#xl_3_1').removeAttr("disabled");
			$('#xl_3_2').attr("disabled","disabled");
			$('#xl_3_3').attr("disabled","disabled");
			$('#xl_3_4').removeAttr("disabled");
		}
		
		if($('#xl_3_4').is(':checked')) {
			$('#xl_3_1').removeAttr("disabled");
			$('#xl_3_2').removeAttr("disabled");
			$('#xl_3_3').removeAttr("disabled");
			$('#xl_3_4').removeAttr("disabled");
		}
		
		if($('#xl_3_1').is(':checked')&& $('#xl_3_4').is(':checked')) {
			$('#xl_3_1').removeAttr("disabled");
			$('#xl_3_2').attr("disabled","disabled");
			$('#xl_3_3').attr("disabled","disabled");
			$('#xl_3_4').removeAttr("disabled");
		}
		
		if($('#xl_3_2').is(':checked')) {
			$('#xl_3_1').attr("disabled","disabled");
			$('#xl_3_2').removeAttr("disabled");
			$('#xl_3_3').attr("disabled","disabled");
			$('#xl_3_4').removeAttr("disabled");
		}
		
		if($('#xl_3_2').is(':checked')&& $('#xl_3_4').is(':checked')) {
			$('#xl_3_1').attr("disabled","disabled");
			$('#xl_3_2').removeAttr("disabled");
			$('#xl_3_3').attr("disabled","disabled");
			$('#xl_3_4').removeAttr("disabled");
		}
		
		if($('#xl_3_3').is(':checked')) {
			$('#xl_3_1').attr("disabled","disabled");
			$('#xl_3_2').attr("disabled","disabled");
			$('#xl_3_3').removeAttr("disabled");
			$('#xl_3_4').removeAttr("disabled");
		}
		
		if($('#xl_3_3').is(':checked')&& $('#xl_3_4').is(':checked')) {
			$('#xl_3_1').attr("disabled","disabled");
			$('#xl_3_2').attr("disabled","disabled");
			$('#xl_3_3').removeAttr("disabled");
			$('#xl_3_4').removeAttr("disabled");
		}
		
		
	})
	
	
	
	
	//jQuery对象  采暖/循环热水  系列
	var $xilie4 = $("#xilie4");
	var $xl_4_1 = $("#xl_4_1");
	var $xl_4_2 = $("#xl_4_2");
	var $xl_4_3 = $("#xl_4_3");
	var $xl_4_4 = $("#xl_4_4");
	var $xl_4_5 = $("#xl_4_5");
	
	$xilie4.click(function(){
		if($('#xl_4_3').is(':checked')&& $('#xl_4_4').is(':checked')&& $('#xl_4_5').is(':checked')) {
			$('#x1').html(dome3+j+dome4+j+dome5);
			$("#content").load("furnish.php?content&id=28");
		}else if($('#xl_4_1').is(':checked')&& $('#xl_4_4').is(':checked') && $('#xl_4_5').is(':checked')) {
			$('#x1').html(dome1+j+dome4+j+dome5);
			$("#content").load("furnish.php?content&id=25");
		}else if($('#xl_4_1').is(':checked')&& $('#xl_4_4').is(':checked')) {
			$('#x1').html(dome1+j+dome4);
			$("#content").load("furnish.php?content&id='23,25'");
		}else if($('#xl_4_1').is(':checked') && $('#xl_4_5').is(':checked')) {
			$('#x1').html(dome1+j+dome5);
			$("#content").load("furnish.php?content&id=25");
		}else if($('#xl_4_2').is(':checked') && $('#xl_4_4').is(':checked')) {
			$('#x1').html(dome2+j+dome4);
			$("#content").load("furnish.php?content&id='24,26'");
		}else if($('#xl_4_2').is(':checked') && $('#xl_4_5').is(':checked')) {
			$('#x1').html(dome2+j+dome5);
			$("#content").load("furnish.php?content&id=26");
		}else if($('#xl_4_3').is(':checked') && $('#xl_4_4').is(':checked')) {
			$('#x1').html(dome3+j+dome4);
			$("#content").load("furnish.php?content&id='27,28'");
		}else if($('#xl_4_3').is(':checked') && $('#xl_4_5').is(':checked')) {
			$('#x1').html(dome3+j+dome5);
			$("#content").load("furnish.php?content&id=28");
		}else if($('#xl_4_4').is(':checked') && $('#xl_4_5').is(':checked')) {
			$('#x1').html(dome4+j+dome5);
			$("#content").load("furnish.php?content&id='25,26,28'");
		}else if($('#xl_4_1').is(':checked')) {
			$('#x1').html(dome1);
			$("#content").load("furnish.php?content&id='23,25'");
		}else if($('#xl_4_2').is(':checked')) {
			$('#x1').html(dome2);
			$("#content").load("furnish.php?content&id='24,26'");
		}else if($('#xl_4_3').is(':checked')) {
			$('#x1').html(dome3);
			$("#content").load("furnish.php?content&id='27,28'");
		}else if($('#xl_4_4').is(':checked')) {
			$('#x1').html(dome4);
			$("#content").load("furnish.php?content&id='23,24,25,26,27,28'");
		}else if($('#xl_4_5').is(':checked')) {
			$('#x1').html(dome5);
			$("#content").load("furnish.php?content&id='25,26,28'");
		}
	
		if($('#xl_4_1').is(':checked')!=true && $('#xl_4_2').is(':checked')!=true && $('#xl_4_3').is(':checked')!=true && $('#xl_4_4').is(':checked')!=true && $('#xl_4_5').is(':checked')!=true) {
			$('#x1').html('');
			$("#content").load("furnish.php?content&id='23,24,25,26,27,28'");
			$('#xl_4_1').removeAttr("disabled");
			$('#xl_4_2').removeAttr("disabled");
			$('#xl_4_3').removeAttr("disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}
		
		if($('#xl_4_1').is(':checked')) {
			$('#xl_4_1').removeAttr("disabled");
			$('#xl_4_2').attr("disabled","disabled");
			$('#xl_4_3').attr("disabled","disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}
		
		if($('#xl_4_2').is(':checked')) {
			$('#xl_4_1').attr("disabled","disabled");
			$('#xl_4_2').removeAttr("disabled");
			$('#xl_4_3').attr("disabled","disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}
		
		if($('#xl_4_3').is(':checked')) {
			$('#xl_4_1').attr("disabled","disabled");
			$('#xl_4_2').attr("disabled","disabled");
			$('#xl_4_3').removeAttr("disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}
		
		if($('#xl_4_4').is(':checked')) {
			$('#xl_4_1').removeAttr("disabled");
			$('#xl_4_2').removeAttr("disabled");
			$('#xl_4_3').removeAttr("disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}
		
		if($('#xl_4_4').is(':checked')&& $('#xl_4_1').is(':checked')) {
			$('#xl_4_1').removeAttr("disabled");
			$('#xl_4_2').attr("disabled","disabled");
			$('#xl_4_3').attr("disabled","disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}
		
		if($('#xl_4_4').is(':checked')&& $('#xl_4_2').is(':checked')) {
			$('#xl_4_1').attr("disabled","disabled");
			$('#xl_4_2').removeAttr("disabled");
			$('#xl_4_3').attr("disabled","disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}
		
		if($('#xl_4_4').is(':checked')&& $('#xl_4_3').is(':checked')) {
			$('#xl_4_1').attr("disabled","disabled");
			$('#xl_4_2').attr("disabled","disabled");
			$('#xl_4_3').removeAttr("disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}
		
		if($('#xl_4_5').is(':checked')) {
			$('#xl_4_1').removeAttr("disabled");
			$('#xl_4_2').removeAttr("disabled");
			$('#xl_4_3').removeAttr("disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}
		
		if($('#xl_4_5').is(':checked')&& $('#xl_4_1').is(':checked')) {
			$('#xl_4_1').removeAttr("disabled");
			$('#xl_4_2').attr("disabled","disabled");
			$('#xl_4_3').attr("disabled","disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}
		
		if($('#xl_4_5').is(':checked')&& $('#xl_4_2').is(':checked')) {
			$('#xl_4_1').attr("disabled","disabled");
			$('#xl_4_2').removeAttr("disabled");
			$('#xl_4_3').attr("disabled","disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}
		
		if($('#xl_4_5').is(':checked')&& $('#xl_4_3').is(':checked')) {
			$('#xl_4_1').attr("disabled","disabled");
			$('#xl_4_2').attr("disabled","disabled");
			$('#xl_4_3').removeAttr("disabled");
			$('#xl_4_4').removeAttr("disabled");
			$('#xl_4_5').removeAttr("disabled");
		}		
	})

	
	
	//jQuery对象  净水/纯水  系列
	var $xilie6 = $("#xilie6");
	var $xl_6_1 = $("#xl_6_1");
	var $xl_6_2 = $("#xl_6_2");
	var $xl_6_3 = $("#xl_6_3");
	var $xl_6_4 = $("#xl_6_4");
	var $xl_6_5 = $("#xl_6_5");
	var $xl_6_6 = $("#xl_6_6");
	
	$xilie6.click(function(){
		if($('#xl_6_1').is(':checked') && $('#xl_6_2').is(':checked') && $('#xl_6_3').is(':checked') && $('#xl_6_5').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#x1').html(dome1+j+dome2+j+dome3+j+dome5+j+dome6);
			$("#content").load("furnish.php?content&id='15'");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_3').is(':checked') && $('#xl_6_5').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#x1').html(dome1+j+dome3+j+dome5+j+dome6);
			$("#content").load("furnish.php?content&id=12");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_2').is(':checked') && $('#xl_6_3').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#x1').html(dome1+j+dome2+j+dome3+j+dome6);
			$("#content").load("furnish.php?content&id=15");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_2').is(':checked') && $('#xl_6_5').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#x1').html(dome1+j+dome2+j+dome5+j+dome6);
			$("#content").load("furnish.php?content&id='12,15'");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_2').is(':checked') && $('#xl_6_3').is(':checked') && $('#xl_6_5').is(':checked')) {
			$('#x1').html(dome1+j+dome2+j+dome3+j+dome5);
			$("#content").load("furnish.php?content&id=14,15");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_2').is(':checked') && $('#xl_6_3').is(':checked') && $('#xl_6_4').is(':checked')) {
			$('#x1').html(dome1+j+dome2+j+dome3+j+dome4);
			$("#content").load("furnish.php?content&id=13");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_2').is(':checked') && $('#xl_6_3').is(':checked')) {
			$('#x1').html(dome1+j+dome2+j+dome3);
			$("#content").load("furnish.php?content&id='13,14,15'");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_2').is(':checked') && $('#xl_6_5').is(':checked')) {
			$('#x1').html(dome1+j+dome2+j+dome5);
			$("#content").load("furnish.php?content&id='11,12,14,15'");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_2').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#x1').html(dome1+j+dome2+j+dome6);
			$("#content").load("furnish.php?content&id='12,15'");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_2').is(':checked') && $('#xl_6_4').is(':checked')) {
			$('#x1').html(dome1+j+dome2+j+dome4);
			$("#content").load("furnish.php?content&id='10,13'");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_3').is(':checked') && $('#xl_6_4').is(':checked')) {
			$('#x1').html(dome1+j+dome3+j+dome4);
			$("#content").load("furnish.php?content&id=13");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_3').is(':checked') && $('#xl_6_5').is(':checked')) {
			$('#x1').html(dome1+j+dome3+j+dome5);
			$("#content").load("furnish.php?content&id='13,14,15'");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_3').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#x1').html(dome1+j+dome3+j+dome6);
			$("#content").load("furnish.php?content&id=15");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_5').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#x1').html(dome1+j+dome5+j+dome6);
			$("#content").load("furnish.php?content&id='12,15'");
		}else if($('#xl_6_3').is(':checked') && $('#xl_6_5').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#x1').html(dome3+j+dome5+j+dome6);
			$("#content").load("furnish.php?content&id=15");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_2').is(':checked')) {
			$('#x1').html(dome1+j+dome2);
			$("#content").load("furnish.php?content&id='10,11,12,13,14,15'");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_3').is(':checked')) {
			$('#x1').html(dome1+j+dome3);
			$("#content").load("furnish.php?content&id='13,14,15'");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_4').is(':checked')) {
			$('#x1').html(dome1+j+dome4);
			$("#content").load("furnish.php?content&id='10,13'");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_5').is(':checked')) {
			$('#x1').html(dome1+j+dome5);
			$("#content").load("furnish.php?content&id='11,12,14,15'");
		}else if($('#xl_6_1').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#x1').html(dome1+j+dome6);
			$("#content").load("furnish.php?content&id='12,15'");
		}else if($('#xl_6_2').is(':checked') && $('#xl_6_3').is(':checked')) {
			$('#x1').html(dome2+j+dome3);
			$("#content").load("furnish.php?content&id='13,14,15'");
		}else if($('#xl_6_4').is(':checked') && $('#xl_6_2').is(':checked')) {
			$('#x1').html(dome2+j+dome4);
			$("#content").load("furnish.php?content&id='10,13'");
		}else if($('#xl_6_5').is(':checked') && $('#xl_6_2').is(':checked')) {
			$('#x1').html(dome2+j+dome5);
			$("#content").load("furnish.php?content&id='11,12,14,15'");
		}else if($('#xl_6_6').is(':checked') && $('#xl_6_2').is(':checked')) {
			$('#x1').html(dome2+j+dome6);
			$("#content").load("furnish.php?content&id='12,15'");
		}else if($('#xl_6_3').is(':checked') && $('#xl_6_4').is(':checked')) {
			$('#x1').html(dome3+j+dome4);
			$("#content").load("furnish.php?content&id=13");
		}else if($('#xl_6_3').is(':checked') && $('#xl_6_5').is(':checked')) {
			$('#x1').html(dome3+j+dome5);
			$("#content").load("furnish.php?content&id='14,15'");
		}else if($('#xl_6_3').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#x1').html(dome3+j+dome6);
			$("#content").load("furnish.php?content&id=15");
		}else if($('#xl_6_5').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#x1').html(dome5+j+dome6);
			$("#content").load("furnish.php?content&id='12,15'");
		}else if($('#xl_6_1').is(':checked')) {
			$('#x1').html(dome1);
			$("#content").load("furnish.php?content&id='10,11,12,13,14,15'");
		}else if($('#xl_6_2').is(':checked')) {
			$('#x1').html(dome2);
			$("#content").load("furnish.php?content&id='10,11,12,13,14,15'");
		}else if($('#xl_6_3').is(':checked')) {
			$('#x1').html(dome3);
			$("#content").load("furnish.php?content&id='14,15'");
		}else if($('#xl_6_4').is(':checked')) {
			$('#x1').html(dome4);
			$("#content").load("furnish.php?content&id='10,13'");
		}else if($('#xl_6_5').is(':checked')) {
			$('#x1').html(dome5);
			$("#content").load("furnish.php?content&id='11,12,14,15'");
		}else if($('#xl_6_6').is(':checked')) {
			$('#x1').html(dome6);
			$("#content").load("furnish.php?content&id='12,15'");
		}
	
		if($('#xl_6_1').is(':checked')!=true && $('#xl_6_2').is(':checked')!=true && $('#xl_6_3').is(':checked')!=true && $('#xl_6_4').is(':checked')!=true && $('#xl_6_5').is(':checked')!=true && $('#xl_6_6').is(':checked')!=true) {
			$('#x1').html('');
			$("#content").load("furnish.php?content&id='10,11,12,13,14,15'");
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').removeAttr("disabled");
			$('#xl_6_5').removeAttr("disabled");
			$('#xl_6_6').removeAttr("disabled");
		}
		
		if($('#xl_6_1').is(':checked')) {
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').removeAttr("disabled");
			$('#xl_6_5').removeAttr("disabled");
			$('#xl_6_6').removeAttr("disabled");
		}
		
		if($('#xl_6_1').is(':checked')&& $('#xl_6_2').is(':checked')) {
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').removeAttr("disabled");
			$('#xl_6_5').removeAttr("disabled");
			$('#xl_6_6').removeAttr("disabled");
		}
		
		if($('#xl_6_1').is(':checked')&& $('#xl_6_2').is(':checked') && $('#xl_6_3').is(':checked')) {
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').removeAttr("disabled");
			$('#xl_6_5').removeAttr("disabled");
			$('#xl_6_6').removeAttr("disabled");
		}
		
		
		
		if($('#xl_6_1').is(':checked')&& $('#xl_6_2').is(':checked') && $('#xl_6_3').is(':checked') && $('#xl_6_4').is(':checked')) {
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').removeAttr("disabled");
			$('#xl_6_5').attr("disabled","disabled");
			$('#xl_6_6').attr("disabled","disabled");
		}
		
		if($('#xl_6_1').is(':checked')&& $('#xl_6_2').is(':checked') && $('#xl_6_3').is(':checked') && $('#xl_6_5').is(':checked')) {
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').attr("disabled","disabled");
			$('#xl_6_5').removeAttr("disabled");
			$('#xl_6_6').removeAttr("disabled");
		}
		
		if($('#xl_6_1').is(':checked')&& $('#xl_6_2').is(':checked') && $('#xl_6_3').is(':checked') && $('#xl_6_6').is(':checked')) {
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').attr("disabled","disabled");
			$('#xl_6_5').removeAttr("disabled");
			$('#xl_6_6').removeAttr("disabled");
		}
		
		if($('#xl_6_2').is(':checked')) {
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').removeAttr("disabled");
			$('#xl_6_5').removeAttr("disabled");
			$('#xl_6_6').removeAttr("disabled");
		}
		
		if($('#xl_6_3').is(':checked')) {
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').removeAttr("disabled");
			$('#xl_6_5').removeAttr("disabled");
			$('#xl_6_6').removeAttr("disabled");
		}
		
		if($('#xl_6_4').is(':checked')) {
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').removeAttr("disabled");
			$('#xl_6_5').attr("disabled","disabled");
			$('#xl_6_6').attr("disabled","disabled");
		}
		
		if($('#xl_6_5').is(':checked')) {
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').attr("disabled","disabled");
			$('#xl_6_5').removeAttr("disabled");
			$('#xl_6_6').removeAttr("disabled");
		}
		
		if($('#xl_6_6').is(':checked')) {
			$('#xl_6_1').removeAttr("disabled");
			$('#xl_6_2').removeAttr("disabled");
			$('#xl_6_3').removeAttr("disabled");
			$('#xl_6_4').attr("disabled","disabled");
			$('#xl_6_5').removeAttr("disabled");
			$('#xl_6_6').removeAttr("disabled");
		}
				
	})



	//jQuery对象  循环热水  系列
	var $xilie7 = $("#xilie7");
	var $xl_7_1 = $("#xl_7_1");
	var $xl_7_2 = $("#xl_7_2");
	
	$xilie7.click(function(){
		if($('#xl_7_1').is(':checked')) {
			$('#x1').html(dome1);
			$("#content").load("furnish.php?content&id=36");
		}else if($('#xl_7_2').is(':checked')) {
			$('#x1').html(dome2);
			$("#content").load("furnish.php?content&id=37'");
		}
	
		if($('#xl_7_1').is(':checked')!=true && $('#xl_7_2').is(':checked')!=true) {
			$('#x1').html('');
			$("#content").load("furnish.php?content&id='36,37'");
			$('#xl_7_1').removeAttr("disabled");
			$('#xl_7_2').removeAttr("disabled");
		}
		
		if($('#xl_7_1').is(':checked')) {
			$('#xl_7_1').removeAttr("disabled");
			$('#xl_7_2').attr("disabled","disabled");
		}
		
		if($('#xl_7_2').is(':checked')) {
			$('#xl_7_1').attr("disabled","disabled");
			$('#xl_7_2').removeAttr("disabled");
		}
				
	})
	
	//jQuery对象  中央吸尘  系列
	var $xilie8 = $("#xilie8");
	var $xl_8_1 = $("#xl_8_1");
	var $xl_8_2 = $("#xl_8_2");
	
	$xilie8.click(function(){
		if($('#xl_8_1').is(':checked')) {
			$('#x1').html(dome1);
			$("#content").load("furnish.php?content&id=32");
		}else if($('#xl_8_2').is(':checked')) {
			$('#x1').html(dome2);
			$("#content").load("furnish.php?content&id=33");
		}
	
		if($('#xl_8_1').is(':checked')!=true && $('#xl_8_2').is(':checked')!=true) {
			$('#x1').html('');
			$("#content").load("furnish.php?content&id='32,33'");
			$('#xl_8_1').removeAttr("disabled");
			$('#xl_8_2').removeAttr("disabled");
		}
		
		if($('#xl_8_1').is(':checked')) {
			$('#xl_8_1').removeAttr("disabled");
			$('#xl_8_2').attr("disabled","disabled");
		}
		
		if($('#xl_8_2').is(':checked')) {
			$('#xl_8_1').attr("disabled","disabled");
			$('#xl_8_2').removeAttr("disabled");
		}
				
	})





















});



/*$("#xl_5_2").attr("disabled","disabled");*/

/*$('#xl_5_2').removeAttr("disabled");
$('#xl_5_3').removeAttr("disabled");
$('#xl_5_4').removeAttr("disabled");
$('#xl_5_5').removeAttr("disabled");*/