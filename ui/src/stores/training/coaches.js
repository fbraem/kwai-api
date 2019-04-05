import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import TrainingCoach from '@/models/trainings/Coach';

const state = {
  coaches: null,
  error: null,
  meta: null
};

const getters = {
  coach: (state) => (id) => {
    if (state.coaches) {
      return state.coaches.find((coach) => coach.id === id);
    }
    return null;
  }
};

const mutations = {
  coaches(state, { meta, data }) {
    state.coaches = data;
    state.meta = meta;
    state.error = null;
  },
  coach(state, { data }) {
    if (state.coaches == null) {
      state.coaches = [];
    }
    var index = state.coaches.findIndex((c) => c.id === data.id);
    if (index !== -1) {
      Vue.set(state.coaches, index, data);
    } else {
      state.coaches.push(data);
    }
    state.error = null;
  },
  error(state, data) {
    state.error = data;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'training.coaches.browse', { root: true });
    try {
      var api = new JSONAPI({ source: TrainingCoach });
      api.sort('name');
      commit('coaches', await api.get());
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'training.coaches.browse', { root: true });
    }
  },
  async save({ dispatch, commit }, coach) {
    try {
      var api = new JSONAPI({ source: TrainingCoach });
      var result = await api.save(coach);
      commit('coach', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  async read({ dispatch, getters, commit }, payload) {
    var coach = getters['coach'](payload.id);
    if (coach) { // already read
      commit('error', null); // Reset error
      return coach;
    }

    dispatch('wait/start', 'training.coaches.read', { root: true });
    try {
      var api = new JSONAPI({ source: TrainingCoach });
      var result = await api.get(payload.id);
      commit('coach', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'training.coaches.read', { root: true });
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
