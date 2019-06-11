import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import RuleGroup from '@/models/users/RuleGroup';
import Rule from '@/models/users/Rule';

const state = {
  rule_groups: null,
  rules: null,
  error: null,
  meta: null
};

const getters = {
  rule_group: (state) => (id) => {
    if (state.rule_groups) {
      return state.rule_groups.find((r) => r.id === id);
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
  rule_groups(state, { meta, data }) {
    state.rule_groups = data;
    state.meta = meta;
    state.error = null;
  },
  rule_group(state, { data }) {
    if (state.rule_groups == null) {
      state.rule_groups = [];
    }
    var index = state.rule_groups.findIndex((r) => r.id === data.id);
    if (index !== -1) {
      Vue.set(state.rule_groups, index, data);
    } else {
      state.rule_groups.push(data);
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
    dispatch('wait/start', 'rule_groups.browse', { root: true });
    try {
      const api = new JSONAPI({ source: RuleGroup });
      commit('rule_groups', await api.get());
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'rule_groups.browse', { root: true });
    }
  },
  async save({ dispatch, commit }, rule) {
    try {
      const api = new JSONAPI({ source: RuleGroup });
      const result = await api.save(rule);
      commit('rule_group', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  async read({ dispatch, getters, commit }, payload) {
    var rule = getters['rule_group'](payload.id);
    if (rule) { // already read
      commit('error', null); // Reset error
      return rule;
    }

    dispatch('wait/start', 'rule_groups.read', { root: true });
    try {
      const api = new JSONAPI({ source: RuleGroup });
      const result = await api.get(payload.id);
      commit('rule_group', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'rule_groups.read', { root: true });
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
