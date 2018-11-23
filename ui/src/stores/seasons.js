import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Season from '@/models/Season';

const state = {
  seasons: null,
  error: null,
};

const getters = {
  seasons(state) {
    return state.seasons;
  },
  season: (state) => (id) => {
    if (state.seasons) {
      return state.seasons.find((s) => s.id === id);
    }
    return null;
  },
  error(state) {
    return state.error;
  },
};

const mutations = {
  seasons(state, seasons) {
    state.error = null;
    state.seasons = seasons;
  },
  season(state, season) {
    state.error = null;
    if (state.seasons == null) {
      state.seasons = [];
    }
    var index = state.seasons.findIndex((s) => s.id === season.id);
    if (index !== -1) {
      Vue.set(state.seasons, index, season);
    } else {
      state.seasons.push(season);
    }
  },
  error(state, error) {
    state.error = error;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'seasons.browse', { root: true });
    const season = new Season();
    try {
      let seasons = await season.get();
      commit('seasons', seasons);
      dispatch('wait/end', 'seasons.browse', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'seasons.browse', { root: true });
      throw error;
    }
  },
  async save({ commit }, season) {
    var newSeason = null;
    try {
      newSeason = await season.save();
      commit('season', newSeason);
      return newSeason;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  async read({ dispatch, getters, commit }, payload) {
    var season = getters['season'](payload.id);
    if (season) { // already read
      return season;
    }

    dispatch('wait/start', 'seasons.read', { root: true });
    let model = new Season();
    try {
      season = await model.find(payload.id);
      commit('season', season);
      dispatch('wait/end', 'seasons.read', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'seasons.read', { root: true });
      throw error;
    }
    return season;
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
