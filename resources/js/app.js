require('./bootstrap');

window.Vue = require('vue').default;

import VueRouter from 'vue-router';
import routes from './routes';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import { BootstrapVueIcons } from 'bootstrap-vue'
import VueMask from 'v-mask';
  
Vue.use(VueRouter)
Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
Vue.use(IconsPlugin)
Vue.use(VueMask)

const app = new Vue({
    el: '#app',
    router: new VueRouter(routes)
});
