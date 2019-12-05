import Vue from 'vue';

import { state as initialize } from './state';

/**
 * Mutate all types
 */
const types = (state, { meta, data }) => {
  state.all = data;
  state.meta = meta;
  state.error = null;
};

/**
 * Mutate a type in the list (if present) and update active type
 */
const type = (state, { data }) => {
  state.error = null;
  if (state.all == null) {
    state.all = [];
  }
  var index = state.all.findIndex((t) => t.id === data.id);
  if (index !== -1) {
    Vue.set(state.all, index, data);
  }
  state.active = data;
};

/**
 * Mutate an error
 */
const error = (state, data) => {
  state.error = data;
};

/**
 * Set the active type
 */
function active(state, active) {
  state.active = active;
}

/**
 * Reset the state
 */
const reset = (state) => {
  Object.assign(state, initialize());
};

export const mutations = {
  types,
  type,
  error,
  active,
  reset
};
