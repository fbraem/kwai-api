import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Team from '@/models/Team';
import Member from '@/models/Member';

const state = {
  meta: null,
  teams: [],
  availableMembers: [],
  error: null,
};

const getters = {
  teamsAsOptions(state) {
    var teams = state.teams;
    if (teams) {
      teams = teams.map((team) => ({
        value: team.id,
        text: team.name
      }));
    } else {
      teams = [];
    }
    return teams;
  },
  team: (state) => (id) => {
    return state.teams.find((team) => team.id === id);
  },
  members: (state) => (id) => {
    var team = state.teams.find((team) => team.id === id);
    if (team) {
      if (team.members) {
        return team.members;
      }
    }
    return null;
  }
};

const mutations = {
  teams(state, { meta, data }) {
    state.teams = data;
    state.error = null;
    state.meta = meta;
  },
  team(state, { data }) {
    var index = state.teams.findIndex((t) => t.id === data.id);
    if (index !== -1) {
      Vue.set(state.teams, index, data);
    } else {
      state.teams.push(data);
    }
    state.error = null;
  },
  setMembers(state, data) {
    var index = state.teams.findIndex((t) => t.id === data.id);
    if (state.teams[index]) {
      if (state.teams[index].members) Vue.delete(state.teams[index], 'members');
      Vue.set(state.teams[index], 'members', data.members);
    }
  },
  availableMembers(state, { data }) {
    state.availableMembers = data;
  },
  clearAvailableMembers(state) {
    state.availableMembers = [];
  },
  error(state, data) {
    state.error = data;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'teams.browse', { root: true });
    try {
      var api = new JSONAPI({ source: Team });
      let result = await api.get();
      commit('teams', result);
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'teams.browse', { root: true });
    }
  },
  async save({ dispatch, commit }, team) {
    try {
      var api = new JSONAPI({ source: Team });
      var result = await api.save(team);
      commit('team', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  async read({ dispatch, getters, commit }, payload) {
    var team = getters['team'](payload.id);
    if (team) { // already read
      return team;
    }

    dispatch('wait/start', 'teams.read', { root: true });
    try {
      var api = new JSONAPI({ source: Team });
      var result = await api.get(payload.id);
      commit('team', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'teams.read', { root: true });
    }
  },
  async members({ dispatch, commit }, payload) {
    dispatch('wait/start', 'teams.members', { root: true });
    try {
      const api = new JSONAPI({ source: Team });
      const result = await api.with(['members']).get(payload.id);
      commit('team', result);
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'teams.members', { root: true });
    }
  },
  async addMembers({ getters, commit }, payload) {
    var team = getters['team'](payload.id);
    try {
      const api = new JSONAPI({source: Team, target: Member});
      let result = await api.attach(team, payload.members);
      commit('setMembers', {
        id: payload.id,
        members: result.data,
      });
    } catch (error) {
      commit('error', error);
      console.log(error);
    }
  },
  async deleteMembers({ getters, commit }, payload) {
    var team = getters['team'](payload.id);
    try {
      const api = new JSONAPI({source: Team, target: Member});
      let result = await api.detach(team, payload.members);
      commit('setMembers', {
        id: payload.id,
        members: result.data,
      });
    } catch (error) {
      commit('error', error);
      console.log(error);
    }
  },
  async availableMembers({ dispatch, commit }, payload) {
    commit('clearAvailableMembers');
    dispatch('wait/start', 'teams.availableMembers', { root: true });

    var api = new JSONAPI({ source: Team, target: Member});
    if (payload.filter) {
      if (payload.filter.start_age) {
        api.where('start_age', '>=' + payload.filter.start_age);
      }
      if (payload.filter.end_age) {
        api.where('end_age', '<=' + payload.filter.end_age);
      }
      if (payload.filter.gender) {
        api.where('gender', payload.filter.gender);
      }
    }
    try {
      var members = await api.custom({
        id: payload.id,
        path: 'available_members'
      });
      commit('availableMembers', members);
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'teams.availableMembers', { root: true });
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
