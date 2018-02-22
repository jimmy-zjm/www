<template>
  <div>
    <div class="jumbotron" id="header">
      <div class="container">
        <div class="row header">
          <!--  -->
          <div class="col-xs-12 col-sm-12 col-md-12 bigImgbox" id="header_index1">
            <img src="/static/img/hotwater/reh_banner.png">
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

            <img  src="/static/img/icon_luzi.png">

          </aside>
          <article class="col-xs-12 col-sm-9">
            <h4 class="bigTitle">{{lang.title01}}</h4>
            <p class="marginbtm100  redlinebox">{{lang.content01}}<span class="redline"></span></p>


            <div class="bigImgbox marginbtm100">
              <img :src="lang.img01">
            </div>
            <div class="bigImgbox marginbtm100">
              <img :src="lang.img02">
            </div>

            <div  class="row marginbtm100">
              <article class="col-xs-12 col-sm-6 col-md-6 ">
               <h4 class="bigTitle">{{lang.title02}}</h4>
               <p class="redlinebox" v-html="lang.content02"></p>
             </article>
             <p class="col-xs-12 col-sm-6 hidden-xs"></p>

             <div class="col-xs-12 col-sm-6 col-md-6 vertical-center-r  imgclick img-center" >

              <img data-src="/static/img/hotwater/reh_show_big.png" src="/static/img/hotwater/reh_show.png">
            </div>
          </div>

          <div class="row ">
            <div class="col-xs-12">
              <h4 class="bigTitle">{{lang.title03}}</h4>
              <div class="redlinebox clearfix iconShow">
               <span class="redline"></span>
               <div class="col-xs-12 col-md-6">
                 <p v-for="(value,index) in lang.contentList" v-if="index<6" :class='myClass(index)'>{{value}}
                 </p>
               </div>
               <div class="col-xs-12 col-md-6">
                <p v-for="(value,index) in lang.contentList" v-if="index>=6" :class='myClass(index,lang.contentList.length)'>{{value}}
                </p>
              </div>
            </div>
          </div>
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
 import language from "./language/wasserkocher.js"
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
  },
   myClass(index,len){
    if(len&&index==len-1){
        return "reh_icon"+(1+index);
    }else{
       return "reh_icon  reh_icon"+(1+index);
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
},


}

</script>