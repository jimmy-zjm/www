/**
 * Created by 唐文权 on 2016/1/18.
 */

/**
 * checkReturnGoods 表示验证退货表单
 * @returns {boolean} 填写是否正确
 */
function checkReturnGoods(){

    //如果没有选择服务类型
    var returnTypeSelect = $("#selectmenu1").val();
    if(returnTypeSelect == "请选择服务类型"){
        $("#errorSelDivId").show();
        return false;
    }
}