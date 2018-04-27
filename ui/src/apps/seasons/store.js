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
  addSeason(state, season) {
      state.seasons.unshift(season);
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
    async browse(context, payload) {
        context.commit('loading');
        const season = new Season();
        const fetchSeasons = async () => {
            let seasons = await season.all();
            context.commit('seasons', seasons.data);
        };
        season.call(fetchSeasons);
        context.commit('success');
    },
    async create(context, season) {
        var newSeason = null;
        const create = async () => {
            newSeason = await season.create();
            context.commit('addSeason', newSeason);
        }
        await season.call(create)
            .catch((error) => {
                context.commit('error', error);
            });
        return newSeason;
    },
    async update(context, season) {
        context.commit('loading');
        var updatedSeason = null;
        const update = async () => {
            updatedSeason = season.save();
        };
        await season.call(update)
            .then(() => {
                context.commit('success');
            })
            .catch((error) => {
                context.commit('error', error);
            });
        return updatedSeason;
    },
    async read(context, payload) {
        context.commit('loading');
        var season = context.getters['season'](payload.id);
        if (season) { // already read
            context.commit('success');
            return;
        }

        context.commit('loading');
        season = new Season();
        const fetchSeason = async() => {
            let data = await season.find(payload.id);
            context.commit('modifySeason', data);
            context.commit('success');
        }
        season.call(fetchSeason);
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
