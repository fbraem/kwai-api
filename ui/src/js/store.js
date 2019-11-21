import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

import { abilityPlugin } from '@/js/ability';
const store = new Vuex.Store({
  namespaced: true,
  plugins: [ abilityPlugin ],
});

import authModule from '@/stores/auth';
store.registerModule('auth', authModule);
import categoryModule from '@/apps/categories/store';
store.registerModule('category', categoryModule);

export default store;
