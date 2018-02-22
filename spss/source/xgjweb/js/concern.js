/*列表页面的点击关注商品*/
$('img[src="images/23.png"]').click(function(){
    var id1   = $(this).parent().parent().parent().parent().parent().attr('data-id');
    var id2   = $(this).parent().parent().parent().parent().attr('data-id');
    var id3   = $(this).parent().parent().attr('data-id');
    var id = id1 || id2 || id3;


    var class_id   = $(this).parent().parent().attr('class-id');

    if(!id){
        // console.log('id没有找到',id);
        return;
    }
    var that = $(this);
    $.get('ajax.php?concern',{id:id,class_id:class_id},function(data){
        if(data.code==1){
            that.attr('src','images/23a.png');
        }
		else if(data.code==2){
            that.attr('src','images/23.png');
        }else{
            alert(data.message);
        }
    },'json');
});
