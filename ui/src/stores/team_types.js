/**
 * Vuex store for teamtypes
 */
import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import TeamType from '@/models/TeamType';

const state = {
  types: null,
  error: null,
  meta: null
};

const getters = {
  /**
   * Returns a type
   */
  type: (state) => (id) => {
    if (state.types) {
      return state.types.find((type) => type.id === id);
    }
    return null;
  },
};

const mutations = {
  /**
   * Mutate all types
   */
  types(state, { meta, data }) {
    state.types = data;
    state.meta = meta;
    state.error = null;
  },
  /**
   * Mutate a type
   */
  type(state, { data }) {
    if (state.types == null) {
      state.types = [];
    }
    var index = state.types.findIndex((t) => t.id === data.id);
    if (index !== -1) {
      Vue.set(state.types, index, data);
    } else {
      state.types.push(data);
    }
    state.error = null;
  },
  /**
   * Mutate an error
   */
  error(state, data) {
    state.error = data;
  },
};

const actions = {
  /**
   * Get all team types
   */
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'team_types.browse', { root: true });
    try {
      const api = new JSONAPI({ source: TeamType });
      commit('types', await api.get());
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'team_types.browse', { root: true });
    }
  },
  /**
   * Save a type
   */
  async save({ commit }, type) {
    try {
      var api = new JSONAPI({ source: TeamType });
      var result = await api.save(type);
      commit('type', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  async read({ dispatch, getters, commit }, payload) {
    var type = getters['type'](payload.id);
    if (type) { // already read
      return type;
    }

    dispatch('wait/start', 'team_types.read', { root: true });
    try {
      var api = new JSONAPI({ source: TeamType });
      var result = await api.get(payload.id);
      commit('type', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'team_types.read', { root: true });
    }
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
