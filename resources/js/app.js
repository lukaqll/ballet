require('./bootstrap');

window.Vue = require('vue').default;

import VueRouter from 'vue-router';
import routes from './routes';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import { BootstrapVueIcons } from 'bootstrap-vue'
import VueTheMask from 'vue-the-mask';
import VMoney from 'v-money';
import vSelect from "vue-select";
import VueMask from 'v-mask'

Vue.use(VueRouter)
Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
Vue.use(IconsPlugin)
Vue.use(VueTheMask)
Vue.use(VMoney)
Vue.component("v-select", vSelect);
Vue.use(VueMask);

const app = new Vue({
    el: '#app',
    router: new VueRouter(routes)
});
