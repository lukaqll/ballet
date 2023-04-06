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
import App from './App.vue'

Vue.use(VueRouter)
Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
Vue.use(IconsPlugin)
Vue.use(VueTheMask)
Vue.use(VMoney)
Vue.component("v-select", vSelect);
Vue.use(VueMask);
Vue.component('app', App)
const app = new Vue({
    el: '#app',
    router: new VueRouter(routes)
});

app.$on('bv::dropdown::show', e => {
    var $dropdown = $(e.target);
    var $container = $dropdown.parents('.table-responsive');

    if (!$container.length && !$dropdown.data('dropdown-container')) return

    if ($container.length) {
        $dropdown.data('dropdown-container', $container);
    } else {
        $container = $dropdown.data('dropdown-container');
    }

    $dropdown.css('top', ($container.offset().top + $container.outerHeight()) + 'px');
    $dropdown.css('left', $container.offset().left + 'px');
    $dropdown.css('position', 'absolute');
    $dropdown.css('display', 'block');
    $dropdown.css('z-index', '9999');
    const parent = $dropdown.parents('.modal-content')[0] || $dropdown.parents('.main-container')
    $dropdown.appendTo(parent);
})

app.$on('bv::dropdown::hide', e => {
    $(e.target).css('display', 'none');
})