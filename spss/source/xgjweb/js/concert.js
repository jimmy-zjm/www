/**
 * Created by 唐文权 on 2016/1/7.
 */

/**
 * concertGoods     表示关注商品
 * @param string concertImgDivId   关注商品的控件(容纳图片链接的DIV)ID
 * @param int classId   商品的classID
 * @param int goodsId   商品的主键ID
 * @return  void
 */
function concertGoods(concertImgDivId,classId,goodsId){

    //判断cookie是否存在(用户是否登录)
    var userId = document.cookie.indexOf("userId=");
    var userName = document.cookie.indexOf("userName=");
    if(userId == -1 || userName == -1){
        window.location.href = "user.php?login";
        return false;
    }

    //更换对应的关注图片
    $("#"+concertImgDivId).html('<a href="javascript:;"><img src="images/20a.png"/></a>');

    //使用get全局方法提交数据
    $.get("concert.php?concertGoods&classId="+classId+"&goodsId="+goodsId);

}