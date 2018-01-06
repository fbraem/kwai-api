import "babel-polyfill";

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);

import Vuetify from 'vuetify';
Vue.use(Vuetify);
import 'vuetify/dist/vuetify.min.css';

import VueI18n from 'vue-i18n';
Vue.use(VueI18n);
import messages from './lang/nl';
const i18n = new VueI18n({
    locale : 'nl',
    fallbackLocale : 'nl',
    messages
});
import moment from 'moment';
moment.locale('nl');

import store from '@/js/store';

import newsStore from '@/apps/news/store';
store.registerModule('newsModule', newsStore);

import categoryStore from '@/apps/categories/store';
store.registerModule('categoryModule', categoryStore);

import VueKindergarten from 'vue-kindergarten';
Vue.use(VueKindergarten, {
    child : (store) => {
        return store ? store.getters.user : null;
    }
});
import basePerimeter from '@/js/perimeter.js';
import newsPerimeter from '@/apps/news/perimeter.js';
import authPerimeter from '@/apps/auth/perimeter.js';

import { VueExtendLayout, layout } from 'vue-extend-layout';
Vue.use(VueExtendLayout);

import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);

var routes = [];
import siteRoutes from './routes';
routes = routes.concat(siteRoutes);
import newsRoutes from '@/apps/news/routes';
routes = routes.concat(newsRoutes);

const router = new VueRouter({
    routes : routes
});

var app = new Vue({
    router,
    store,
    perimeters : [
        basePerimeter,
        authPerimeter,
        newsPerimeter
    ],
    ...layout,
    i18n
}).$mount('#clubmanApp');
