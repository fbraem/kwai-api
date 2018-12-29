import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import moment from 'moment';

import TrainingEvent from '@/models/trainings/Event';

const state = {
  events: null,
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
  events(state, events) {
    state.events = events;
    state.error = null;
  },
  event(state, event) {
    if (state.events == null) {
      state.events = [];
    }
    var index = state.events.findIndex((e) => e.id === event.id);
    if (index !== -1) {
      Vue.set(state.events, index, event);
    } else {
      state.events.push(event);
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
    const model = new TrainingEvent();
    try {
      commit('events', await model.get());
      dispatch('wait/end', 'training.events.browse', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'training.events.browse', { root: true });
      throw error;
    }
  },
  async save({ dispatch, commit }, event) {
    var newEvent = null;
    try {
      newEvent = await event.save();
      commit('event', newEvent);
      return newEvent;
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
    let model = new TrainingEvent();
    try {
      event = await model.find(payload.id);
      commit('event', event);
      dispatch('wait/end', 'training.events.read', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'training.events.read', { root: true });
      throw error;
    }
    return event;
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
