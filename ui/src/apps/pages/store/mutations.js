import Vue from 'vue';
import { state as initialize } from './state';

/**
 * Set the pages of the store
 */
function pages(state, { meta, data }) {
  state.meta = meta;
  state.all = data;
  state.error = null;
}

/**
 * Update a page in the list
 */
function page(state, page, insert = false) {
  state.error = null;
  const index = state.all.findIndex((p) => p.id === page.id);
  if (index !== -1) {
    Vue.set(state.all, index, page);
  } else {
    if (insert) {
      state.all.push(page);
    }
  }
  state.active = page;
}

/**
 * Remove a page from the store
 */
function remove(state, page) {
  state.error = null;
  state.all = state.all.filter((p) => {
    return page.id !== p.id;
  });
}

/**
 * Set the error of the store
 */
function error(state, error) {
  state.error = error;
}

/**
 * Set the active page
 */
function active(state, active) {
  state.active = active;
}

/**
 * Reset the state
 */
function reset(state) {
  Object.assign(state, initialize());
}

export const mutations = {
  pages,
  page,
  active,
  remove,
  error,
  reset
};
