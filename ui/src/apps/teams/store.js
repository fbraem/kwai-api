import Vue from 'vue';

import OAuth from '@/js/oauth';
const oauth = new OAuth();

import Vuex from 'vuex';
Vue.use(Vuex);

import URI from 'urijs';
import moment from 'moment';

import JSONAPI from '@/js/JSONAPI';
import Team from './models/Team';
import Member from './models/Member';
import TeamType from './models/TeamType';

const state = {
    types : [],
    teams : null,
    availableMembers : [],
    status : {
        loading : false,
        success : false,
        error : false
    }
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
    loading(state) {
        return state.status.loading;
    },
    success(state) {
        return state.status.success;
    },
    error(state) {
        return state.status.error;
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
  addTeam(state, team) {
      state.teams.unshift(team);
  },
  setTeam(state, team) {
      var index = state.teams.findIndex((t) => t.id == team.id);
      if (state.teams[index]) Vue.set(state.teams, index, team);
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
  loading(state) {
      state.status = {
          loading : true,
          success: false,
          error : false
      };
  },
  success(state) {
      state.status = {
          loading : false,
          success: true,
          error : false
      };
  },
  error(state, payload) {
      state.status = {
          loading : false,
          success: false,
          error : payload
      };
  }
};

const actions = {
    async browse(context, payload) {
        const team = new Team();
        const fetchTeams = async () => {
            let teams = await team.get();
            context.commit('teams', teams);
        };
        team.call(fetchTeams);
    },
    async create(context, team) {
        var newTeam = null;
        const create = async () => {
            newTeam = team.save();
            context.commit('addTeam', newTeam);
        }
        await team.call(create)
            .catch((error) => {
                context.commit('error', error);
            });
        return newTeam;
    },
    async read(context, payload) {
        var team = context.getters['team'](payload.id);
        if (team) { // already read
            context.commit('success');
            return;
        }

        context.commit('loading');
        team = new Team();
        const fetchTeam = async() => {
            let data = await team.find(payload.id);
            context.commit('setTeam', data);
            context.commit('success');
        }
        team.call(fetchTeam);
    },
    async update(context, team) {
        context.commit('loading');
        var updatedTeam = null;
        const update = async () => {
            updatedTeam = team.save();
            context.commit('setTeam', updatedTeam);
            context.commit('success');
        };
        await team.call(update)
            .catch((error) => {
                context.commit('error', error);
            });
        return updatedTeam;
    },
    async members(context, payload) {
        var team = new Team();
        context.commit('loading');
        const fetchMembers = async() => {
            let teamWithMembers = await team.with(['members']).find(payload.id);
            context.commit('setTeam', teamWithMembers);
        }
        team.call(fetchMembers)
            .then(() => {
                context.commit('success');
            })
            .catch((error) => {
                context.commit('error', error);
            });
    },
    async addMembers(context, payload) {
        context.commit('loading');

        var team = context.getters['team'](payload.id);
        const attachMembers = async() => {
            let members = await team.attach(team.id, payload.members);
            context.commit('setMembers', {
                id : payload.id,
                members : members
            });
        }
        team.call(attachMembers)
            .then(() => {
                context.commit('success');
            })
            .catch((error) => {
                context.commit('error', error);
            });
    },
    async deleteMembers(context, payload) {
        context.commit('loading');

        var team = context.getters['team'](payload.id);
        const detachMembers = async() => {
            let members = await team.detach(team.id, payload.members);
            context.commit('setMembers', {
                id : payload.id,
                members : members
            });
        }
        team.call(detachMembers)
            .then(() => {
                context.commit('success');
            })
            .catch((error) => {
                context.commit('error', error);
            });
    },
    async availableMembers(context, payload) {
        context.commit('loading');
        context.commit('clearAvailableMembers');

        const team = new Team();
        const findAvailableMembers = async() => {
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
            var members = await team.available(payload.id);
            context.commit('availableMembers', members);
        }
        team.call(findAvailableMembers)
            .then(() => {
                context.commit('success');
            })
            .catch((error) => {
                context.commit('error', error);
            });
    },
    browseType(context, payload) {
        context.commit('loading');
        const type = new TeamType();
        const fetchTypes = async () => {
            let types = await type.all();
            context.commit('types', types);
        };
        type.call(fetchTypes);
        context.commit('success');
    },
    createType(context, payload) {
        context.commit('loading');
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
        context.commit('loading');
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
        context.commit('loading');
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
