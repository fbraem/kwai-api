import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';

import User from '@/models/users/User';

const state = () => {
  return {
    meta: null,
    users: [],
    error: null
  };
};

const getters = {
  user: (state) => (id) => {
    return state.users.find((user) => user.id === id);
  },
};

const mutations = {
  users(state, { meta, data }) {
    state.users = data;
    state.error = null;
  },
  user(state, { data }) {
    var index = state.users.findIndex((u) => u.id === data.id);
    if (index !== -1) {
      Vue.set(state.users, index, data);
    } else {
      state.users.push(data);
    }
    state.error = null;
  },
  error(state, data) {
    state.error = data;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'users.browse', { root: true });
    try {
      var api = new JSONAPI({ source: User });
      let result = await api.get();
      commit('users', result);
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'users.browse', { root: true });
    }
  },
  async read({ dispatch, getters, commit }, payload) {
    var user = getters['user'](payload.id);
    if (user) { // already read
      return user;
    }

    dispatch('wait/start', 'users.read', { root: true });
    try {
      var api = new JSONAPI({ source: User });
      var result = await api.get(payload.id);
      commit('user', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'users.read', { root: true });
    }
  },
  async readWithAbilities({ dispatch, commit }, { id }) {
    dispatch('wait/start', 'user.read.abilities', { root: true });
    try {
      const api = new JSONAPI({ source: User });
      commit('user', await api.with('abilities').get(id));
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'user.read.abilities', { root: true });
    }
  },
  async createWithToken({ state, getters, commit, context }, payload) {
    var user = null;
    try {
      const api = new JSONAPI({ source: User});
      const result = api.custom({
        method: 'POST',
        path: payload.token,
        data: payload.user.serialize()
      });
      commit('user', result);
    } catch (error) {
      commit('error', error);
      throw error;
    }
    return user;
  },
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
