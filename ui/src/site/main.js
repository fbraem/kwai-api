import "babel-polyfill";

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);

import Vuetify from 'vuetify';
Vue.use(Vuetify);
import 'vuetify/dist/vuetify.min.css';

import UIkit from 'uikit';
import UIkitIcons from 'uikit/dist/js/uikit-icons';
UIkit.use(UIkitIcons);
import css from 'uikit/dist/css/uikit.min.css';

import VueI18n from 'vue-i18n';
Vue.use(VueI18n);
import messages from './lang/nl';
const i18n = new VueI18n({
    locale : 'nl',
    fallbackLocale : 'nl',
    messages
});

//import FlagIcon from 'vue-flag-icon';
//Vue.use(FlagIcon);
import Icon from 'vue-awesome/components/Icon';
Vue.component('fa-icon', Icon);

import moment from 'moment';
moment.locale('nl');

import store from '@/js/store';

import VueKindergarten from 'vue-kindergarten';
Vue.use(VueKindergarten, {
    child : (store) => {
        return store ? store.getters.user : null;
    }
});
import perimeters from '@/perimeters';

import { VueExtendLayout, layout } from 'vue-extend-layout';
Vue.use(VueExtendLayout);

//TODO: remove when all is changed to mixins ...
import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);

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
    i18n
}).$mount('#clubmanApp');
