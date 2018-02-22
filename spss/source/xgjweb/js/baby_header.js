$(function(){

    /*获取头部的所有数据*/
    $.ajax({
        url:'ajax.php?getHeader',
        dataType:'json',
        async:true,
        success:function(d){
            if(d.code){
                //购物车总数量
                $('#cart_total').html(d.data.cart_total);
            }
        }
    });
});