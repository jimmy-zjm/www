
<template>
  


  <div class="navbar navbar-default navbar-fixed-top" role="navigation">

    <div class="container">
      <a class="navbar-brand col-sm-2 col-md-4" href="index.html">
        <img src="/static/img/hoffzen_logo.png" alt="Hoffzen Hausgeräte Logo">
      </a>

      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Navigation ein-/ausblenden</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
          <li v-bind:class="[navIndex==0?'active':'']" >
           <router-link to="/home?navIndex=0">{{lang.home}}</router-link>
         </li>
         <li v-bind:class="[navIndex==1?'active':'']" class="hidden-xs" id="product-hoverDown-show">
          <a href="#" >{{lang.product}}<span class="caret"></span></a>
        </li>
        <li v-bind:class="[navIndex==1?'active':'']" class="visible-xs-block dropdown" >
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{lang.product}}<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">

            
            <li>
             <router-link to="/wasserkocher?navIndex=1">{{lang.product01}}</router-link>
             
           </li>
           <li>
             <router-link to="/pipeline?navIndex=1">{{lang.product02}}</router-link>

             
           </li>
           <li  >
             <router-link to="/radiator?navIndex=1">{{lang.product03}}</router-link>
           </li>
           <li >
             <router-link to="/freshair?navIndex=1">{{lang.product04}}</router-link>
           </li>
         </ul>
       </li>
       <li v-bind:class="[navIndex==2?'active':'']">
         <router-link to="/service?navIndex=2">{{lang.service}}</router-link>
       </li>
       <li v-bind:class="[navIndex==3?'active':'']">
         <router-link to="/ueberuns?navIndex=3">{{lang.aboutus}}</router-link>
       </li>

       <li v-bind:class="[navIndex==4?'active':'']">
         <router-link to="/kontakt?navIndex=4">{{lang.contact}}</router-link>
       </li>
       <li id="country-box">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{lang.lang}}<img class="guoqi" v-bind:src="lang.langImg"></a>
        <ul class="dropdown-menu" role="menu">
          <p></p>
          <li v-on:click="changeLanguage('de')"><a href="#">DE<img class="guoqi" src="/static/img/home/de.png"></a>
          </li>
          <li v-on:click="changeLanguage('cn')"><a href="#">CN<img class="guoqi" src="/static/img/home/cn.png"></a>
          </li>
          <li  ><a href="#">UK<img class="guoqi" src="/static/img/home/uk.png"></a>
          </li>
          <li ><a href="#">US<img class="guoqi" src="/static/img/home/us.png"></a>
          </li>
          <li ><a href="#">JP<img class="guoqi" src="/static/img/home/jp.png"></a>
          </li>
          <li ><a href="#">KR<img class="guoqi" src="/static/img/home/kr.png"></a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
  <!--/.nav-collapse -->
</div>
<div class="hidden-xs" id="product-hoverDown">
 <div class="container">
   <ul class="productList">
     <li class="active">

       <router-link to="/wasserkocher?navIndex=1">{{lang.product01}}</router-link>
     </li>
     <li>
      <router-link to="/pipeline?navIndex=1">{{lang.product02}}</router-link>
    </li>
    <li  >
      <router-link to="/radiator?navIndex=1">{{lang.product03}}</router-link>
    </li>
    <li >
      <router-link to="/freshair?navIndex=1">{{lang.product04}}</router-link>
    </li>
  </ul>
  <ul class="productList-img" >
   <li  class="active"><img src="/static/img/headhover/ranqireshui.png" alt=""></li>
   <li><img src="/static/img/headhover/guanzi.png" alt=""></li>
   <li><img src="/static/img/headhover/nuanqi.png" alt=""></li>
   <li><img src="/static/img/headhover/air.png" alt=""></li>
 </ul>
</div>
</div>

</div> 
</template>
<script type="text/javascript">
import {hoverInit }from "../assets/js/index.js"
import  language from "./language/nav.js"
  export default {
    data(){
      return {
        navIndex:0,
        lang:language.cn,
      }
    },
    watch:{
      $route(to){    
        this.navIndex=to.query.navIndex;
      }
    },
    mounted(){
      hoverInit();
       let index=this.$route.query.navIndex;
       if(index){
        this.navIndex=index;
       }else{
        this.navIndex=0;
       }
      
    },
   
    methods:{
      changeLanguage(lang){
        this.$emit('navChangeLang',lang);
        if(lang=='de'){
          this.lang=language.de;
        }
        if(lang=='cn'){
          this.lang=language.cn;
        }
      },
    }
  }

  

</script>  
