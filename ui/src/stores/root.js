import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import config from 'config';

import OAuth from '@/js/oauth';
const oauth = new OAuth();

const state = {
  global: {
    user: {
      authenticated: oauth.isAuthenticated(),
    },
    page: {
      title: config.title,
      facebook: config.facebook,
      subTitle: '',
    },
    error: null
  }
};

const getters = {
  user(state) {
    return state.global.user;
  },
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
  authenticated(state, sw) {
    state.global.user.authenticated = sw;
  },
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
  async login({ commit, dispatch }, payload) {
    dispatch('wait/start', 'auth.login', { root: true });
    try {
      await oauth.login(payload.email, payload.password);
      commit('authenticated', true);
    } catch (error) {
      dispatch('wait/end', 'auth.login', { root: true });
      commit('error', error);
      commit('authenticated', false);
      throw (error);
    }
    dispatch('wait/end', 'auth.login', { root: true });
  },
  async logout({ commit }) {
    await oauth.logout();
    commit('authenticated', false);
  },
  setTitle({ commit }, text) {
    commit('title', text);
  },
  setSubTitle({ commit }, text) {
    commit('subTitle', text);
  },
};

export default new Vuex.Store({
  namespaced: true,
  state: state,
  getters: getters,
  mutations: mutations,
  actions: actions,
  modules: {
  },
});
