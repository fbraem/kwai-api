import "babel-polyfill";

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);

import Vuetify from 'vuetify';
Vue.use(Vuetify);
import '@/../node_modules/vuetify/dist/vuetify.min.css';

import store from '../js/store.js';

import VueKindergarten from 'vue-kindergarten';
Vue.use(VueKindergarten, {
  child : store => store.getters.activeUser
});

import SiteApp from './app.vue';

const router = new VueRouter({
    routes : [
        {
            path : '/',
            component : SiteApp
        }
    ]
});

var app = new Vue({
    router,
    store,
    data : {}
}).$mount('#clubmanApp');
