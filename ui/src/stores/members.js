/**
 * Vuex store for members
 */
import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Member from '@/models/Member';

const state = {
  members: null,
  error: null,
  meta: null
};

const getters = {
  /**
   * Gets a member from the store
   */
  member: (state) => (id) => {
    if (state.members == null) return null;
    return state.members.find((member) => member.id === id);
  }
};

const mutations = {
  /**
   * Mutate members
   */
  members(state, { meta, data }) {
    state.members = data;
    state.meta = meta;
    state.error = null;
  },
  /**
   * Mutate a member
   */
  member(state, { data }) {
    if (state.members == null) {
      state.members = [];
    }
    var index = state.members.findIndex((m) => m.id === data.id);
    if (index !== -1) {
      Vue.set(state.members, index, data);
    } else {
      state.members.push(data);
    }
    state.error = null;
  },
  /**
   * Mutate the error
   */
  error(state, error) {
    state.error = error;
  },
};

const actions = {
  /**
   * Get all members
   */
  async browse({ dispatch, commit }, payload = {}) {
    dispatch('wait/start', 'members.browse', { root: true });

    var api = new JSONAPI({ source: Member });
    if (payload.name) {
      api.where('name', payload.name);
    }
    if (payload.active) {
      api.where('active', payload.active);
    }

    try {
      let result = await api.get();
      commit('members', result);
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'members.browse', { root: true });
    }
  },
  /**
   * Read a member.
   */
  async read({ getters, dispatch, commit }, payload) {
    var member = getters['member'](payload.id);
    if (member) { // already read
      return member;
    }

    dispatch('wait/start', 'members.read', { root: true });
    var api = new JSONAPI({ source: Member });
    try {
      var result = await api.get(payload.id);
      commit('member', result);
      return result.data;
    } catch (error) {
      commit('error', error);
    } finally {
      dispatch('wait/end', 'members.read', { root: true });
    }
  },

  async readTeams({ getters, dispatch, commit }, payload) {
    var member = getters['member'](payload.id);
    if (member && member.teams) { // already read
      return;
    }

    dispatch('wait/start', 'members.read', { root: true });
    var api = new JSONAPI({ source: Member });
    try {
      var result = await api.with('teams').get(payload.id);
      commit('member', result);
      return result.data;
    } catch (error) {
      commit('error', error);
    } finally {
      dispatch('wait/end', 'members.read', { root: true });
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
