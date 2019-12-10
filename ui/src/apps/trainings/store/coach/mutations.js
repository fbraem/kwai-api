import Vue from 'vue';

import { state as initialize } from './state';

const coaches = (state, { meta, data }) => {
  state.all = data;
  state.meta = meta;
  state.error = null;
};

const coach = (state, { data }) => {
  state.error = null;
  if (state.all) {
    var index = state.all.findIndex((c) => c.id === data.id);
    if (index !== -1) {
      Vue.set(state.all, index, data);
    }
  }
  state.active = data;
};

const error = (state, data) => {
  state.error = data;
};

const active = (state, coach) => {
  state.active = coach;
};

const reset = (state) => {
  Object.assign(state, initialize());
};

export const mutations = {
  coaches,
  coach,
  active,
  error,
  reset
};
