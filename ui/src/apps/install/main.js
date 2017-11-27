import "babel-polyfill";

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);
import store from '@/js/store';
import installStore from './store';
store.registerModule('installModule', installStore);

import Vuetify from 'vuetify';
Vue.use(Vuetify);
import '@/../node_modules/vuetify/dist/vuetify.min.css';

import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);

import VueKindergarten from 'vue-kindergarten';
Vue.use(VueKindergarten, {
    child : (store) => {
        return store ? store.getters.user : null;
    }
});
import basePerimeter from '@/js/perimeter.js';

import { VueExtendLayout, layout } from 'vue-extend-layout';
Vue.use(VueExtendLayout);

import InstallApp from './App.vue';

const router = new VueRouter({
    routes : [
        {
            path : '/',
            component : InstallApp
        }
    ]
});

var app = new Vue({
    router,
    store,
    perimeters : [
        basePerimeter
    ],
    ...layout
}).$mount('#clubmanApp');
