import Vue from 'vue';

import OAuth from '@/js/oauth';
const oauth = new OAuth();
import axios from 'axios';

import Vuex from 'vuex';
Vue.use(Vuex);

import URI from 'urijs';
import moment from 'moment';

import JSONAPI from '@/js/JSONAPI';

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
            return team.members;
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
  types(state, data) {
      state.types = data.types;
  },
  addType(state, data) {
      state.types.unshift(data.type);
  },
  modifyType(state, data) {
      var index = state.types.findIndex((type) => type.id == data.type.id);
      if (state.types[index]) state.types[index] = date.type;
  },
  teams(state, data) {
      state.teams = data.teams;
  },
  addTeam(state, data) {
      state.teams.unshift(data.team);
  },
  modifyTeam(state, data) {
      var index = state.teams.findIndex((team) => team.id == data.team.id);
      if (state.teams[index]) state.teams[index] = date.team;
  },
  members(state, data) {
      var team = state.teams.find((team) => team.id == data.team);
      if (team) Vue.set(team, 'members',data.members);
  },
  availableMembers(state, data) {
      state.availableMembers = data.members;
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
    browse(context, payload) {
        return new Promise((resolve, reject) => {
            oauth.get('api/teams', {
            }).then((res) => {
                var api = new JSONAPI();
                var teams = api.parse(res.data);
                context.commit('teams', {
                    teams : teams.data
                });
                resolve();
            }).catch((error) => {
                reject();
            });
        });
    },
    create(context, payload) {
        context.commit('loading');
        return oauth.post('api/teams', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('addTeam', {
                team : result.data
            });
            context.commit('success');
            return result.data;
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    read(context, payload) {
        context.commit('loading');
        var team = context.getters['team'](payload.id);
        if (team) { // already read
            context.commit('success');
            return;
        }

        oauth.get('api/teams/' + payload.id, {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('modifyTeam', {
                team : result.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    update(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            oauth.patch('api/teams/' + payload.data.id, {
                data : payload
            }).then((res) => {
                var api = new JSONAPI();
                var result = api.parse(res.data);
                context.commit('modifyTeam', {
                    team : result.data
                });
                context.commit('success');
                resolve();
            }).catch((error) => {
                context.commit('error', error);
                reject();
            });
        });
    },
    members(context, payload) {
        var members = context.getters['members'](payload.id);
        if (members) { // already read
            context.commit('success');
            return;
        }

        context.commit('loading');

        oauth.get('api/teams/' + payload.id + '/members', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('members', {
                team : payload.id,
                members : result.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    addMembers(context, payload) {
        context.commit('loading');
        oauth.post('api/teams/' + payload.id + '/members', {
            data : {
                data : payload.members
            }
        }).then((res) => {
            console.log(res);
        }).catch((error) => {
            console.log(error);
        });
    },
    deleteMembers(context, payload) {
        context.commit('loading');
        oauth.delete('api/teams/' + payload.id + '/members', {
            data : {
                data : payload.members
            }
        }).then((res) => {
            console.log(res);
        }).catch((error) => {
            console.log(error);
        });
    },
    availableMembers(context, payload) {
        context.commit('loading');
        context.commit('clearAvailableMembers');
        let uri = new URI('api/teams/' + payload.id + '/available_members');
        if (payload.filter) {
            if (payload.filter.start_age) {
                uri.addQuery('filter[start_age]', '>=' + payload.filter.start_age);
            }
            if (payload.filter.end_age) {
                uri.addQuery('filter[end_age]', '<=' + payload.filter.end_age);
            }
            if (payload.filter.gender) {
                uri.addQuery('filter[gender]', payload.filter.gender);
            }
        }
        oauth.get(uri.href(), {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('availableMembers', {
                team : payload.id,
                members : result.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    browseType(context, payload) {
        return new Promise((resolve, reject) => {
            oauth.get('api/teams/types', {
            }).then((res) => {
                var api = new JSONAPI();
                var types = api.parse(res.data);
                context.commit('types', {
                    types : types.data
                });
                resolve();
            }).catch((error) => {
                reject();
            });
        });
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
