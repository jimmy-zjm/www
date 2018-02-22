/**
 * Created by 唐文权 on 2016/1/4.
 */

$(function(){

    //收货人失去焦点
    $("#UserNameTextId").blur(function(){
        if(T_InBetweenLenCh("UserNameTextId", "ErrorUserNameDivId", "收货人", 2, 20, false)){
            return false;
        }
    });

    //手机号失去焦点
    $("#mobileTextId").blur(function(){
        if(T_IsMobile("mobileTextId", "ErrorMobileDivId", false)){
            return false;
        }
    });

    //固定电话失去焦点
    $("#phoneTextId").blur(function(){
        if(T_IsTel("phoneTextId", "ErrorPhoneDivId", true)){
            return false;
        }
    });

    //邮箱失去焦点
    $("#emailTextId").blur(function(){
        if(T_IsEmail("emailTextId", "ErrorEmailDivId", true)){
            return false;
        }
    });

    //详细地址失去焦点
    $("#addrTextId").blur(function(){
        if(T_IsNull("addrTextId", "ErrorAddrDivId", "详细地址")){
            return false;
        }
    });

});


/**
 * checkAddrAddOrEdit   表示验证输入
 * @returns {boolean}   true/false
 */
function checkAddrAddOrEdit(){

    //收货人
    if(T_InBetweenLenCh("UserNameTextId", "ErrorUserNameDivId", "收货人", 2, 20, false)){
        return false;
    }

    //手机号
    if(T_IsMobile("mobileTextId", "ErrorMobileDivId", false)){
        return false;
    }

    //固定电话
    if(T_IsTel("phoneTextId", "ErrorPhoneDivId", true)){
        return false;
    }

    //邮箱
    if(T_IsEmail("emailTextId", "ErrorEmailDivId", true)){
        return false;
    }

    //详细地址
    if(T_IsNull("addrTextId", "ErrorAddrDivId", "详细地址")){
        return false;
    }

    return true;
}

//收货人
function checkName(){
    if(T_InBetweenLenCh("UserNameTextId", "ErrorUserNameDivId", "收货人", 2, 20, false)){
        return false;
    }
}


/**
 * confirmDel   表示删除确认框
 * @return boolean  true/false
 */
function confirmDel(){
    if(confirm("确定删除此收货地址?")){
        return true;
    }
    else{
        return false;
    }
}