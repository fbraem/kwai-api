import Vue from 'vue';

import { state as initialize } from './state';

const definitions = (state, { meta, data }) => {
  state.all = data;
  state.meta = meta;
  state.error = null;
};

const definition = (state, { data }) => {
  state.error = null;
  if (state.all) {
    var index = state.all.findIndex((t) => t.id === data.id);
    if (index !== -1) {
      Vue.set(state.all, index, data);
    }
  }
  state.active = data;
};

const error = (state, data) => {
  state.error = data;
};

const active = (state, data) => {
  state.error = null;
  state.data = active;
};

const reset = (state) => {
  Object.assign(state, initialize());
};

export const mutations = {
  definitions,
  definition,
  active,
  reset,
  error
};
