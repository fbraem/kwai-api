import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

/**
 * Check registered module
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
Vuex.Store.prototype.setModule = function(aPath, module) {
  if (!this.hasModule(aPath)) {
    this.registerModule(aPath, module);
  }
};

import store from '@/stores/root';

export default () => {
  return store;
};
