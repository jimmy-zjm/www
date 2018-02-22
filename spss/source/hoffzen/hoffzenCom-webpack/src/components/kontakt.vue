<template>
<div>
<div class="jumbotron" id="header">
    <div class="container">
        <div class="row header">
            <!--  -->
            <div class="col-xs-12 col-sm-12 col-md-12 bigImgbox" id="header_index1">
                <img src="/static/img/contact_banner.png">
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

        <img  src="/static/img/icon_contact.png">

    </aside>
    <article  class="col-xs-12 col-sm-9">
        <h4 class="bigTitle">{{lang.title}}</h4>
        <p class="  redlinebox">{{lang.content.line01}}<br>{{lang.content.line02}}<br>{{lang.content.line03}}<br><br>

            Hoffzen GmbH <br>
            Landshuter Allee 8-10 <br>

            80637 München <br>
            Fon: 089/ 5455 8287 <br> 
            Fax: 089/ 557443
            <span class="redline"></span></p>
            <p class="margintop50"><a href="mailto:info@hoffzen.de" class="btn btn2 btn-md" role="button">{{lang.btmTxt}}&raquo;</a>
            </p>

        </article>




    </div>
<div class="kongDiv"></div>




</div>
</div>


</template>
<script type="text/javascript">
import language from "./language/contact.js"
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
