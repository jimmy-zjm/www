 $(function(){

var zhiding=$(".video_list-conter-left-nev"); //得到导航对象

var win=$(window); //得到窗口对象

var sc=$(document);//得到document文档对象。

win.scroll(function(){

  if(sc.scrollTop()>=150){

    zhiding.addClass("video_list-conter-left-nev_fixed"); 

  }else{

   zhiding.removeClass("video_list-conter-left-nev_fixed");

  }

})  


 })