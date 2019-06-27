import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Ability from '@/models/users/Ability';
import Rule from '@/models/users/Rule';

const state = {
  abilities: null,
  rules: null,
  error: null,
  meta: null
};

const getters = {
  ability: (state) => (id) => {
    if (state.abilities) {
      return state.abilities.find((r) => r.id === id);
    }
    return null;
  },
  rule: (state) => (id) => {
    if (state.rules) {
      return state.rules.find((r) => r.id === id);
    }
    return null;
  },
};

const mutations = {
  abilities(state, { meta, data }) {
    state.abilities = data;
    state.meta = meta;
    state.error = null;
  },
  rule_group(state, { data }) {
    if (state.abilities == null) {
      state.abilities = [];
    }
    var index = state.abilities.findIndex((r) => r.id === data.id);
    if (index !== -1) {
      Vue.set(state.abilities, index, data);
    } else {
      state.abilities.push(data);
    }
    state.error = null;
  },
  rules(state, { meta, data }) {
    state.rules = data;
    state.meta = meta;
    state.error = null;
  },
  error(state, data) {
    state.error = data;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'abilities.browse', { root: true });
    try {
      const api = new JSONAPI({ source: Ability });
      commit('abilities', await api.get());
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'abilities.browse', { root: true });
    }
  },
  async save({ dispatch, commit }, rule) {
    try {
      const api = new JSONAPI({ source: Ability });
      const result = await api.save(rule);
      commit('ability', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  async read({ dispatch, getters, commit }, payload) {
    var rule = getters['ability'](payload.id);
    if (rule) { // already read
      commit('error', null); // Reset error
      return rule;
    }

    dispatch('wait/start', 'abilities.read', { root: true });
    try {
      const api = new JSONAPI({ source: Ability });
      const result = await api.get(payload.id);
      commit('ability', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'abilities.read', { root: true });
    }
  },

  async browseRules({ dispatch, commit }, payload) {
    dispatch('wait/start', 'rules.browse', { root: true });
    try {
      const api = new JSONAPI({ source: Rule });
      commit('rules', await api.get());
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'rules.browse', { root: true });
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
