$(function(){

      $("body").append(htmlMask);
      // var ObjMask=$(".img-mask");
      var img=$(".imgbox-list img");
      img.height(img.width());
      $(window).resize(function(){
          img.height(img.width());			
    });
      $(".imgbox-list").each(function(){
      	$(this).find("li").each(function(){
      		var index=$(this).index();
      		if(index%5==0){
      			$(this).css("margin-left",0);
      		}
      		if(index%5==4){
      			$(this).css("margin-right",0);
      		}
      	});
      });
      var htmlMask='<div class="img-mask"><img src=""></div>';
      $("body").append(htmlMask);
      ImgMask([".imgbox-list",".new-imgbox"])
      function ImgMask(options){
           var ObjMask=$(".img-mask");
           var MaskImg=ObjMask.find("img");
           $.each(options,function(index,item){
            console.log(item);
            $(item).on("click","img",function(){
                MaskImg.attr("src",$(this).attr("src"));
                ObjMask.fadeIn("slow"); 

          });
      });
           ObjMask.click(function(){
            $(this).fadeOut("fast");
      });
     }



})