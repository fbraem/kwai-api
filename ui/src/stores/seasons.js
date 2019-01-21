import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Season from '@/models/Season';

const state = {
  meta: null,
  seasons: null,
  error: null,
};

const getters = {
  seasons(state) {
    return state.seasons;
  },
  seasonsAsOptions(state) {
    var seasons = state.seasons;
    if (seasons) {
      seasons = seasons.map((season) => ({
        value: season.id,
        text: season.name }
      ));
    } else {
      seasons = [];
    }
    return seasons;
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
  seasons(state, {meta, data}) {
    state.error = null;
    state.seasons = data;
    state.meta = meta;
  },
  season(state, { data }) {
    state.error = null;
    if (state.seasons == null) {
      state.seasons = [];
    }
    var index = state.seasons.findIndex((s) => s.id === data.id);
    if (index !== -1) {
      Vue.set(state.seasons, index, data);
    } else {
      state.seasons.push(data);
    }
  },
  error(state, error) {
    state.error = error;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'seasons.browse', { root: true });
    try {
      var api = new JSONAPI({ source: Season });
      commit('seasons', await api.get());
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'seasons.browse', { root: true });
    }
  },
  async save({ commit }, season) {
    try {
      var api = new JSONAPI({ source: Season });
      var result = await api.save(season);
      commit('season', result);
      return result.data;
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
    try {
      var api = new JSONAPI({ source: Season });
      var result = await api.get(payload.id);
      commit('season', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'seasons.read', { root: true });
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
