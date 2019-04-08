import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import UIkit from 'uikit';
import UIkitIcons from 'uikit/dist/js/uikit-icons';
UIkit.use(UIkitIcons);
import 'uikit/dist/css/uikit.min.css';

import VueI18n from 'vue-i18n';
Vue.use(VueI18n);
const i18n = new VueI18n({
  locale: 'nl',
  fallbackLocale: 'nl',
});

import '@fortawesome/fontawesome-free/css/all.min.css';

import moment from 'moment';
moment.locale('nl');

import makeStore from '@/js/makeVuex';
var store = makeStore();

/**
 * Initialise casl
 */
import { abilitiesPlugin } from '@casl/vue';
import makeAbilities from '@/js/ability';
Vue.use(abilitiesPlugin, makeAbilities());

import VueWait from 'vue-wait';
Vue.use(VueWait);

import Notifications from 'vue-notification';
Vue.use(Notifications);

import routes from '@/routes';

const router = new VueRouter({
  routes: routes(),
});

import VueScrollBehavior from 'vue-scroll-behavior';
Vue.use(VueScrollBehavior, { router: router });

new Vue({
  router,
  store,
  wait: new VueWait({
    useVuex: true,
  }),
  i18n,
}).$mount('#clubmanApp');
