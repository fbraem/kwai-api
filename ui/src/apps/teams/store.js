import Vue from 'vue';

import OAuth from '@/js/oauth';
const oauth = new OAuth();
import axios from 'axios';

import Vuex from 'vuex';
Vue.use(Vuex);

import find from 'lodash/find';
import unionBy from 'lodash/unionBy';

import URI from 'urijs';
import moment from 'moment';

import JSONAPI from '@/js/JSONAPI';

const state = {
    types : [],
    teams : [],
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
        return find(state.types, ['id', id]);
    },
    teams(state) {
        return state.teams;
    },
    team: (state) => (id) => {
        return find(state.teams, ['id', id]);
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
      state.types = unionBy([data.type], state.types, 'id');
  },
  teams(state, data) {
      state.teams = data.teams;
  },
  addTeam(state, data) {
      state.teams.unshift(data.team);
  },
  modifyTeam(state, data) {
      state.teams = unionBy([data.team], state.teams, 'id');
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
            context.commit('addTeam', {
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
