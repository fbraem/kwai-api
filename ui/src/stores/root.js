import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import config from 'config';

const state = {
  global: {
    page: {
      title: config.title,
      facebook: config.facebook,
      subTitle: '',
    },
    error: null
  }
};

const getters = {
  title(state) {
    return state.global.page.title;
  },
  subTitle(state) {
    return state.global.page.subTitle;
  },
  facebook(state) {
    return state.global.page.facebook;
  },
  error(state) {
    return state.global.error;
  },
};

const mutations = {
  title(state, text) {
    state.global.page.title = text;
  },
  subTitle(state, text) {
    state.global.page.subTitle = text;
  },
  error(state, payload) {
    state.global.error = payload;
  },
};

const actions = {
  setTitle({ commit }, text) {
    commit('title', text);
  },
  setSubTitle({ commit }, text) {
    commit('subTitle', text);
  },
};

import { abilityPlugin } from '@/js/ability';

export default new Vuex.Store({
  namespaced: true,
  plugins: [ abilityPlugin ],
  state: state,
  getters: getters,
  mutations: mutations,
  actions: actions,
});
