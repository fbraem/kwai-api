import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);

/**
 * Check registred module
 * @param {Array} aPath - path to module - ex: ['my', 'nested', 'module']
 * @return {Boolean}
 */
Vuex.Store.prototype.hasModule = function(aPath) {
  let m = this._modules.root;
  return aPath.every((p) => {
    m = m._children[p];
    return m;
  });
};
/**
 * Register a module if it is not yet registered
 */
Vuex.Store.prototype.setModule = async function(aPath, createFn) {
  var has = await this.hasModule(aPath);
  if (!has) {
    var m = await createFn();
    if (!m) {
      console.log("Can't create module ", aPath);
    } else {
      await this.registerModule(aPath, m.default);
    }
  }
};

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

import store from '@/stores/root';

/**
 * Initialise casl
 */
import { abilitiesPlugin } from '@casl/vue';
import ability from '@/js/ability';
Vue.use(abilitiesPlugin, ability);

import VueWait from 'vue-wait';
Vue.use(VueWait);

import Notifications from 'vue-notification';
Vue.use(Notifications);

import routes from '@/routes';

const router = new VueRouter({
  routes: routes(),
});
router.beforeEach(async(to, from, next) => {
  for (var r in to.matched) {
    var matched = to.matched[r];
    if (!matched.meta.called && matched.meta.stores) {
      matched.meta.called = true;
      for (var s in matched.meta.stores) {
        await store.setModule(
          matched.meta.stores[s].ns,
          matched.meta.stores[s].create
        );
      }
    }
  }
  next();
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
