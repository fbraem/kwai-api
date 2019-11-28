import Vue from 'vue';
import { state as initialize } from './state';

/**
 * Mutate members
 */
function members(state, { meta, data }) {
  state.all = data;
  state.meta = meta;
  state.error = null;
}

/**
 * Mutate a member
 */
function member(state, { data }) {
  state.error = null;
  if (state.all == null) {
    return;
  }
  var index = state.all.findIndex((m) => m.id === data.id);
  if (index !== -1) {
    Vue.set(state.all, index, data);
  }
}

/**
 * Set a member as active member
 */
function active(state, data) {
  state.active = data;
}

/**
 * Reset the state
 */
function reset(state) {
  Object.assign(state, initialize());
};

/**
 * Mutate the error
 */
function error(state, error) {
  state.error = error;
};

export const mutations = {
  members,
  member,
  active,
  reset,
  error
};
