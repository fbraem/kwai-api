import Vue from 'vue';

import OAuth from '@/js/oauth';
const oauth = new OAuth();

import Vuex from 'vuex';
Vue.use(Vuex);

import URI from 'urijs';
import moment from 'moment';

import Team from './models/Team';
import Member from './models/Member';
import TeamType from './models/TeamType';

const state = {
    types : [],
    teams : [],
    availableMembers : [],
    error : null
};

const getters = {
    types(state) {
        return state.types;
    },
    type: (state) => (id) => {
        return state.types.find((type) => type.id == id);
    },
    teams(state) {
        return state.teams;
    },
    team: (state) => (id) => {
        return state.teams.find((team) => team.id == id);
    },
    members: (state) => (id) => {
        var team = state.teams.find((team) => team.id == id);
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
    }
};

const mutations = {
  types(state, types) {
      state.types = types;
  },
  addType(state, type) {
      state.types.unshift(type);
  },
  modifyType(state, type) {
      var index = state.types.findIndex((t) => t.id == type.id);
      if (state.types[index]) state.types[index] = type;
  },
  teams(state, teams) {
      state.teams = teams;
  },
  team(state, team) {
      var index = state.teams.findIndex((t) => t.id == team.id);
      if (index != -1) {
          Vue.set(state.teams, index, team);
      } else {
          state.teams.push(team);
      }
  },
  setMembers(state, data) {
      var index = state.teams.findIndex((t) => t.id == data.id);
      if (state.teams[index]) Vue.set(state.teams[index], 'members', data.members);
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
  success(state) {
      state.error = null;
  }
};

const actions = {
    async browse({ dispatch, commit }, payload) {
        dispatch('wait/start', 'teams.browse', { root : true });
        const team = new Team();
        try {
            let teams = await team.get();
            commit('teams', teams);
            commit('success');
            dispatch('wait/end', 'teams.browse', { root : true });
        } catch(error) {
            commit('error', error);
            dispatch('wait/end', 'teams.browse', { root : true });
            throw error;
        }
    },
    async save({ dispatch, commit }, team) {
        var newTeam = null;
        try  {
            newTeam = await team.save();
            commit('team', newTeam);
            commit('success');
            return newTeam;
        } catch(error) {
            commit('error', error);
            throw error;
        }
    },
    async read({ dispatch, getters, commit }, payload) {
        var team = getters['team'](payload.id);
        if (team) { // already read
            commit('success');
            return team;
        }

        dispatch('wait/start', 'teams.read', { root : true });
        let model = new Team();
        try {
            team = await model.find(payload.id);
            commit('team', team);
            commit('success');
            dispatch('wait/end', 'teams.read', { root : true });
        } catch(error) {
            commit('error', error);
            dispatch('wait/end', 'teams.read', { root : true });
            throw error;
        }
        return team;
    },
    async members({ dispatch, commit }, payload) {
        dispatch('wait/start', 'teams.members', { root : true });
        var team = new Team();
        try {
            const teamWithMembers = await team.with(['members']).find(payload.id);
            commit('team', teamWithMembers);
            commit('success');
            dispatch('wait/end', 'teams.members', { root : true });
        } catch(error) {
            commit('error', error);
            dispatch('wait/end', 'teams.members', { root : true });
            throw error;
        }
    },
    async addMembers({ getters, commit }, payload) {
        var team = getters['team'](payload.id);
        try {
            let members = await team.attach(team.id, payload.members);
            commit('setMembers', {
                id : payload.id,
                members : members
            });
            commit('success');
        } catch(error) {
            commit('error', error);
            console.log(error);
        }
    },
    async deleteMembers({ getters, commit }, payload) {
        var team = getters['team'](payload.id);
        try {
            let members = await team.detach(team.id, payload.members);
            commit('setMembers', {
                id : payload.id,
                members : members
            });
            commit('success');
        } catch(error) {
            commit('error', error);
            console.log(error);
        }
    },
    async availableMembers({ dispatch, commit }, payload) {
        commit('clearAvailableMembers');
        dispatch('wait/start', 'teams.availableMembers', { root : true });

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
            commit('success');
            dispatch('wait/end', 'teams.availableMembers', { root : true });
        } catch(error) {
            commit('error', error);
            dispatch('wait/end', 'teams.availableMembers', { root : true });
            throw error;
        }
    },
    async browseType({ dispatch, commit }, payload) {
        dispatch('wait/start', 'teamtypes.browse', { root : true });
        const type = new TeamType();
        let types = await type.get();
        commit('types', types);
        commit('success');
        dispatch('wait/end', 'teamtypes.browse', { root : true });
    },
    createType(context, payload) {
        return oauth.post('api/teams/types', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('addType', {
                type : result.data
            });
            context.commit('success');
            return result.data;
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    updateType(context, payload) {
        return new Promise((resolve, reject) => {
            oauth.patch('api/teams/types/' + payload.data.id, {
                data : payload
            }).then((res) => {
                var api = new JSONAPI();
                var result = api.parse(res.data);
                context.commit('modifyType', {
                    type : result.data
                });
                context.commit('success');
                resolve();
            }).catch((error) => {
                context.commit('error', error);
                reject();
            });
        });
    },
    readType(context, payload) {
        var type = context.getters['type'](payload.id);
        if (type) { // already read
            context.commit('success');
            return;
        }

        oauth.get('api/teams/types/' + payload.id, {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('addType', {
                type : result.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
    }
};

export default {
    namespaced : true,
    state : state,
    getters : getters,
    mutations : mutations,
    actions : actions,
    modules: {
    }
};
