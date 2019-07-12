import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import config from 'config';

import tokenStore from '@/js/TokenStore';
import axios from '@/js/http';

import JSONAPI from '@/js/JSONAPI';
import User from '@/models/users/User';

import Lockr from 'lockr';
const USER_RULES_KEY = 'rules';
const USER_KEY = 'user';

const state = {
  user: Lockr.get(USER_KEY, null),
  tokenStore,
  rules: Lockr.get(USER_RULES_KEY, []),
  error: null,
};

const getters = {
  authenticated(state) {
    return state.tokenStore.access_token != null;
  },
  guest() {
    return state.tokenStore.access_token === null;
  }
};

const mutations = {
  login(state, { access_token, refresh_token }) {
    state.tokenStore.setTokens(access_token, refresh_token);
    state.error = null;
  },
  user(state, { data }) {
    state.user = data;
    let rules = [];
    for (let rule_group of state.user.rule_groups) {
      for (let rule of rule_group.rules) {
        rules.push({
          actions: rule.action.name,
          subject: rule.subject.name
        });
      }
    }
    state.rules = rules;
    Lockr.set(USER_RULES_KEY, state.rules);
    Lockr.set(USER_KEY, state.user);
    state.error = null;
  },
  logout(state) {
    state.tokenStore.clear();
    state.user = null;
    state.rules = [];
    Lockr.set(USER_RULES_KEY, []);
    state.error = {};
  },
  error(state, error) {
    state.error = error;
  }
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
      commit('login', {
        access_token: response.data.access_token,
        refresh_token: response.data.refresh_token
      });
    } catch (error) {
      commit('error', error);
      throw (error);
    } finally {
      dispatch('wait/end', 'auth.login', { root: true });
    }
  },
  async user({commit, dispatch}) {
    dispatch('wait/start', 'users.read', { root: true });
    try {
      var api = new JSONAPI({ source: User });
      var result = await api.path('auth').get();
      commit('user', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'users.read', { root: true });
    }
  },
  async logout({ commit, state }) {
    var form = new FormData();
    form.append('refresh_token', state.tokenStore.refresh_token);
    try {
      await axios.request({
        method: 'POST',
        url: config.api + '/auth/logout',
        data: form
      });
    } catch (error) {
      console.log(error);
    }
    commit('logout');
  },
  async refresh({ commit, state }, failedRequest) {
    if (state.tokenStore.refresh_token) {
      var form = new FormData();
      form.append('grant_type', 'refresh_token');
      form.append('client_id', config.clientId);
      form.append('refresh_token', state.tokenStore.refresh_token);
      var response = await axios.request({
        method: 'POST',
        url: config.api + '/auth/access_token',
        data: form
      });
      commit('login', {
        access_token: response.data.access_token,
        refresh_token: response.data.refresh_token
      });
    } else {
      throw failedRequest;
    }
  }
};

export default {
  namespaced: true,
  state: state,
  getters: getters,
  mutations: mutations,
  actions: actions,
  modules: {
  },
};
