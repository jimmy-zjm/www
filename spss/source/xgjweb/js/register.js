/**
 * Created by 唐文权 on 2015/12/21.
 */

$(function(){

    //验证用户名
    $("#userNameTextId").blur(function(){
        checkUserName();
    });


    //验证密码
    $("#passWordId").blur(function(){
        checkPassWord();
    });

    //验证确认密码
    $("#passWordConfirmId").blur(function(){
        checkConfirmPassWord();
    });

    /**
     * 绑定回车(注册)事件
     * @param hashCode
     */
    window.document.onkeydown = function doRegister(hashCode){
        if(hashCode.keyCode == 13){
            document.getElementById("registerButId").click();
        }
    }

});


/**
 * doRegister   表示注册的方法
 * @returns {boolean}
 */
function doRegister(){

    //为了阻止用户一上来就直接点击注册
    checkUserName();    //调用用户名验证方法
    checkPassWord();    //调用密码验证方法
    checkConfirmPassWord(); //调用确认密码验证方法

    //如果有一个地方验证没有通过
    if($("#errorUserNameBackId").val() == "验证未通过" || $("#errorPassWordBackId").val() == "验证未通过" || $("#errorConfirmPassWordBackId").val() == "验证未通过"){
        //alert("验证未通过");
        return false;
    }

    //发送注册信息
    $.ajax({
        type:"post",
        url:"user.php?doRegister",
        data:{
            "userName":$("#userNameTextId").val(),
            "passWord":$("#passWordId").val(),
        },
        success:function(data){
            if(data == 1){
                alert("注册成功!");
//                window.location.href = "user.php?login";
                window.location.href = "index.php";
            }else{
                alert("注册失败,请重试!!!");
                window.location.href = "user.php?register";
            }
        }
    });
}


/**
 * checkUserName    表示验证用户名的方法
 * @returns {boolean}
 */
function checkUserName(){
    //如果用户名长度小于6或者大于15
    if(T_InBetweenLenEn('userNameTextId','userNameErrorDivId','用户名', 6, 15, false)) {
        $("#errorUserNameBackId").val("验证未通过");
        return false;
    }else{
        $("#userNameErrorDivId").hide();
        $("#errorUserNameBackId").val("");
    }

    //如果用户名中含有中文
    if(T_IsContainCh('userNameTextId','userNameErrorDivId','用户名')) {
        $("#errorUserNameBackId").val("验证未通过");
        return false;
    }else{
        $("#userNameErrorDivId").hide();
        $("#errorUserNameBackId").val("");
    }

    //如果用户名包含(<、>、'、"、*、!、#、-、&、 )非法字符
    if(T_IsSpecialChar('userNameTextId','userNameErrorDivId','用户名')) {
        $("#errorUserNameBackId").val("验证未通过");
        return false;
    }else{
        $("#userNameErrorDivId").hide();
        $("#errorUserNameBackId").val("");
    }

    //使用ajax提交填写的用户名到后台验证是否存在
    $.ajax({
        type:"post",
        url:"user.php?checkRegister",
        data:{
            "userName":$("#userNameTextId").val(),
        },
        success:function(data){
            if(data == "用户名可注册!"){
                $("#userNameErrorDivId").show().html(data);
                $("#errorUserNameBackId").val("");
            }else{
                $("#userNameErrorDivId").show().html(data);
                $("#errorUserNameBackId").val("验证未通过");
                return false;
            }
        }
    });
    //如果用户名存在不允许通过
    if($("#userNameErrorDivId").html() == "用户名被占用!!!"){
        $("#errorUserNameBackId").val("验证未通过");
        return false;
    }

    if($("#errorUserNameBackId").val() == "验证未通过"){
        //alert("验证未通过");
        return false;
    }

    //验证通过
    return true;
}


/**
 * checkPassWord    表示验证密码
 * @returns {boolean}
 */
function checkPassWord(){
    //如果密码长度不在6——16之间
    if(T_InBetweenLenEn('passWordId','passWordErrorDivId','密码', 6, 16, false)) {
        $("#errorPassWordBackId").val("验证未通过");
        return false;
    }else{
        $("#passWordErrorDivId").hide();
        $("#errorPassWordBackId").val("");
    }

    if($("#errorPassWordBackId").val() == "验证未通过"){
        //alert("验证未通过");
        return false;
    }

    return true;
}


/**
 * checkConfirmPassWord 表示验证确认密码
 * @returns {boolean}
 */
function checkConfirmPassWord(){
    //如果确认密码与密码不一致
    if(T_IsEqual('passWordId','passWordConfirmId')) {
        $("#errorConfirmPassWordBackId").val("验证未通过");
        return false;
    }else{
        $("#passWordConfirmErrorDivId").hide();
        $("#errorConfirmPassWordBackId").val("");
    }

    if($("#errorConfirmPassWordBackId").val() == "验证未通过"){
        //alert("验证未通过");
        return false;
    }

    return true;
}