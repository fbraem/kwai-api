import "babel-polyfill";

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);
import store from '@/js/store.js';
import authStore from './store';
store.registerModule('authModule', authStore);

import Vuetify from 'vuetify';
Vue.use(Vuetify);
import '@/../node_modules/vuetify/dist/vuetify.min.css';

import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);

import VueKindergarten from 'vue-kindergarten';
Vue.use(VueKindergarten, {
    child : (store) => {
        return store ? store.getters.oauth : null;
    }
});
import basePerimeter from '@/js/perimeter.js';
import authPerimeter from './perimeter.js';

import { VueExtendLayout, layout } from 'vue-extend-layout';
Vue.use(VueExtendLayout);

import AuthApp from './App.vue';
//import AuthLogout from './app/logout.vue';

const router = new VueRouter({
    routes : [
        {
            path : '/',
            component : AuthApp
        },
/*
        {
            path : '/logout',
            component : AuthLogout
        }
*/
    ]
});

var app = new Vue({
    router,
    store,
    perimeters : [
        basePerimeter,
        authPerimeter
    ],
    ...layout
}).$mount('#clubmanApp');
