import "babel-polyfill";

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);
import store from '../../js/store.js';
import userStore from './store';
store.registerModule('userModule', userStore);

import UIkit from 'uikit';
import Icons from 'uikit/dist/js/uikit-icons';
UIkit.use(Icons);
import css from 'uikit/dist/css/uikit.min.css';

import VueKindergarten from 'vue-kindergarten';
Vue.use(VueKindergarten, {
    child : store => store.getters.activeUser
});

import UserApp from './app.vue';

const router = new VueRouter({
    routes : [
        {
            path : '/',
            component : UserApp
        }
    ]
});

var app = new Vue({
    router,
    store
}).$mount('#clubmanApp');
