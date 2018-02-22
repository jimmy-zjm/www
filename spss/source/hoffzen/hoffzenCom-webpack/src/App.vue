<template>
  <div id="app">
    <NavView @navChangeLang="changelang"></NavView>
    <router-view :newlang="lang"></router-view>
    <FooterView></FooterView>
  </div>
</template>

<script>
  import NavView from './components/head.vue'
  import FooterView from './components/foot.vue'

  export default {
    name: 'app',
    data(){
      return {
        lang:"cn"
      }
    },
    components:{
      NavView,
      FooterView
    },
    methods:{
      changelang(a){
       this.lang=a;
     }
   }


 }
</script>
