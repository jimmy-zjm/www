 $(function(){

var zhiding=$(".zhiding"); //得到导航对象

var win=$(window); //得到窗口对象

var sc=$(document);//得到document文档对象。

win.scroll(function(){

  if(sc.scrollTop()>=150){

    zhiding.addClass("fixedzhiding"); 

   $(".zhidingTmp").fadeIn(); 
   $(".fixedzhiding").css({ background: "#efefef"});
   $(".fixedzhiding a").css({ color: "#fff"});

  }else{

   zhiding.removeClass("fixedzhiding");

   $(".zhidingTmp").fadeOut();
   $(".zhiding").css({ background: "#efefef"});
   $(".zhiding a").css({ color: "#fff"});
  }

})  


 })