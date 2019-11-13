/**
 * Vuex store for members
 */
import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Member from '@/models/Member';

function initialize() {
  return {
    members: null,
    selected: null,
    meta: null
  };
}

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
    state.error = null;
    if (state.members == null) {
      return;
    }
    var index = state.members.findIndex((m) => m.id === data.id);
    if (index !== -1) {
      Vue.set(state.members, index, data);
    }
  },
  select(state, data) {
    state.selected = data;
  },
  reset(state) {
    Object.assign(state, initialize());
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
  async read({ getters, dispatch, commit, state }, payload) {
    // Don't read the same member again
    if (state.selected?.id === payload.id) {
      return;
    }

    // Check if the member was already present in the list
    var member = getters['member'](payload.id);
    if (member) { // already read
      commit('select', member);
      return;
    }

    dispatch('wait/start', 'members.read', { root: true });
    var api = new JSONAPI({ source: Member });
    try {
      var result = await api.get(payload.id);
      commit('member', result);
      commit('select', result.data);
    } catch (error) {
      commit('error', error);
    } finally {
      dispatch('wait/end', 'members.read', { root: true });
    }
  },

  async readTeams({ getters, state, dispatch, commit }, payload) {
    // Don't read the same member again ...
    if (state.selected?.id === payload.id && state.selected.teams) {
      return;
    }

    // Check if the member is already available in the list
    var member = getters['member'](payload.id);
    if (member && member.teams) { // already read
      commit('select', member);
      return;
    }

    dispatch('wait/start', 'members.read', { root: true });
    var api = new JSONAPI({ source: Member });
    try {
      var result = await api.with('teams').get(payload.id);
      commit('member', result);
      commit('select', result.data);
    } catch (error) {
      commit('error', error);
    } finally {
      dispatch('wait/end', 'members.read', { root: true });
    }
  },
  reset({ commit }) {
    commit('reset');
  }
};

export default {
  namespaced: true,
  state: initialize(),
  getters: getters,
  mutations: mutations,
  actions: actions,
  modules: {
  },
};
