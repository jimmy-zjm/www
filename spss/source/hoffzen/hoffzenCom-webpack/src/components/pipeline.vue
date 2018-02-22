<template>
<div>
<div class="jumbotron" id="header">
    <div class="container">
        <div class="row header">
            <!--  -->
            <div class="col-xs-12 col-sm-12 col-md-12 bigImgbox" id="header_index1">
                <img src="/static/img/pipe/pipe_banner.png">
                <div class="banner-txt-Box hidden-xs">
                 <p v-html="lang.bannerTxt"></p>
                </div>
            </div>

            <!--  -->


        </div>


    </div>
</div>
<!--  -->
<div class="container" role="main">

    <div class="row">
     <!-- <p class="col-xs-3 hidden-xs"></p> -->
        <aside class="col-xs-3  img-center page-icon">

            <img  src="/static/img/icon_guandao.png">

        </aside>
        <article class="col-xs-12 col-sm-9">


         <p class="arrowTitle">{{lang.bigTitle01}}</p>
         <!-- row1 -->
         <div class="row marginbtm80">
          <article class="col-xs-12 col-sm-6">
            <h4>{{lang.content01.title}}</h4>
                <div class="redlinebox">
                    <ul class="wenzilist">
                        <li v-for="item in lang.content01.txtList">{{item}}</li>
                       
                    </ul>
                    <span class="redline"></span>
                </div>
            </article>
            <p class="col-xs-12 col-sm-6 hidden-xs"></p>

            <aside class="col-xs-12 col-sm-6 vertical-center-r  imgclick img-center">
                <img class="pipe-imgtxt-img " data-src="/static/img/pipe/pipe_show01_big.png"   src="/static/img/pipe/pipe_show01.png">
            </aside>

        </div>
        <!-- row2 -->

        <div class="bigImgbox marginbtm80">
            <img :src="lang.bigImg01" alt="">
        </div> 

        <!-- row3 -->

        <div class="row marginbtm80">
            <article class="col-xs-12 col-sm-6 ">
                <h4>{{lang.content02.title}}</h4>
                    <div class="redlinebox">
                        <ul class="wenzilist">
                             <li v-for="item in lang.content02.txtList">{{item}}</li>
                        </ul>
                        <span class="redline"></span>
                    </div>
                </article>
            <p class="col-xs-12 col-sm-6 hidden-xs"></p>

                <aside class="col-xs-12 col-sm-6 vertical-center-r imgclick img-center">
                    <img class="pipe-imgtxt-img " data-src="/static/img/pipe/pipe_show02_big.png"   src="/static/img/pipe/pipe_show02.png">
                </aside>

            </div>
            <!-- row4 -->

            <div class="bigImgbox marginbtm80">
                <img :src="lang.bigImg02" alt="">
            </div> 
            <p class="arrowTitle">{{lang.bigTitle02}}</p>
            <!-- row5 -->

            <div class="row marginbtm80">
            
            <article class="col-xs-12 col-sm-6 ">
                <h4>{{lang.content03.title}}</h4>
                <div class="redlinebox">
                    <ul class="wenzilist">
                        <li v-for="item in lang.content03.txtList">{{item}}</li>
                    </ul>
                    <span class="redline"></span>
                </div>
            </article>
            <p class="col-xs-12 col-sm-6 hidden-xs"></p>

              <aside class="col-xs-12 col-sm-6 vertical-center-r imgclick img-center">
                <img style="width:280px" data-src="/static/img/pipe/pipe_show03_big.png"   src="/static/img/pipe/pipe_show03.png">
            </aside>
        </div>
        <!-- row6 -->

      <div class="bigImgbox marginbtm80">
                <img :src="lang.bigImg03" alt="">
            </div> 
     <!-- row7 -->

     <div class="row marginbtm80">
       <p class="col-xs-12 col-sm-6 hidden-xs"></p>
        <article class="col-xs-12  col-sm-6 vertical-center-l">
            <h4>{{lang.content04.title}}</h4>
            <div class="redlinebox">
                <ul class="wenzilist">
                    <li v-for="item in lang.content04.txtList">{{item}}</li>
                </ul>




                <span class="redline"></span>
            </div>
        </article>

        <aside class="col-xs-12 col-sm-6  imgclick img-center">
            <img data-src="/static/img/pipe/pipe_show04_big.png"   src="/static/img/pipe/pipe_show04.png">
        </aside>

    </div>

    <p class="arrowTitle">{{lang.bigTitle03}}</p>
    <!-- row8 -->
    <div class="row marginbtm80">
     
    <article class="col-xs-12 col-sm-6">
        <h4>{{lang.content05.title}}</h4>
            <div class="redlinebox">
                <ul class="wenzilist">
                    <li v-for="item in lang.content05.txtList">{{item}}</li>
                </ul>
                <span class="redline"></span>
            </div>
        </article>
            <p class="col-xs-12 col-sm-6 hidden-xs"></p>

         <aside class="col-xs-12 col-sm-6 vertical-center-r  imgclick img-center">
        <img class="pipe-imgtxt-img " data-src="/static/img/pipe/pipe_show05_big.png"   src="/static/img/pipe/pipe_show05.png">
    </aside>
    </div> 
    <!-- row9 --> 
    <div class="bigImgbox marginbtm80">
        <img :src="lang.bigImg04" alt="">
    </div> 
    <!-- row10 -->  
    <div class="row marginbtm80">
        <article class="col-xs-12 col-sm-6 ">
            <h4>{{lang.content06.title}}</h4>
                <div class="redlinebox">
                    <ul class="wenzilist">
                        <li v-for="item in lang.content06.txtList">{{item}}</li>
                        </ul>
                        <span class="redline"></span>
                    </div>
                </article>
            <p class="col-xs-12 col-sm-6 hidden-xs"></p>

                <aside class="col-xs-12 col-sm-6 vertical-center-r imgclick img-center">
                    <img class="pipe-imgtxt-img " data-src="/static/img/pipe/pipe_show06_big.png"   src="/static/img/pipe/pipe_show06.png">
                </aside>

            </div>
            <!-- row11 --> 
            <div class="bigImgbox">
                <img :src="lang.bigImg05" alt="">
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
import language from "./language/pipeline.js"
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
