import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Team from '@/models/Team';

const state = {
  teams: [],
  availableMembers: [],
  error: null,
};

const getters = {
  teams(state) {
    return state.teams;
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
  },
  availableMembers(state) {
    return state.availableMembers;
  },
  error(state) {
    return state.error;
  },
};

const mutations = {
  teams(state, teams) {
    state.teams = teams;
    state.error = null;
  },
  team(state, team) {
    var index = state.teams.findIndex((t) => t.id === team.id);
    if (index !== -1) {
      Vue.set(state.teams, index, team);
    } else {
      state.teams.push(team);
    }
    state.error = null;
  },
  setMembers(state, data) {
    var index = state.teams.findIndex((t) => t.id === data.id);
    if (state.teams[index])
      Vue.set(state.teams[index], 'members', data.members);
  },
  availableMembers(state, members) {
    state.availableMembers = members;
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
    const team = new Team();
    try {
      let teams = await team.get();
      commit('teams', teams);
      dispatch('wait/end', 'teams.browse', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'teams.browse', { root: true });
      throw error;
    }
  },
  async save({ dispatch, commit }, team) {
    var newTeam = null;
    try {
      newTeam = await team.save();
      commit('team', newTeam);
      return newTeam;
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
    let model = new Team();
    try {
      team = await model.find(payload.id);
      commit('team', team);
      dispatch('wait/end', 'teams.read', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'teams.read', { root: true });
      throw error;
    }
    return team;
  },
  async members({ dispatch, commit }, payload) {
    dispatch('wait/start', 'teams.members', { root: true });
    var team = new Team();
    try {
      const teamWithMembers = await team.with(['members']).find(payload.id);
      commit('team', teamWithMembers);
      dispatch('wait/end', 'teams.members', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'teams.members', { root: true });
      throw error;
    }
  },
  async addMembers({ getters, commit }, payload) {
    var team = getters['team'](payload.id);
    try {
      let members = await team.attach(team.id, payload.members);
      commit('setMembers', {
        id: payload.id,
        members: members,
      });
    } catch (error) {
      commit('error', error);
      console.log(error);
    }
  },
  async deleteMembers({ getters, commit }, payload) {
    var team = getters['team'](payload.id);
    try {
      let members = await team.detach(team.id, payload.members);
      commit('setMembers', {
        id: payload.id,
        members: members,
      });
    } catch (error) {
      commit('error', error);
      console.log(error);
    }
  },
  async availableMembers({ dispatch, commit }, payload) {
    commit('clearAvailableMembers');
    dispatch('wait/start', 'teams.availableMembers', { root: true });

    const team = new Team();
    if (payload.filter) {
      if (payload.filter.start_age) {
        team.where('start_age', '>=' + payload.filter.start_age);
      }
      if (payload.filter.end_age) {
        team.where('end_age', '<=' + payload.filter.end_age);
      }
      if (payload.filter.gender) {
        team.where('gender', payload.filter.gender);
      }
    }
    try {
      var members = await team.available(payload.id);
      commit('availableMembers', members);
      dispatch('wait/end', 'teams.availableMembers', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'teams.availableMembers', { root: true });
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
