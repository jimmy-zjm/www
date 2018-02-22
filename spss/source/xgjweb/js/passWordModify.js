/**
 * Created by 唐文权 on 2016/1/8.
 */

$(function(){

    //原始密码框失去焦点
    $("#passWordOldId").blur(function(){
        checkOldPassAjax();
    });

    //密码框失去焦点出发验证
    $(".passWordClass").blur(function(){
        checkPassWordModify();
    });

});

/**
 * checkOldPassAjax 验证原始密码是否正确
 */
function checkOldPassAjax(){
    $.ajax({
        type:"post",
        url:"user.php?ajaxCheckPassWord",
        data:{
            "oldPassWord":$("#passWordOldId").val()
        },
        success:function(data){
            if(data == 1){
                $("#passWordOldConfirmErrorDivId").html("原始密码正确!").css("color","green").show();
            }else{
                $("#passWordOldId").focus();
                $("#passWordOldConfirmErrorDivId").html("原始密码错误!").css("color","red").show();
                return false;
            }
        }
    });
}


//修改密码表单验证
function checkPassWordModify(){

    var newPass = $("#passWordId");   //新密码框
    var newPassVal = $("#passWordId").val();   //新密码框的值
    var newPassError = $("#passWordErrorDivId");  //新密码错误提示框

    var confirmPass = $("#passWordConfirmId");   //确认密码框
    var confirmPassVal = $("#passWordConfirmId").val();   //确认密码框的值
    var confirmPassError = $("#passWordConfirmErrorDivId");  //确认密码错误提示框

    //原始密码
    checkOldPassAjax();


    //新密码
    //如果为空
    if(newPassVal == ""){
        //newPass.focus();
        newPassError.html("密码不得为空!").show();
        return false;
    }else{
        newPassError.html("").hide();
    }

    //如果长度小于6
    if(newPassVal.length < 6){
        //newPass.focus();
        newPassError.html("密码长度不得小于6!").show();
        return false;
    }else{
        newPassError.html("").hide();
    }

    //如果长度大于16
    if(newPassVal.length > 16){
        //newPass.focus();
        newPassError.html("密码长度不得大于16!").show();
        return false;
    }else{
        newPassError.html("").hide();
    }


    //确认密码
    //如果与密码不一致
    if(confirmPassVal != newPassVal){
        //confirmPass.focus();
        confirmPassError.html("密码不一致!").show();
        return false;
    }else{
        confirmPassError.html("").hide();
    }

    return true;

}