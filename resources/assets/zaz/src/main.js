import Vue from 'vue'

import 'normalize.css/normalize.css'// A modern alternative to CSS resets

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import Vepie from 'v-charts/lib/pie'
import '@/styles/index.scss' // global css
import App from './App'
import HttpRequest from '@/utils/HttpRequest'
Vue.component(Vepie.name, Vepie)

import router from './router'
import store from './store'

import '@/icons' // icon
import '@/permission' // permission control

Vue.use(ElementUI)
Vue.use(HttpRequest)
Vue.config.productionTip = false

new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
})
