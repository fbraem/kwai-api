import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Member from '@/models/Member';

const state = {
  members: null,
  error: null,
};

const getters = {
  members(state) {
    return state.members;
  },
  member: (state) => (id) => {
    if (state.members) {
      return find(state.members, ['id', id]);
    }
    return null;
  },
  error(state) {
    return state.error;
  },
};

const mutations = {
  members(state, members) {
    state.members = members;
    state.error = null;
  },
  member(state, member) {
    if (state.members == null) {
      state.members = [];
    }
    var index = state.members.findIndex((m) => m.id === member.id);
    if (index !== -1) {
      Vue.set(state.members, index, member);
    } else {
      state.members.push(member);
    }
    state.error = null;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    payload = payload || {};

    dispatch('wait/start', 'members.browse', { root: true });

    const member = new Member();
    if (payload.name) {
      member.where('name', payload.name);
    }

    try {
      let members = await member.get();
      commit('members', members);
      dispatch('wait/end', 'members.browse', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'members.browse', { root: true });
      throw error;
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
