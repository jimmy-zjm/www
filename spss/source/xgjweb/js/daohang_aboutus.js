 $(function(){

var zhiding=$(".about-center-left-nav"); //得到导航对象

var win=$(window); //得到窗口对象

var sc=$(document);//得到document文档对象。

win.scroll(function(){

  if(sc.scrollTop()>=150){

    zhiding.addClass("about-center-left-nav_fixed"); 

  }else{

   zhiding.removeClass("about-center-left-nav_fixed");

  }

})  


 })