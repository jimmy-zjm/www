
//默认情况下不能提交
var Checkmobilephone=false;
var Checkname=false;
var Checkpassword=false;
var Checkrpassword=false;
var Checkonlybox=false;
var Checkidentifyingcode=false;


function  pd_sj (obj){

	//判断手机号
	var re=/^[1][358][0-9]{9}$/;
	var reg=new RegExp(re);
	if(reg.test(obj.value))
	{   
		 $("#pd_sj").text("");
		  Checkmobilephone=true;
	}
	else if(obj.value=="")
		{
		  $("#pd_sj").text("请输入您的手机号码");
		  Checkmobilephone=false;
		}
	else
		{
		  $("#pd_sj").text("您的格式不对，请重新输入");
		  Checkmobilephone=false;
		}

}

   function pd_pwd(obj){

       //判断密码

       var re=/^\w{6,15}$/;
       var reg=new RegExp(re);
       if(reg.test(obj.value))
       {
           $("#pd_pwd").text("");
           Checkpassword=true;
       }


       else if(obj.value=="")
       {
           $("#pd_pwd").text("请输入您的密码！");
           Checkpassword=false;
       }
       else
       {
           $("#pd_pwd").text("请正确填写您的密码！");
           Checkpassword=false;
       }

   }
    function pd_rpwd(obj){
        //判断两次密码是否一致

        var password=document.getElementById("passWord").value;
        if(obj.value==password)
        {

            $("#pd_rpwd").text("");
             Checkrpassword=true;
        }
        else
        {

         $("#pd_rpwd").text("您输入的两次密码不一样，请重新输入！");
            Checkrpassword=false;

        }

    }

        function gain_yzm(){
            //获取验证码
            if(Checkmobilephone)
            {

                set();
            }

             }

var wait=60;
function set()
{

    var obj=document.getElementById("yzm");
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




function checkbox()
{
    //判断用户是否选择
    var hasChk = $('#Bike').is(':checked');
    if(hasChk)
    {
        Checkonlybox=true;
    }
    else{
        Checkonlybox=false;
    }
}


  function  tijiao() {



      //当所有条件成立才可以提交
      if (Checkpassword  && Checkrpassword && Checkmobilephone  && Checkonlybox  &&  Checkidentifyingcode )
      {
    	 
          $("#f2").submit();
      }





  }


