
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
$("body").append(html);


var mySwiper = new Swiper('.swiper-container', {
	loop:true,
	nextButton: '.swiper-button-next',
	prevButton: '.swiper-button-prev',

}
);
$img.on("click",function(){
	$(".img-mask").addClass('mask-active');
	var index=$img.index(this);
	mySwiper.slideTo(index+1, 0, false);
});
$("#mask-close-btn").click(function(){
	$(".img-mask").removeClass('mask-active');

})



