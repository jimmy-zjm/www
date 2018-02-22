/**
 * Created by 唐文权 on 2015/12/31.
 */

$(function(){

    $("#loginSubmitId").click(function(){

        if($("#userNameTextId").val() == ""){
            $("#userNameErrorDivId").show();
            $("#userNameTextId").focus();
            return false;
        }else{
            $("#userNameErrorDivId").hide();
        }

        if($("#passWordId").val() == ""){
            $("#passwordErrorDivId").show();
            $("#passWordId").focus();
            return false;
        }else{
            $("#passwordErrorDivId").hide();
        }

        $.ajax({
            type: "post",
            url: "index.php?doLogin",
            data: {
                "userName":$("#userNameTextId").val(),
                "passWord":$("#passWordId").val(),
            },
            success:function(data){
                if(data == 1){
                    window.location.href = "index.php";
                }else{
                    $("#loginError").html("账号或密码错误,请重试!!!").show();
                }
            }
        });
    });


    /**
     * 绑定回车(登录)事件
     * @param hashCode
     */
    window.document.onkeydown = function doLogin(hashCode){
        if (hashCode.keyCode) {
            if(hashCode.keyCode == 13){
                document.getElementById("loginSubmitId").click();
            }
        }
    }

});


