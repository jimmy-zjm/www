<template>

  <div>


    <div class="jumbotron" id="header">
      <div class="container">
        <div class="row header">
          <!--  -->
          <div class="col-xs-12 col-sm-12 col-md-12 bigImgbox" id="header_index1">
            <img src="/static/img/newair/newair_banner.png">
            <div class="banner-txt-Box hidden-xs">
            <p v-html="lang.bannerTxt"></p>

            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container" role="main">




      <div class="row">
        <aside class="col-xs-3  img-center page-icon">

          <img  src="/static/img/icon_newair.png">

        </aside>
        <article class="col-xs-12 col-sm-9 ">
          <!-- row1 -->
          <div class="row marginbtm80">

            <article class="col-xs-12 col-sm-6 ">
              <h4>{{lang.content.title}}</h4>
              <ul class="col-xs-12 redlinebox">
                <li v-for="item in lang.content.text"> {{item}}
                </li>
                <span class="redline"></span>
              </ul>
            </article>
            <p class="col-xs-12 col-sm-6 hidden-xs"></p>
            <aside class="col-xs-12 col-sm-6 vertical-center-r  imgclick img-center">
              <img  data-src="/static/img/newair/newair_show01_big.png"   src="/static/img/newair/newair_show01.png">
            </aside>
          </div>
          <!-- row2 -->
          <div class="bigImgbox marginbtm80">
            <img :src="lang.img01">
          </div>
          <!-- row3 -->
          <div class="bigImgbox marginbtm80">
            <p class="marginbtm20">{{lang.des}}</p>
            <img :src="lang.img02">
          </div>
          <!-- row4 -->
          <div class="bigImgbox marginbtm80">
            <h4 class="lowerCase marginbtm50">{{lang.title01}}</h4>
            <img :src="lang.img03">
          </div>
          <!-- row5 -->
          <h4 class=" lowerCase marginbtm50">{{lang.title02}}</h4>
          <div class="row marginbtm80">

            <aside class="col-xs-12 col-sm-6  imgclick img-center">
              <img  :data-src="lang.imgShow.max" :src="lang.imgShow.min">
            </aside>
            <p class="col-xs-12 col-sm-6 hidden-xs"></p>
            <aside class="col-xs-12 col-sm-6 vertical-center-r bigImgbox">
              <img    :src="lang.img04">
            </aside>
          </div>
          <!--row6  -->
          <div class="bigImgbox">
            <img :src="lang.img05">
          </div>
          <p class="margintop50"><router-link to="/contact?navIndex=4" class="btn btn-md btn2" role="button">{{lang.btmTxt}}  &raquo;</router-link>
          </p>

        </article>




      </div>

    </div>

  </div>

</template>

<script type="text/javascript">
  import {maskInit,iconInit}from "../assets/js/index.js"
  import language from "./language/freshair.js"
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
mounted(){
  this.langUpdate(this.newlang);
  this.$nextTick(function(){
    maskInit();
    iconInit();
  })
}
}

</script>