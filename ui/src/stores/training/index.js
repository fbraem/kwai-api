import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Training from '@/models/trainings/Training';
import Presence from '@/models/trainings/Presence';

const state = {
  trainings: null,
  meta: null,
  error: null,
};

const getters = {
  training: (state) => (id) => {
    if (state.trainings) {
      return state.trainings.find((training) => training.id === id);
    }
    return null;
  }
};

const mutations = {
  trainings(state, { meta, data }) {
    state.trainings = data;
    state.meta = meta;
    state.error = null;
  },
  training(state, { data }) {
    if (state.trainings == null) {
      state.trainings = [];
    }
    var index = state.trainings.findIndex((e) => e.id === data.id);
    if (index !== -1) {
      Vue.set(state.trainings, index, data);
    } else {
      state.trainings.push(data);
    }
    state.error = null;
  },
  presences(state, { data }) {
    var index = state.trainings.findIndex((e) => e.id === data.id);
    if (index !== -1) {
      state.trainings.presences = data.presences;
    }
    state.error = null;
  },
  error(state, data) {
    state.error = data;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    const api = new JSONAPI({ source: Training });
    if (payload.year) {
      api.where('year', payload.year);
      if (payload.month) {
        api.where('month', payload.month);
      }
    }
    if (payload.coach) {
      api.where('coach', payload.coach);
    }
    dispatch('wait/start', 'training.browse', { root: true });
    try {
      commit('trainings', await api.get());
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'training.browse', { root: true });
    }
  },
  async save({ dispatch, commit }, training) {
    try {
      const api = new JSONAPI({ source: Training });
      const result = await api.save(training);
      commit('training', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  async read({ dispatch, getters, commit }, { id, cache = true }) {
    if (cache) {
      var training = getters['training'](id);
      if (training) { // already read
        commit('error', null); // Reset error
        return training;
      }
    }

    dispatch('wait/start', 'training.read', { root: true });
    try {
      const api = new JSONAPI({ source: Training });
      const result = await api.get(id);
      commit('training', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'training.read', { root: true });
      throw error;
    } finally {
      dispatch('wait/end', 'training.read', { root: true });
    }
  },
  async createAll({commit, state}, trainings) {
    try {
      const api = new JSONAPI({ source: Training });
      const result = await api.save(trainings);
      commit('training', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  async updatePresences({commit, state}, {training, presences}) {
    try {
      const api = new JSONAPI({ source: Presence });
      api.path(training.id);
      const result = await api.save(presences);
      commit('presences', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  }
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
