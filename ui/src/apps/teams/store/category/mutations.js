import Vue from 'vue';

import { state as initialize } from './state';

/**
 * Mutate all team categories
 */
const team_categories = (state, { meta, data }) => {
  state.all = data;
  state.meta = meta;
  state.error = null;
};

/**
 * Mutate a category in the list (if present) and update active type
 */
const team_category = (state, { data }) => {
  state.error = null;
  if (state.all == null) {
    state.all = [];
  }
  var index = state.all.findIndex((c) => c.id === data.id);
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
  team_categories,
  team_category,
  error,
  active,
  reset
};
