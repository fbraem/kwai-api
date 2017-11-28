import "babel-polyfill";

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);

import Vuetify from 'vuetify';
Vue.use(Vuetify);
import '@/../node_modules/vuetify/dist/vuetify.min.css';

import VueI18n from 'vue-i18n';
Vue.use(VueI18n);
import messages from './lang/nl.js';
const i18n = new VueI18n({
    locale : 'nl',
    fallbackLocale : 'nl',
    messages
});

import store from '../js/store.js';

import VueKindergarten from 'vue-kindergarten';
Vue.use(VueKindergarten, {
  child : store => store.getters.user
});
import basePerimeter from '@/js/perimeter.js';

import { VueExtendLayout, layout } from 'vue-extend-layout';
Vue.use(VueExtendLayout);

import SiteApp from './App.vue';

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
    perimeters : [
        basePerimeter
    ],
    ...layout,
    i18n
}).$mount('#clubmanApp');
