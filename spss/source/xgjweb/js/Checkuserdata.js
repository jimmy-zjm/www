//默认情况下不能提交
var Checkmobilephone=false;
var Checkname=false;
var Checkpassword=false;
var Checkrpassword=false;
var Checkonlybox=false;
//var Checkidentifyingcode=false;

//无卷

//手机号判断
function checkmobilephone(obj)
{
	
	var re=/^[1][34578][0-9]{9}$/;
	var reg=new RegExp(re);
	if(reg.test(obj.value))
	{     
		 
		 //判断手机号码是否注册过
		$.post('user.php?checkRegister',{usermobile_phone:$("#mobile_phone").val()},
		function(data){
			 
			if(data==1)
				{
				  $("#span_mobile_phone").text("您已注册过，不可再次注册");
				  Checkmobilephone=false;
				}
			else
				{
				    
				 $("#span_mobile_phone").text("");
				    Checkmobilephone=true;
				}
			
		              }
	           )
		
		
	}
	else if(obj.value=="")
		{
		  $("#span_mobile_phone").text("请输入您的手机号码");
		  Checkmobilephone=false;
		}
	else
		{
		  $("#span_mobile_phone").text("请输入正确的手机号码");
		  Checkmobilephone=false;
		}

}

//昵称判断
function checknickname(obj)
{
  
  var re=/^\w{6,15}$/;
  var reg=new RegExp(re);
 
  if(reg.test(obj.value))
	  {
	  
	  //判断昵称是否重名
	  $.post('user.php?checkRegister',{userName:$("#userName").val()},
			  
		function (data){
		
		  if(data==1) 
			 {
			   $("#span_nickname").text("用户名被占用");	 
		        Checkname=false;
		      
			 }
		 else
			 {
			   $("#span_nickname").text("");
			     Checkname=true;
			 }
	     }
	     )
		
	   }
	
  else if (obj.value=="")
	  {
	    $("#span_nickname").text("请输入您的昵称！");
	     Checkname=false;
	  }
	  
 else {
	
	    $("#span_nickname").text("请输入为6-15位数字或英文！");
	    Checkname=false;
	  }

}
//密码判断
function checkpassword(obj)
{
	
var re=/^\w{6,15}$/;
var reg=new RegExp(re);
if(reg.test(obj.value))
	  {
	  $("#span_passWord").text("");
	    Checkpassword=true;
	  }


else if(obj.value=="")
 { 
   $("#span_passWord").text("请输入您的密码！");
   Checkpassword=false;
 }
else
	  {
	    $("#span_passWord").text("请正确填写您的密码！");
	    Checkpassword=false;
	  }

}

//确认密码判断
function  checkrpassword (obj)
{  var password=document.getElementById("password01").value;
	if(obj.value==password)
		{
		$("#span_rpassWord").text("");
		   Checkrpassword=true;
		}
	else
		{
		$("#span_rpassWord").text("两次密码不相同，请重新输入！");
	     Checkrpassword=false;
		
		}
}

//验证码
function gain()
{
	if(Checkmobilephone){
		var tel=$("#mobile_phone").val();
		// alert(tel);
		 $.post('user.php?message',{tel:tel},
			function (data){
			  if(data==1){
		     	set();
				}
	     	}
	    )
   }
}
var wait=60;
function set()
{ 
	var obj=document.getElementById("getyzm"); 
	if (wait == 0) 
	{
		
		obj.removeAttribute("disabled");
		obj.value="免费获取验证码";
	    wait = 60;
	}
	else
	{
		 
		 obj.setAttribute("disabled", true);
		 obj.value="重新发送(" + wait + ")";
	      wait--;
	     setTimeout(function (){set()},1000);
	 }      
	
}
	
//判断用户是否选择
// function checkbox()
// {  
// 	 var hasChk = $('#Bike').is(':checked'); 
// 	 if(hasChk)
// 		 {
// 		 $("#checked01").text("");
// 		 Checkonlybox=true;
// 		 }
// 	 else{
// 	 	$("#checked01").text("请同意新感觉服务协议！");
// 		 Checkonlybox=false;
// 	 }
// }

//提交
function tijiao_Num01(){  //当所有条件成立才可以提交
var hasChk = $('#Bike').is(':checked'); 
	 if(hasChk)
		 {
		 $("#checked01").text("");
		 Checkonlybox=true;
		 }
	 else{
	 	$("#checked01").text("请同意新感觉服务协议！");
		 Checkonlybox=false;
	 }
  if ( Checkpassword && Checkrpassword &&Checkmobilephone && Checkonlybox){
  	//alert('aaa');
	    $("#form_num01").submit();
	}
}


//有卷

//默认情况下不可提交
var Checkmobilephone_num02=false;
var Checkname_num02=false;
var Checkpassword_num02=false;
var Checkrpassword_num02=false;
var Checkcoupon=false;
var Checkcouponpassword=false;
var Checkonlybox_num02=false;
//var Checkidentifyingcode_num02=false;

//手机号码判断

function checkmobilephone_num02(obj)
{
	
	var re=/^[1][358][0-9]{9}$/;
	var reg=new RegExp(re);
	if(reg.test(obj.value))
	{     
		 
		 //判断手机号码是否注册过
		$.post('user.php?checkRegister',{usermobile_phone:$("#mobile_phone_num02").val()},
		function(data){
			 
			if(data==1)
				{
				  $("#span_mobile_phone_num02").text("您已注册过，不可再次注册");
				    Checkmobilephone_num02=false;
				}
			else
				{
				    
				 $("#span_mobile_phone_num02").text("");
				   Checkmobilephone_num02=true;
				}
			
		         }
		         )
	           
		
		
	}
	else  if(obj.value=="")
		{
		 $("#span_mobile_phone_num02").text("请输入您的手机号码");
		  Checkmobilephone_num02=false;
		}
	else
		{
		  $("#span_mobile_phone_num02").text("请输入正确的手机号码");
		   Checkmobilephone_num02=false;
		}

}





//昵称判断
function checknickname_num02(obj)
{
  
  var re= /\w{6,12}$/;
  var reg=new RegExp(re);
 
  if(reg.test(obj.value))
	  {
	  
	    //判断昵称是否重名
	   $.post('user.php?checkRegister',{userName:$("#userName_num02").val()},
			  
		function (data){
		  
		  if(data==1) 
			 {
			   $("#span_nickname_num02").text("用户名被占用");	 
			   Checkname_num02=false;
		      
			 }
		 else
			 {
			   $("#span_nickname_num02").text("");
			   Checkname_num02=true;
			 }
	  })
		
	   }
	  

  
  else if (obj.value=="")
	  {
	    $("#span_nickname_num02").text("请输入您的昵称！");
	    Checkname_num02=false;
	  }
	  
 else {
	
	    $("#span_nickname_num02").text("请输入为6-15位数字或英文！");
	      Checkname_num02=false;
	  }

}

//密码判断
function checkpassword_num02(obj)
{
	
var re=/^\w{6,15}$/;
var reg=new RegExp(re);
if(reg.test(obj.value))
	  {
	    
	  $("#span_passWord_num02").text("");
	  Checkpassword_num02=true;
	  }


else if(obj.value=="")
 { 
   $("#span_passWord_num02").text("请输入您的密码！");
   Checkpassword_num02=false;
 }
else
	  {
	    $("#span_passWord_num02").text("请正确填写您的密码！");
	    Checkpassword_num02=false;
	  }

}

//确认密码判断
function  checkrpassword_num02 (obj)
{  var password=document.getElementById("password04").value;
	if(obj.value==password)
		{
		 
		 $("#span_rpassWord_num02").text("");
		 Checkrpassword_num02=true;
		}
	else
		{
		$("#span_rpassWord_num02").text("两次密码不相同，请重新输入！");
		 Checkrpassword_num02=false;
		
		}
}

//入卷账号判断
function checkrollinto(obj){	 
	if(obj.value==""){
		$("#span_rollinto").text("请输入您的入券账号");
	    Checkcoupon=false;
	}else{
	   //判断卷账号是否使用过
	   $.post('user.php?checkRegister',{coupon:$("#coupon").val()},
		function (data){
		  	if(data==1){
			   $("#span_rollinto").text("优惠券不存在");	 
			   Checkcoupon=false;
			}else if(data==2){
			   $("#span_rollinto").text("优惠券已使用");
			   Checkcoupon=false;
			}else if(data==3){
				$("#span_rollinto").text("");
				Checkcoupon=true;
			}
	  })
	}
}
//入卷密码判断
function checkrollintopassword(obj)
{
    //判断数据库是否存在
	$.post("user.php?checkRegister",{coupon:$("#coupon").val(),cpassword:$("#password03").val()},
			
	function(data){
		if(data==0){
		   $("#span_rollintopassword").text("密码错误，请重新输入");
		   Checkcouponpassword=false;
		}else if(data==1){
		   $("#span_rollintopassword").text("");
		    Checkcouponpassword=true;
		}
    
	})

}


//验证码
function gain_num02()
{
	
  if(Checkmobilephone_num02)
  	var tel=$("#mobile_phone02").val();
		//alert(tel);
		 $.post('user.php?message',{tel:tel},
			function (data){
			  if(data==1){
		     	 set_num02();
				}
	     	}
	    )
}

var rwait=60;
function set_num02()
{ 
	
	var robj=document.getElementById("getryzm"); 
	if (rwait == 0) 
	{
		
		robj.removeAttribute("disabled");
		robj.value="免费获取验证码";
	    rwait = 60;
	}
	else
	{
		 
		 robj.setAttribute("disabled", true);
		 robj.value="重新发送(" + rwait + ")";
	      rwait--;
	     setTimeout(function (){set_num02()},1000);
	 }      
	
}

//判断用户是否选择
// function checkbox_num02()
// {  
	  
// 	 var hasChk = $('#Bike_num02').is(':checked'); 
// 	 if(hasChk)
// 		 {
// 		 $("#checked02").text("");
// 		 Checkonlybox_num02=true;
// 		 }
// 	 else{
// 	 	$("#checked02").text("请同意新感觉服务协议！");

// 		 Checkonlybox_num02=false;
// 	 }
// }

	
//提交
function tijiao_Num02()
{  
	
var hasChk = $('#Bike_num02').is(':checked'); 
	 if(hasChk)
		 {
		 $("#checked02").text("");
		 Checkonlybox_num02=true;
		 }
	 else{
	 	$("#checked02").text("请同意新感觉服务协议！");

		 Checkonlybox_num02=false;
	 }
//当所有条件成立才可以提交
  if ( Checkmobilephone_num02  && Checkpassword_num02 && Checkrpassword_num02 && Checkcoupon  && Checkcouponpassword && Checkonlybox_num02)
	  {
	      $("#form_num02").submit();
	  }

}
