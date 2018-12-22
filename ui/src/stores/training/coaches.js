import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import TrainingCoach from '@/models/trainings/Coach';

const state = {
  coaches: null,
  error: null,
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
  coaches(state, coaches) {
    state.coaches = coaches;
    state.error = null;
  },
  coach(state, coach) {
    if (state.coaches == null) {
      state.coaches = [];
    }
    var index = state.coaches.findIndex((c) => c.id === coach.id);
    if (index !== -1) {
      Vue.set(state.coaches, index, coach);
    } else {
      state.coaches.push(coach);
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
    const model = new TrainingCoach();
    try {
      commit('coaches', await model.get());
      dispatch('wait/end', 'training.coaches.browse', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'training.coaches.browse', { root: true });
      throw error;
    }
  },
  async save({ dispatch, commit }, coach) {
    var newCoach = null;
    try {
      newCoach = await coach.save();
      commit('coach', newCoach);
      return newCoach;
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
    let model = new TrainingCoach();
    try {
      coach = await model.find(payload.id);
      commit('coach', coach);
      dispatch('wait/end', 'training.coaches.read', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'training.coaches.read', { root: true });
      throw error;
    }
    return coach;
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
