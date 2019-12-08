import Vue from 'vue';

import { state as initialize } from './state';

/**
 * Set all seasons
 */
const seasons = (state, {meta, data}) => {
  state.error = null;
  state.all = data;
  state.meta = meta;
};

/**
 * Update the season in the list (if present), and sets as active
 */
const season = (state, { data }) => {
  state.error = null;
  if (state.all == null) {
    return;
  }
  var index = state.all.findIndex((s) => s.id === data.id);
  if (index !== -1) {
    Vue.set(state.all, index, data);
  }
  state.active = data;
};

/**
 * Set the error
 */
const error = (state, error) => {
  state.error = error;
};

/**
 * Set the active season
 */
const active = (state, season) => {
  state.active = season;
};

/**
 * Reset the state
 */
const reset = (state) => {
  Object.assign(state, initialize());
};

export const mutations = {
  seasons,
  season,
  active,
  error,
  reset
};
