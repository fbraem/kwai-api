import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import moment from 'moment';

import JSONAPI from '@/js/JSONAPI';
import TrainingEvent from '@/models/trainings/Event';

const state = {
  events: null,
  meta: null,
  error: null,
};

const getters = {
  event: (state) => (id) => {
    if (state.events) {
      return state.events.find((event) => event.id === id);
    }
    return null;
  }
};

const mutations = {
  events(state, { meta, data }) {
    state.events = data;
    state.meta = meta;
    state.error = null;
  },
  event(state, { data }) {
    if (state.events == null) {
      state.events = [];
    }
    var index = state.events.findIndex((e) => e.id === data.id);
    if (index !== -1) {
      Vue.set(state.events, index, data);
    } else {
      state.events.push(data);
    }
    state.error = null;
  },
  error(state, data) {
    state.error = data;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'training.events.browse', { root: true });
    try {
      const api = new JSONAPI({ source: TrainingEvent });
      commit('events', await api.get());
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'training.events.browse', { root: true });
    }
  },
  async save({ dispatch, commit }, event) {
    try {
      const api = new JSONAPI({ source: TrainingEvent });
      const result = await api.save(event);
      commit('event', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  async read({ dispatch, getters, commit }, payload) {
    var event = getters['event'](payload.id);
    if (event) { // already read
      commit('error', null); // Reset error
      return event;
    }

    dispatch('wait/start', 'training.events.read', { root: true });
    try {
      const api = new JSONAPI({ source: TrainingEvent });
      const result = await api.get(payload.id);
      commit('event', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'training.events.read', { root: true });
      throw error;
    } finally {
      dispatch('wait/end', 'training.events.read', { root: true });
    }
  },
  generate({commit}, payload) {
    var tz = moment.tz.guess();
    var start = payload.start;
    var end = payload.end;
    var next = start.day(payload.definition.weekday + 7);
    var events = [];
    while (next.isBefore(end)) {
      var event = new TrainingEvent();
      event.name = payload.definition.name;
      event.start_date = next.clone();
      var s = event.start_date.clone();
      s.hours(payload.definition.start_time.hours());
      s.minutes(payload.definition.start_time.minutes());
      event.start_time = s;

      var e = event.start_date.clone();
      e.hours(payload.definition.end_time.hours());
      e.minutes(payload.definition.end_time.minutes());
      event.end_time = e;

      event.location = payload.definition.location;
      event.time_zone = tz;
      event.disabled = false;
      event.definition = payload.definition;
      event.coaches = payload.coaches;
      events.push(event);
      next = next.day(payload.definition.weekday + 7);
    }
    commit('events', events);
  },
  createAll({commit, state}) {
    var events = state.events.filter((e) => {
      return !e.disabled;
    });
    var event = new TrainingEvent();
    event.createAll(events);
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
