<template>


  <div>
    <div class="jumbotron" id="header">
      <div class="container">
        <div class="row header">
          <!--  -->
          <div class="col-xs-12 col-sm-12 col-md-12 bigImgbox" id="header_index1">
            <img src="/static/img/heating/heating_banner.png">
            <div class="banner-txt-Box hidden-xs">
             <p v-html="lang.bannerTxt"></p>
              </div>
            </div>

            <!--  -->


          </div>


        </div>
      </div>
      <div class="container" role="main">

        <div class="row">
         <aside class="col-xs-3 img-center page-icon">

          <img  src="/static/img/icon_heating.png">

        </aside>
        <article class="col-xs-12 col-sm-9">
          <!-- row1 -->
          <h4>{{lang.content01.title}}</h4>
          <p class="marginbtm80 redlinebox" v-html="lang.content01.text">

          </p>
          <!--row 2 -->
          <div class="bigImgbox changeBig marginbtm80">
            <img src="/static/img/heating/heating_img01.png">
          </div>
          <!-- row3 -->
          <h4>{{lang.content02.title}}</h4>
          <p class="marginbtm80 redlinebox" v-html="lang.content02.text">

          </p>

          <!--  row4-->
          <div class="row marginbtm100">
            <aside class="col-xs-12 col-sm-6  my-boxShadow imgclick img-center">
              <div>
                <img  data-src="/static/img/heating/heating_show01_big.png"   src="/static/img/heating/heating_show01.png">
              </div>
            </aside>
            <p class="col-xs-12 col-sm-6 hidden-xs"></p>

            <article class="col-xs-12 col-sm-6 vertical-center-r limg">
              <h4>{{lang.content03.title}}</h4>
              <p class="redlinebox" v-html="lang.content03.text">

              </p>
            </article>
          </div>
          <!-- row5 -->
          <div class="row marginbtm100">
           <p class="col-xs-12 col-sm-6 hidden-xs"></p>
           <article class="col-xs-12 vertical-center-l col-sm-6 rimg">
            <h4>{{lang.content04.title}}</h4>
            <p class="redlinebox" v-html="lang.content04.text">

            </p>
          </article>
          <aside class="col-xs-12 col-sm-6  bigImgbox">
            <img :src='lang.img01'>
          </aside>
        </div>
        <!-- row6 -->
        <div class="bigImgbox changeBig marginbtm80">
          <img src="/static/img/heating/heating_img02.png">
        </div>
        <!--row7  -->
        <h4>{{lang.content05.title}}</h4>
        <p class="marginbtm80 redlinebox" v-html="lang.content05.text"></p>
        <!-- row8 -->
        <div class="row marginbtm80">
          <aside class="col-xs-12 col-sm-6 my-boxShadow imgclick img-center">
            <div>
              <img  data-src="/static/img/heating/heating_show02_big.png"   src="/static/img/heating/heating_show02.png">
            </div>
          </aside>
          <p class="col-xs-12 col-sm-6 hidden-xs"></p>

          <article class="col-xs-12 col-sm-6  vertical-center-r limg">
            <h4>{{lang.content06.title}}</h4>
            <p class="redlinebox" v-html="lang.content06.text">

            </p>
          </article>
        </div>
        <!-- row9 -->
        <div class="row marginbtm80">

          <article class="col-xs-12 col-sm-6 ">
            <h4>{{lang.content07.title}}</h4>
            <p class="redlinebox" v-html="lang.content07.text">

            </p>
          </article>
          <p class="col-xs-12 col-sm-6 hidden-xs"></p>

          <aside class="col-xs-12 col-sm-6  vertical-center-r  bigImgbox">
            <img :src='lang.img02'>
          </aside>
        </div>
        <!-- row10 -->
        <div class="bigImgbox changeBig marginbtm80">
          <img src="/static/img/heating/heating_img03.png">
        </div>

        <!-- row11 -->
        <h4>{{lang.content08.title}}</h4>
        <p class="marginbtm80 redlinebox" v-html="lang.content08.text"></p>
        <!--row12  -->
        <div class="row marginbtm100">
         <p class="col-xs-12 col-sm-6 hidden-xs"></p>

         <aside class="col-xs-12 col-sm-6 my-boxShadow vertical-center-l imgclick img-center">
          <div>
            <img  data-src="/static/img/heating/heating_show03_big.png"   src="/static/img/heating/heating_show03.png">
          </div>
        </aside>
        <article class="col-xs-12 col-sm-6 limg">
          <h4>{{lang.content09.title}}</h4>
          <p class="redlinebox" v-html="lang.content09.text">

          </p>
        </article>
      </div>
      <!-- row13 -->
      <div class="row marginbtm100">
       <p class="col-xs-12 col-sm-6 hidden-xs"></p>
       <article class="col-xs-12 col-sm-6 vertical-center-l rimg">
        <h4>{{lang.content10.title}}</h4>
        <p class="redlinebox" v-html="lang.content10.text">
        </p>
      </article>
      <aside class="col-xs-12 col-sm-6  bigImgbox">
        <img :src='lang.img03'>
      </aside>
    </div>
    <!-- row14 -->
    <div class="bigImgbox changeBig marginbtm80">
      <img src="/static/img/heating/heating_img04.png">
    </div>
    <!--row15  -->
    <h4>{{lang.content11.title}}</h4>
    <ul class="marginbtm80 redlinebox col-xs-12">
      <li v-for="item in lang.content11.text">{{item}}</li>
      <span class="redline"></span></ul>
      <!-- row16 -->
      <div class="bigImgbox changeBig marginbtm80">
        <img src="/static/img/heating/heating_img05.png">
      </div>
      <!-- row17 -->
      <h4>{{lang.content12.title}}</h4>
      <p class=" redlinebox" v-html="lang.content12.text"></p>
      <!-- end -->


      <p class="margintop50">
     <router-link to="/contact?navIndex=4" class="btn btn-md btn2" role="button">{{lang.btmTxt}}  &raquo;</router-link>
      </p>

    </article>




  </div>












</div>
</div>

</template>
<script type="text/javascript">
import {maskInit,iconInit}from "../assets/js/index.js"
import language from "./language/radiator.js"
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