import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

/**
 * Check registred module
 * @param {Array} aPath - path to module - ex: ['my', 'nested', 'module']
 * @return {Boolean}
 */
Vuex.Store.prototype.hasModule = function(aPath) {
  let m = this._modules.root;
  for (const p of aPath) {
    m = m._children[p];
    if (!m) return false;
  }
  return true;
};
/**
 * Register a module if it is not yet registered
 */
Vuex.Store.prototype.setModule = async function(aPath, createFn) {
  var has = this.hasModule(aPath);
  if (!has) {
    var m = await createFn();
    if (!m) {
      console.log("Can't create module ", aPath);
    } else {
      console.log('Register module ', aPath);
      await this.registerModule(aPath, m.default);
    }
  }
};

import store from '@/stores/root';

export default () => {
  return store;
};
