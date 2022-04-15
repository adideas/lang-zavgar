import Vue from 'vue'
import Cookies from 'js-cookie'
import Element from 'element-ui'
import locale from 'element-ui/lib/locale/lang/en'
import App from './App'
import store from './store'
import router from './router'
import Query from './query/index'

import 'normalize.css/normalize.css' // a modern alternative to CSS resets
import './styles/element-variables.scss'
import '@/styles/index.scss'
import './icons' // icon
import './permission' // permission control
import './pusher' // permission control

import './components/HeaderSticky' // Липкий хеадер
Vue.use(Query)
Vue.use(Element, {
  size: Cookies.get('size') || 'medium', locale
})

Vue.config.productionTip = false

new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
})
