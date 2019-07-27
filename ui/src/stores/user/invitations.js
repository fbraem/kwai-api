import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';

import Invitation from '@/models/users/Invitation';

const state = () => {
  return {
    meta: null,
    invitations: [],
    error: null
  };
};

const getters = {
  invitationByToken: (state) => (token) => {
    return state.invitations.find((invitation) => invitation.token === token);
  },
  invitationById: (state) => (id) => {
    return state.invitations.find((invitation) => invitation.id === id);
  },
};

const mutations = {
  invitation(state, { meta, data }) {
    var index = state.invitations.findIndex((i) => i.id === data.id);
    if (index !== -1) {
      Vue.set(state.invitations, index, data);
    } else {
      state.invitations.push(data);
    }
    state.meta = meta;
    state.error = null;
  },
  error(state, data) {
    state.error = data;
  },
};

const actions = {
  async invite({ dispatch, commit }, invitation) {
    dispatch('wait/start', 'user.invite.save', { root: true });
    try {
      const api = new JSONAPI({ source: Invitation });
      const result = await api.save(invitation);
      commit('invitation', result);
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'user.invite.save', { root: true });
    }
    return invitation;
  },
  async readInvitationByToken({ state, getters, commit, context }, payload) {
    var invitation = getters['invitationByToken'](payload.token);
    if (invitation) { // already read
      return invitation;
    }
    const api = new JSONAPI({ source: Invitation });
    try {
      var result = await api.get(payload.token);
      commit('invitation', result);
      return result.data;
    } catch (error) {
      commit('error', error);
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
