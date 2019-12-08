import Vue from 'vue';

import { state as initialize } from './state';

const teams = (state, { meta, data }) => {
  state.all = data;
  state.error = null;
  state.meta = meta;
};

const team = (state, { data }) => {
  state.error = null;
  if (state.all) {
    var index = state.all.findIndex((t) => t.id === data.id);
    if (index !== -1) {
      Vue.set(state.all, index, data);
    }
  }
  state.active = data;
};

const setMembers = (state, data) => {
  Vue.set(state.active, 'members', data.members);
};

const availableMembers = (state, { data }) => {
  state.availableMembers = data;
};

const clearAvailableMembers = (state) => {
  state.availableMembers = [];
};

const error = (state, data) => {
  state.error = data;
};

const active = (state, team) => {
  state.active = team;
};

const reset = (state) => {
  Object.assign(state, initialize());
};

export const mutations = {
  teams,
  team,
  setMembers,
  error,
  reset,
  active,
  availableMembers,
  clearAvailableMembers
};
