

/*下滑框的图片显示或者隐藏*/
export function hoverInit(){
  var $productList =$(".productList li");
  var $productListImg =$(".productList-img li");
  productHover();
  function productHover(){ 
   var index=0;  
   $productList.mouseenter(function(){
    $productList.removeClass("active");
    $(this).addClass("active");
    $productListImg.removeClass('active');
    $productListImg.eq($productList.index(this)).addClass("active");
  })
   $productList.mouseleave(function(){
    $productList.removeClass("active");
    $productListImg.removeClass("active");

     $productList.eq(index).addClass("active");  
     $productListImg.eq(index).addClass("active");  
  
   


 })
   
   $productList.click(function(){
     index=$productList.index(this);    
   })

 };

 /*下滑框的显示或者隐藏*/
 productHoverDown();
 function productHoverDown(){
   var  $productHoverDown = $("#product-hoverDown")
   $(".navbar-nav>li").hover(function(){
    if ($(this).attr("id")!="product-hoverDown-show") {
      $productHoverDown.removeClass("active");

    }
  });
   $("#product-hoverDown-show").hover(function(){
    $productHoverDown.addClass("active");
  });
   $(".navbar-default").mouseleave(function(){
    $productHoverDown.removeClass("active");

  })
 }

}
export function iconInit(){

 var pageIcon =$(".page-icon");
 var pageIconImg =$(".page-icon>img");
 pageIconImg.load(function(){
   positionInit();
 })
 positionInit();
 function positionInit(){

   var iconLeft=(pageIcon.innerWidth()-pageIconImg.width())/2+pageIcon.offset().left;
   var winWidth=$(window).width();
   autoPosition();
   function autoPosition(){
     if(winWidth<=768){
      pageIconImg.get(0).style="";
    }else{
     setPosition();
   }
 }
 $(window).scroll(function () {
  autoPosition()
});
 function setPosition(){
   if ($(window).scrollTop() >= $(".jumbotron").height()) {

     pageIconImg.css({
      position:"fixed",
      top:"140px",
      left:iconLeft,
    })
   }else{
    var target =$(".jumbotron").height()-$(window).scrollTop()+140; 
    pageIconImg.css({
      top:target
    })
  }
}

$(window).resize(function(){
  winWidth=$(window).width();
  if(winWidth<=768){
    pageIconImg.get(0).style="";
  }else{
   setPosition();
   iconLeft =(pageIcon.innerWidth()-pageIconImg.width())/2+pageIcon.offset().left;
   pageIconImg.css({
    left:iconLeft,
  })
 }
})


}

}

export function maskInit(){
  var html= '<div class="img-mask"><span id="mask-close-btn"></span>';
  html+='<div class="swiper-container">';
  html+='<div class="swiper-wrapper">';

  var $img=$(".imgclick img");

  $img.each(function() {
    html+= '<div class="swiper-slide img-center">'
    html+='<img src="'+$(this).attr('data-src')+'">'
    html+='</div>'
  });
  html+='</div>'
  html+='<div class="my-swiper-button swiper-button-prev  swiper-button-white"></div>'
  html+='<div class="my-swiper-button swiper-button-next  swiper-button-white"></div>'
  html+='</div></div>';



  var mySwiper;
  function swiperInit(){
    mySwiper = new Swiper('.swiper-container', {
      loop:true,
      nextButton: '.swiper-button-next',
      prevButton: '.swiper-button-prev',

    });
  }
  $img.on("click",function(){
    $("body").append(html);
    swiperInit();
    $(".img-mask").addClass('mask-active');
    var index=$img.index(this);
    mySwiper.slideTo(index+1, 0, false);
    closeFun();
  });
  function closeFun(){
    $("#mask-close-btn").click(function(){
      $(".img-mask").removeClass('mask-active');
      setTimeout(function(){
        $(".img-mask").remove();
      },200)
    });
  }

}

