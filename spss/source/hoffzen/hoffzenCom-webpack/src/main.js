import Vue from 'vue'
import App from './App.vue'
import VueRouter from 'vue-router'
import routes from './router.config.js'
import "./assets/css/bootstrap.min.css"
import "./assets/css/swiper-3.4.2.min.css"
import "./assets/less/bootstrap-theme.less"

Vue.use(VueRouter);
const router =new VueRouter({
	// mode: 'history',
 scrollBehavior: () => ({ y: 0 }), // 滚动条滚动的行为，不加这个默认就会记忆原来滚动条的位置
 routes
})
new Vue({
	el: '#app',
	router,
	render: h => h(App),
	
})
