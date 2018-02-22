<template>

<div>
   <div class="jumbotron" id="header">
    <div class="container">
        <div class="row header">
            <!--  -->
            <div class="col-xs-12 bigImgbox" id="header_index1">
                <img src="/static/img/service_banner.png">
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
            <aside class="col-xs-3  img-center page-icon">

            <img  src="/static/img/icon_service.png">

        </aside>
            <article  class="col-xs-12 col-sm-9" >
                <h4 class="bigTitle">{{lang.title}}</h4>
            <p class="  redlinebox">{{lang.content.line01}}<br>
<br>
{{lang.content.line02}}<br><br>{{lang.content.line03}}
<span class="redline"></span></p>
                <p class="margintop50"><router-link to="/contact?navIndex=4" class="btn btn-md btn2" role="button">{{lang.btmTxt}}  &raquo;</router-link>
                </p>

            </article>




        </div>

<div class="kongDiv"></div>




       


    </div>
</div>

</template>
<script type="text/javascript">
import language from "./language/service.js"
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
  
   }
   
 }
</script>