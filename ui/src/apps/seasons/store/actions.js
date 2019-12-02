import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Season from '@/models/Season';

const browse = async({ dispatch, commit }, payload) => {
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
};

const save = async({ commit }, season) => {
  try {
    var api = new JSONAPI({ source: Season });
    var result = await api.save(season);
    commit('season', result);
  } catch (error) {
    commit('error', error);
    throw error;
  }
};

const read = async({ dispatch, getters, commit, state }, payload) => {
  // Don't read it again when it is active ...
  if (state.active?.id === payload.id) {
    return;
  }

  var season = getters['season'](payload.id);
  if (season) { // already read
    commit('active', season);
    return;
  }

  dispatch('wait/start', 'seasons.read', { root: true });
  try {
    var api = new JSONAPI({ source: Season });
    var result = await api.get(payload.id);
    commit('active', result.data);
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'seasons.read', { root: true });
  }
};

const reset = ({ commit }) => {
  commit('reset');
};

const set = ({ commit }, season) => {
  commit('active', season);
};

export const actions = {
  browse,
  read,
  save,
  reset,
  set
};
