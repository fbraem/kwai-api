import "babel-polyfill";

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);
import store from '@/js/store';
import newsStore from './store';
store.registerModule('categoryModule', newsStore);

import Vuetify from 'vuetify';
Vue.use(Vuetify);
import '@/../node_modules/vuetify/dist/vuetify.min.css';

import VueI18n from 'vue-i18n';
Vue.use(VueI18n);
import messages from './lang/lang.js';
const i18n = new VueI18n({
    locale : 'nl',
    fallbackLocale : 'nl',
    messages
});
import moment from 'moment';
moment.locale('nl');

import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);

import VueKindergarten from 'vue-kindergarten';
Vue.use(VueKindergarten, {
    child : (store) => {
        return store ? store.getters.user : null;
    }
});
import basePerimeter from '@/js/perimeter.js';
import categoryPerimeter from './perimeter.js';

import { VueExtendLayout, layout } from 'vue-extend-layout';
Vue.use(VueExtendLayout);

import CategoryApp from './App.vue';
import CategoryCreate from './app/CategoryCreate.vue';
import CategoryUpdate from './app/CategoryUpdate.vue';
import CategoryRead from './app/CategoryRead.vue';

const router = new VueRouter({
    routes : [
        {
            path : '/',
            component : CategoryApp
        },
        {
            path : '/create',
            component : CategoryCreate
        },
        {
            path : '/update/:id',
            component : CategoryUpdate
        },
        {
            path : '/create',
            component : CategoryCreate
        },
        {
            path : '/read/:id',
            component : CategoryRead
        }
    ]
});

var app = new Vue({
    router,
    store,
    perimeters : [
        basePerimeter,
        categoryPerimeter
    ],
    ...layout,
    i18n
}).$mount('#clubmanApp');
