import Vue from 'vue';

import { state as initialize } from './state';

function events(state, { meta, data }) {
  state.all = data;
  state.meta = meta;
  state.error = null;
};

function event(state, { data }) {
  if (state.all == null) {
    state.all = [];
  }
  var index = state.all.findIndex((e) => e.id === data.id);
  if (index !== -1) {
    Vue.set(state.all, index, data);
  } else {
    state.all.push(data);
  }
  state.error = null;
};

function error(state, data) {
  state.error = data;
};

function reset(state) {
  Object.assign(state, initialize());
}

/**
 * Mutations
 */
export const mutations = {
  events,
  event,
  error,
  reset
};
