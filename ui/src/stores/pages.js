import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Page from '@/models/Page';

const state = {
  pages: [],
  error: null,
  meta: null,
};

const getters = {
  pages(state) {
    return state.pages;
  },
  page: (state) => (id) => {
    return state.pages.find((page) => page.id === id);
  },
  error(state) {
    return state.error;
  },
  meta(state) {
    return state.meta;
  },
};

const mutations = {
  pages(state, pages) {
    state.meta = pages.meta();
    state.pages = pages;
    state.error = null;
  },
  page(state, page) {
    var index = state.pages.findIndex((p) => p.id === page.id);
    if (index !== -1) {
      Vue.set(state.pages, index, page);
    } else {
      state.pages.push(page);
    }
    state.error = null;
  },
  deletePage(state, page) {
    state.pages = state.pages.filter((p) => {
      return page.id !== p.id;
    });
    state.error = null;
  },
  attachContent(state, data) {
    var index = state.pages.findIndex((p) => p.id === data.page.id);
    if (index !== -1) {
      if (state.pages[index].contents == null) {
        state.pages[index].contents = [];
      }
      state.pages[index].contents.push(data.content);
    }
    state.error = null;
  },
  error(state, payload) {
    state.error = payload;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'pages.browse', { root: true });
    const page = new Page();
    if (payload.offset || payload.limit) {
      page.paginate(payload.offset, payload.limit);
    }
    if (payload.category) {
      page.where('category', payload.category);
    }
    if (payload.user) {
      page.where('user', payload.user);
    }
    let pages = await page.get();
    commit('pages', pages);
    dispatch('wait/end', 'pages.browse', { root: true });
  },
  async read({ dispatch, getters, commit }, payload) {
    var page = getters['page'](payload.id);
    if (page) { // already read
      return page;
    }

    dispatch('wait/start', 'pages.read', { root: true });
    let model = new Page();
    try {
      page = await model.find(payload.id);
      commit('page', page);
      dispatch('wait/end', 'pages.read', { root: true });
    } catch (error) {
      commit('error', error);
      dispatch('wait/end', 'pages.read', { root: true });
    }
    return page;
  },
  async save({ state, getters, commit, context }, page) {
    try {
      var newPage = await page.save();
      commit('page', newPage);
      return newPage;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },
  async attachContent({ state, getters, commit, context }, payload) {
    try {
      var newPage = await payload.page.attach(payload.content);
      commit('page', newPage);
      return newPage;
    } catch (error) {
      commit('error', error);
      throw (error);
    }
  },
  async delete({ state, getters, commit, context }, payload) {
    try {
      await payload.page.delete();
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
