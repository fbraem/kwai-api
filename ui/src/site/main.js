import "babel-polyfill";

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);

import UIkit from 'uikit';
import UIkitIcons from 'uikit/dist/js/uikit-icons';
UIkit.use(UIkitIcons);
import css from 'uikit/dist/css/uikit.min.css';

import VueI18n from 'vue-i18n';
Vue.use(VueI18n);
const i18n = new VueI18n({
    locale : 'nl',
    fallbackLocale : 'nl'
});

import Icon from 'vue-awesome/components/Icon';
Vue.component('fa-icon', Icon);

import moment from 'moment';
moment.locale('nl');

import store from '@/stores/root';

import VueKindergarten from 'vue-kindergarten';
Vue.use(VueKindergarten, {
    child : (store) => {
        return store ? store.getters.user : null;
    }
});
import perimeters from '@/perimeters';

import VueWait from 'vue-wait';
Vue.use(VueWait);

import { VueExtendLayout, layout } from 'vue-extend-layout';
Vue.use(VueExtendLayout);

import routes from '@/routes';

const router = new VueRouter({
    routes : routes()
});

import VueScrollBehavior from 'vue-scroll-behavior';
Vue.use(VueScrollBehavior, { router : router });

var app = new Vue({
    router,
    store,
    perimeters : perimeters(),
    ...layout,
    wait : new VueWait({
        useVuex : true
    }),
    i18n
}).$mount('#clubmanApp');
