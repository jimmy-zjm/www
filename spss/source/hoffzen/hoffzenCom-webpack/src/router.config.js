
import Freshair from './components/freshair.vue'
import Impressum from './components/impressum.vue'
import Kontakt from './components/kontakt.vue'
import Home from './components/index.vue'
import Pipeline from './components/pipeline.vue'
import Radiator from './components/radiator.vue'
import Service from './components/service.vue'
import Ueberuns from './components/ueberuns.vue'
import Wasserkocher from './components/wasserkocher.vue'

export default[
		{path:'/home', component:Home},
		{path:'/wasserkocher', component:Wasserkocher},
		{path:'/freshair', component:Freshair},
		{path:'/pipeline', component:Pipeline},
		{path:'/radiator', component:Radiator},
		{path:'/service', component:Service},
		{path:'/kontakt', component:Kontakt},
		{path:'/ueberuns', component:Ueberuns},
		{path:'/impressum', component:Impressum},
		{path:'*', redirect:'/home'}
	]
