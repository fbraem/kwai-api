import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import config from 'config';
import TokenStore from '@/js/TokenStore';
import axios from '@/js/http';

var tokenStore = new TokenStore();

const state = {
  global: {
    user: {
      authenticated: tokenStore.isAuthenticated(),
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
      var form = new FormData();
      form.append('grant_type', 'password');
      form.append('client_id', config.clientId);
      form.append('username', payload.email);
      form.append('password', payload.password);
      form.append('scope', 'basic');
      var response = await axios.request({
        method: 'POST',
        url: config.api + '/auth/access_token',
        data: form
      });
      tokenStore.setTokens(
        response.data.access_token,
        response.data.refresh_token
      );
      commit('authenticated', true);
    } catch (error) {
      commit('error', error);
      commit('authenticated', false);
      throw (error);
    } finally {
      dispatch('wait/end', 'auth.login', { root: true });
    }
  },
  async logout({ commit }) {
    var form = new FormData();
    form.append('refresh_token', tokenStore.getRefreshToken());
    try {
      await axios.request({
        method: 'POST',
        url: config.api + '/auth/logout',
        data: form
      });
    } catch (error) {
      console.log(error);
    }
    tokenStore.clear();
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
