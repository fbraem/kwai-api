import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import config from 'config';

import tokenStore from '@/js/TokenStore';
import { http, http_auth } from '@/js/http';

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
    if (state.user.abilities) {
      for (let ability of state.user.abilities) {
        for (let rule of ability.rules) {
          rules.push({
            actions: rule.action.name,
            subject: rule.subject.name
          });
        }
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
      const form = {
        grant_type: 'password',
        client_id: config.clientId,
        username: payload.email,
        password: payload.password,
        scope: 'basic'
      };
      const json = await http
        .url('/auth/access_token')
        .formData(form)
        .post()
        .json()
      ;
      commit('login', {
        access_token: json.access_token,
        refresh_token: json.refresh_token
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
    const form = {
      refresh_token: state.tokenStore.refresh_token
    };
    try {
      await http_auth.url('auth/logout').formData(form).post();
    } catch (error) {
      console.log(error);
    }
    commit('logout');
  },
  async refresh({ commit, state }, failedRequest) {
    if (state.tokenStore.refresh_token) {
      const form = {
        grant_type: 'refresh_token',
        client_id: config.clientId,
        refresh_token: state.tokenStore.refresh_token
      };
      const json = await http
        .url('auth/access_token')
        .formData(form)
        .post()
        .json()
      ;
      commit('login', {
        access_token: json.access_token,
        refresh_token: json.refresh_token
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
