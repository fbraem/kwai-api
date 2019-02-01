import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import moment from 'moment';

import JSONAPI from '@/js/JSONAPI';
import Training from '@/models/trainings/Training';

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
  async read({ dispatch, getters, commit }, payload) {
    var training = getters['training'](payload.id);
    if (training) { // already read
      commit('error', null); // Reset error
      return training;
    }

    dispatch('wait/start', 'training.read', { root: true });
    try {
      const api = new JSONAPI({ source: Training });
      const result = await api.get(payload.id);
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
  generate({commit}, payload) {
    var tz = moment.tz.guess();
    var start = payload.start;
    var end = payload.end;
    var next = start.day(payload.definition.weekday + 7);
    var trainings = [];
    while (next.isBefore(end)) {
      var training = new Training();
      training.name = payload.definition.name;
      training.start_date = next.clone();
      var s = training.start_date.clone();
      s.hours(payload.definition.start_time.hours());
      s.minutes(payload.definition.start_time.minutes());
      training.start_time = s;

      var e = training.start_date.clone();
      e.hours(payload.definition.end_time.hours());
      e.minutes(payload.definition.end_time.minutes());
      training.end_time = e;

      training.location = payload.definition.location;
      training.time_zone = tz;
      training.disabled = false;
      training.definition = payload.definition;
      training.coaches = payload.coaches;
      training.push(training);
      next = next.day(payload.definition.weekday + 7);
    }
    commit('trainings', trainings);
  },
  createAll({commit, state}) {
    var trainings = state.trainings.filter((e) => {
      return !e.disabled;
    });
    var training = new Training();
    training.createAll(trainings);
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
