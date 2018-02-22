 $(function(){

var zhiding=$(".eugroup-center-left-nav"); //得到导航对象

var win=$(window); //得到窗口对象

var sc=$(document);//得到document文档对象。

win.scroll(function(){

  if(sc.scrollTop()>=150){

    zhiding.addClass("eugroup-center-left-nav_fixed"); 

  }else{

   zhiding.removeClass("eugroup-center-left-nav_fixed");

  }

})  


 })