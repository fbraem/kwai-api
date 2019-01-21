import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Page from '@/models/Page';

const state = {
  pages: [],
  error: null,
  meta: null,
};

const getters = {
  page: (state) => (id) => {
    return state.pages.find((page) => page.id === id);
  },
};

const mutations = {
  /**
   * Set the pages of the store
   */
  pages(state, { meta, data }) {
    state.meta = meta;
    state.pages = data;
    state.error = null;
  },
  /**
   * Put a page into the store
   */
  page(state, { data }) {
    var index = state.pages.findIndex((p) => p.id === data.id);
    if (index !== -1) {
      Vue.set(state.pages, index, data);
    } else {
      state.pages.push(data);
    }
    state.error = null;
  },
  /**
   * Remove a page from the store
   */
  deletePage(state, page) {
    state.pages = state.pages.filter((p) => {
      return page.id !== p.id;
    });
    state.error = null;
  },
  /**
   * Set the error of the store
   */
  error(state, error) {
    state.error = error;
  },
};

const actions = {
  /**
   * Get all pages
   */
  async browse({ dispatch, commit }, payload) {
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
  },
  /**
   * Get a page. First look into the store before getting it from the server.
   */
  async read({ dispatch, getters, commit }, payload) {
    var page = getters['page'](payload.id);
    if (page) { // already read
      return page;
    }

    dispatch('wait/start', 'pages.read', { root: true });
    try {
      const api = new JSONAPI({ source: Page });
      var result = await api.get(payload.id);
      commit('page', result);
      return result.data;
    } catch (error) {
      commit('error', error);
    } finally {
      dispatch('wait/end', 'pages.read', { root: true });
    }
  },
  /**
   * Save the (new) page
   */
  async save({ commit }, page) {
    try {
      const api = new JSONAPI({ source: Page });
      var result = await api.save(page);
      commit('page', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  /**
   * Save the (new) content
   */
  async saveContent({ commit }, payload) {
    try {
      const api = new JSONAPI({ source: Page });
      var result = await api.attach(payload.page, payload.content);
      commit('page', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw (error);
    }
  },
  /**
   * Delete a page
   */
  async delete({ commit }, payload) {
    try {
      const api = new JSONAPI({ source: Page });
      await api.delete(payload.page);
      commit('deletePage', { id: payload.page.id });
    } catch (error) {
      commit('error', error);
      throw (error);
    }
  },
};

export default {
  namespaced: true,
  state: state,
  getters: getters,
  mutations: mutations,
  actions: actions,
  modules: {
  },
};
