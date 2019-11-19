import { state as initialize } from './state';

function reset(state) {
  Object.assign(state, initialize());
};

export const mutations = {
  reset
};
