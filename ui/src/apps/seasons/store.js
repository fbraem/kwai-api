import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Season from './models/Season';

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
        return state.seasons.find((s) => s.id == id);
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
  seasons(state, seasons) {
      state.seasons = seasons;
  },
  season(state, season) {
      var index = state.seasons.findIndex((s) => s.id == season.id);
      if (index != -1) {
          Vue.set(state.seasons, index, season);
      } else {
          state.seasons.push(season);
      }
  },
  modifySeason(state, season) {
      var index = state.seasons.findIndex((s) => s.id == season.id);
      if (index != -1) {
          state.seasons[index] = season;
      } else {
          state.seasons.push(season);
      }
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
    async browse({ state, getters, commit, context }, payload) {
        commit('loading');
        const season = new Season();
        let seasons = await season.get();
        commit('seasons', seasons);
        commit('success');
    },
    async save({ state, getters, commit, context }, season) {
        var newSeason = null;
        try  {
            newSeason = await season.save();
            commit('season', newSeason);
            return newSeason;
        } catch(error) {
            commit('error', error);
            throw error;
        }
    },
    async read({ state, getters, commit, context }, payload) {
        commit('loading');
        var season = getters['season'](payload.id);
        if (season) { // already read
            commit('success');
            return season;
        }

        let model = new Season();
        try {
            season = await model.find(payload.id);
            commit('season', season);
            commit('success');
        } catch(error) {
            console.log(error);
            commit('error', error);
        }
        return season;
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
