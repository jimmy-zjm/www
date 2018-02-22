 <footer>
 	<div class="footer navbar navbar-fixed-bottom">
 		<div class="container-fluid">
 			<div class="row text-center small">
 				<div><span class="hervorhebung rot">Hoffzen GmbH</span> | Landshuter Allee 8-10 | 80637 München | Fon: 089/ 5455 8287 | Fax: 089/ 557443 |Konzept, Gestaltung, Umsetzung: www.plote.de |
 					<a id="bottomlink" href="impressum.html?navIndex=-1"> <span class="hervorhebung">Impressum</span>
 					</a>
 				</div>
 			</div>
 		</div>
 	</div>
 </footer>
 <script src="js/jquery-2.1.4.js"></script>
 <script src="js/bootstrap.min.js"></script>
 <script type="text/javascript">
  var $productList =$(".productList li");
  var $productListImg =$(".productList-img li");
  var $navList=$(".nav>li");

  // if (sessionStorage.navIndex){
  //   if (sessionStorage.navIndex!=-1) {
  //     $navList.eq(sessionStorage.navIndex).addClass('active');
  //   }
  // }else{
  //   $navList.eq(0).addClass('active');

  // }


  // $("#bottomlink").click(function(){
  //   sessionStorage.navIndex=-1;
  // });
  // $navList.click(function(){
  //   sessionStorage.navIndex=$navList.index(this);

  // });
  // $productList.click(function(){
  //   sessionStorage.navIndex=1;
  // });
  var navIndex=0;
   var url=document.location.href;
  if(url.indexOf("?")!=-1)   
  {
    url = url.substr(url.indexOf("?")+1);   
    var urls = url.split("&");
    var param;
    for(var i=0;i<urls.length;i++)   
    {
      param=urls[i].split("=");
      if(param[0]=="navIndex")
      {
        navIndex=param[1];
        
      }
      
    }
  }
  if(navIndex!=-1){
    $navList.eq(navIndex).addClass('active');
  }


  /*下滑框的图片显示或者隐藏*/
  productHover();
  function productHover(){   
   $productList.mouseenter(function(){
    $productList.removeClass("active");
    $(this).addClass("active");
    $productListImg.removeClass('active');
    $productListImg.eq($productList.index(this)).addClass("active");
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



 var pageIcon =$(".page-icon");
 var pageIconImg =$(".page-icon>img");
 pageIconImg.load(function(){
   positionInit();
 })
  positionInit();
 function positionInit(){
  try {
   var iconLeft=(pageIcon.innerWidth()-pageIconImg.width())/2+pageIcon.offset().left;
   var winWidth=$(window).width();
   $(window).scroll(function () {
    if(winWidth<=768){
      pageIconImg.get(0).style="";
    }else{
     setPosition();
   }

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
} catch (e) {
 console.log( "js not run!!!!");
} 

}

</script>