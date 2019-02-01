import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Event from '@/models/Event';

const state = {
  trainings: null,
  meta: null,
  error: null,
};

const getters = {
  training: (state) => (id) => {
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
    const api = new JSONAPI({ source: Event });
    if (payload.year) {
      api.where('year', payload.year);
      if (payload.month) {
        api.where('month', payload.month);
      }
    }
    dispatch('wait/start', 'event.browse', { root: true });
    try {
      commit('events', await api.get());
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'event.browse', { root: true });
    }
  },
  async save({ dispatch, commit }, event) {
    try {
      const api = new JSONAPI({ source: Event });
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

    dispatch('wait/start', 'event.read', { root: true });
    try {
      const api = new JSONAPI({ source: Event });
      const result = await api.get(payload.id);
      commit('event', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'event.read', { root: true });
      throw error;
    } finally {
      dispatch('wait/end', 'event.read', { root: true });
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
