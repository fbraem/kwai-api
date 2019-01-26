import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import TrainingDefinition from '@/models/trainings/Definition';

const state = {
  definitions: null,
  error: null,
  meta: null
};

const getters = {
  definition: (state) => (id) => {
    if (state.definitions) {
      return state.definitions.find((def) => def.id === id);
    }
    return null;
  },
};

const mutations = {
  definitions(state, { meta, data }) {
    state.definitions = data;
    state.meta = meta;
    state.error = null;
  },
  definition(state, { data }) {
    if (state.definitions == null) {
      state.definitions = [];
    }
    var index = state.definitions.findIndex((t) => t.id === data.id);
    if (index !== -1) {
      Vue.set(state.definitions, index, data);
    } else {
      state.definitions.push(data);
    }
    state.error = null;
  },
  error(state, data) {
    state.error = data;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'training.definitions.browse', { root: true });
    try {
      const api = new JSONAPI({ source: TrainingDefinition });
      commit('definitions', await api.get());
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'training.definitions.browse', { root: true });
    }
  },
  async save({ dispatch, commit }, definition) {
    try {
      const api = new JSONAPI({ source: TrainingDefinition });
      const result = await api.save(definition);
      commit('definition', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  async read({ dispatch, getters, commit }, payload) {
    var definition = getters['definition'](payload.id);
    if (definition) { // already read
      commit('error', null); // Reset error
      return definition;
    }

    dispatch('wait/start', 'training.definitions.read', { root: true });
    try {
      const api = new JSONAPI({ source: TrainingDefinition });
      const result = await api.get(payload.id);
      commit('definition', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'training.definitions.read', { root: true });
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
