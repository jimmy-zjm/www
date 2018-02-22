<template>
<div>
<div class="jumbotron" id="header">
  <div class="container">
    <div class="row header">
      <div class="col-xs-12 col-sm-12 col-md-12 text-right" id="header_index1">
       <div class="swiper-container">
        <div class="swiper-wrapper">
          <div v-for="item in lang.bannerImg" class="swiper-slide">
            <img :src="item">
            <div class="hidden-xs myswiper-textBox">
              <p   class="myswiper-text1">{{lang.bannerTxt.line01}}</p>
              <h4  class="myswiper-text2">{{lang.bannerTxt.line02}}</h4>
              <h4  class="myswiper-text3">{{lang.bannerTxt.line03}}</h4>
              <h4  class="myswiper-text4">{{lang.bannerTxt.line04}}</h4>
            </div>
          </div>
    </div>
    <div class="swiper-pagination"></div>
  </div>
</div>



</div>
</div>
</div>

<div class="container" role="main">



 
    <div class="row ">
     <article v-for="item in lang.itemList" class="col-sm-6 col-md-3" >
      <div class="row marginbottom1em home-des-div">
        <img :src="item.img" class="pull-left col-xs-3 col-sm-4" alt="Hoffzen Haustechnologie Firmensitz">
        <h4 class="col-sm-offset-4 col-xs-offset-3" v-html="item.title"></h4> 
      </div>
      <p class="">{{item.content}} </p>
      <p>  <router-link :to="item.btmLink" class="btn btn2  btn-md" role="button">{{lang.btmTxt}}   &raquo;</router-link>
     
      </p>

    </article>
   
    
  

  </div>
  

</div>

</div>

</template>
<script type="text/javascript">
import language from "./language/home.js"
  export default{
       data(){
        return{
          lang:language.cn
        }
      },
      props:{newlang:String},
      methods:{
        langUpdate(value){
         if(value=="cn"){
          this.lang=language.cn
        }
        if(value=="de"){
          this.lang=language.de
        }
      }
    },
    watch:{
     newlang(value){
      this.langUpdate(value);
    
     }
   },
   updated(){
      mySwiper.destroy(true, true);
      homeInit();
     
   },
   mounted(){
   
        this.langUpdate(this.newlang);
        this.$nextTick(function(){
           homeInit();
        })
       
   }
   
 }
 var mySwiper;
function homeInit(){
   mySwiper= new Swiper('.swiper-container', {
        autoplay: 3000,//可选选项，自动滑动
        pagination : '.swiper-pagination',
        paginationClickable :true,
       loop:true,
        effect: 'fade',
        onSlideChangeStart: function(swiper){ 
      swiper.slides.eq(swiper.activeIndex).find("div").addClass('active') //
    },
    onSlideChangeEnd: function(swiper){
     swiper.slides.each(function(index, el) {
       if (index!=swiper.activeIndex) {
        $(this).find("div").removeClass('active');
      }
    });
   },
 })
}

</script>