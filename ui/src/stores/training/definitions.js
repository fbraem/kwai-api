import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import TrainingDefinition from '@/models/trainings/Definition';

const state = {
  definitions: null,
  error: null,
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
  definitions(state, definitions) {
    state.definitions = definitions;
    state.error = null;
  },
  definition(state, definition) {
    if (state.definitions == null) {
      state.definitions = [];
    }
    var index = state.definitions.findIndex((t) => t.id === definition.id);
    if (index !== -1) {
      Vue.set(state.definitions, index, definition);
    } else {
      state.definitions.push(definition);
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
    const model = new TrainingDefinition();
    try {
      commit('definitions', await model.get());
      dispatch('wait/end', 'training.definitions.browse', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'training.definitions.browse', { root: true });
      throw error;
    }
  },
  async save({ dispatch, commit }, definition) {
    var newDefinition = null;
    try {
      newDefinition = await definition.save();
      commit('definition', newDefinition);
      return newDefinition;
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
    let model = new TrainingDefinition();
    try {
      definition = await model.find(payload.id);
      commit('definition', definition);
      dispatch('wait/end', 'training.definitions.read', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'training.definitions.read', { root: true });
      throw error;
    }
    return definition;
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
