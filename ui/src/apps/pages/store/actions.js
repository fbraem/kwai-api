import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Page from '@/models/Page';

/**
 * Get all pages
 */
async function browse({ dispatch, commit }, payload) {
  dispatch('wait/start', 'pages.browse', { root: true });
  const api = new JSONAPI({ source: Page });
  if (payload.offset || payload.limit) {
    api.paginate(payload.offset, payload.limit);
  }
  if (payload.category) {
    api.where('category', payload.category);
  }
  if (payload.user) {
    api.where('user', payload.user);
  }
  let result = await api.get();
  commit('pages', result);
  dispatch('wait/end', 'pages.browse', { root: true });
}

/**
 * Get a page. First look into the store before getting it from the server.
 */
async function read({ dispatch, getters, commit, state }, payload) {
  // Don't read it again when it is active ...
  if (state.active?.id === payload.id) {
    return;
  }

  // Check the list
  var page = getters['page'](payload.id);
  if (page) { // already read
    commit('active', page);
    return;
  }

  dispatch('wait/start', 'pages.read', { root: true });
  try {
    const api = new JSONAPI({ source: Page });
    var result = await api.get(payload.id);
    commit('active', result.data);
  } catch (error) {
    commit('error', error);
  } finally {
    dispatch('wait/end', 'pages.read', { root: true });
  }
}

/**
 * Save the (new) page
 */
async function save({ commit }, page) {
  try {
    const api = new JSONAPI({ source: Page });
    var result = await api.save(page);
    commit('page', result);
    return result.data;
  } catch (error) {
    commit('error', error);
    throw error;
  }
}

/**
 * Delete a page
 */
async function remove({ commit }, payload) {
  try {
    const api = new JSONAPI({ source: Page });
    await api.delete(payload.page);
    commit('deletePage', { id: payload.page.id });
  } catch (error) {
    commit('error', error);
    throw (error);
  }
}

async function reset({ commit }) {
  commit('reset');
}

/**
 * When a page was read in another instance of this module, set can be
 * used to make it available in the current instance.
 */
function set({ commit }, page) {
  commit('active', page);
}

export const actions = {
  browse,
  read,
  save,
  remove,
  reset,
  set
};
