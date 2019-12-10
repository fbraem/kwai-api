import Vue from 'vue';

import { state as initialize } from './state';

const trainings = (state, { meta, data }) => {
  state.all = data;
  state.meta = meta;
  state.error = null;
};

const training = (state, { data }) => {
  if (state.all != null) {
    var index = state.all.findIndex((e) => e.id === data.id);
    if (index !== -1) {
      Vue.set(state.all, index, data);
    }
  }
  state.active = data;
  state.error = null;
};

const setPresences = (state, { data }) => {
  Vue.set(state.active, 'presences', data.presences);
};

const error = (state, data) => {
  state.error = data;
};

const active = (state, data) => {
  state.active = data;
};

const reset = (state) => {
  Object.assign(state, initialize());
};

export const mutations = {
  trainings,
  training,
  active,
  error,
  reset,
  setPresences
};
