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
    seasons : [],
    status : {
        loading : false,
        success : false,
        error : false
    }
};

const getters = {
    seasons(state) {
        return state.seasons;
    },
    season: (state) => (id) => {
        return find(state.seasons, ['id', id]);
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
  seasons(state, data) {
      state.seasons = data.seasons;
  },
  addSeason(state, data) {
      state.seasons.unshift(data.season);
  },
  modifySeason(state, data) {
      state.seasons = unionBy([data.season], state.seasons, 'id');
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
            oauth.get('api/seasons', {
            }).then((res) => {
                var api = new JSONAPI();
                var seasons = api.parse(res.data);
                context.commit('seasons', {
                    seasons : seasons.data
                });
                resolve();
            }).catch((error) => {
                reject();
            });
        });
    },
    create(context, payload) {
        context.commit('loading');
        return oauth.post('api/seasons', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('addSeason', {
                season : result.data
            });
            context.commit('success');
            return result.data;
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    update(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            oauth.patch('api/seasons/' + payload.data.id, {
                data : payload
            }).then((res) => {
                var api = new JSONAPI();
                var result = api.parse(res.data);
                context.commit('modifySeason', {
                    season : result.data
                });
                context.commit('success');
                resolve();
            }).catch((error) => {
                context.commit('error', error);
                reject();
            });
        });
    },
    read(context, payload) {
        context.commit('loading');
        var season = context.getters['season'](payload.id);
        if (season) { // already read
            context.commit('success');
            return;
        }

        oauth.get('api/seasons/' + payload.id, {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('addSeason', {
                season : result.data
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
