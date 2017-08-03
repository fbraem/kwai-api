import "babel-polyfill";

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);
import store from '@/js/store.js';

import UIkit from 'uikit';
import Icons from 'uikit/dist/js/uikit-icons';
UIkit.use(Icons);
import css from 'uikit/dist/css/uikit.min.css';

import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);

import VueKindergarten from 'vue-kindergarten';
Vue.use(VueKindergarten, {
    child : (store) => {
        return store ? store.getters.activeUser : null;
    }
});

import AuthApp from './app.vue';
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
    store
}).$mount('#clubmanApp');
