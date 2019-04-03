import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Category from '@/models/Category';

const state = {
  meta: null,
  categories: null,
  error: null
};

const getters = {
  category: (state) => (id) => {
    if (state.categories) {
      return state.categories.find((category) => category.id === id);
    }
    return null;
  },
  categoryWithSlug: (state) => (slug) => {
    if (state.categories) {
      return state.categories.find((category) => category.slug === slug);
    }
    return null;
  },
  categoriesAsOptions(state) {
    var categories = state.categories;
    if (categories) {
      categories = categories.map((category) => ({
        value: category.id,
        text: category.name }
      ));
    } else {
      categories = [];
    }
    return categories;
  },
};

const mutations = {
  categories(state, { meta, data }) {
    state.meta = meta;
    state.categories = data;
    state.error = null;
  },
  category(state, { data }) {
    if (!state.categories) state.categories = [];
    var index = state.categories.findIndex((c) => c.id === data.id);
    if (index !== -1) {
      Vue.set(state.categories, index, data);
    } else {
      state.categories.push(data);
    }
    state.error = null;
  },
  deleteCategory(state, category) {
    state.categories = state.categories.filter((c) => {
      return category.id !== c.id;
    });
    state.error = null;
  },
  error(state, error) {
    state.error = error;
  },
};

const actions = {
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'categories.browse', { root: true });
    try {
      var api = new JSONAPI({ source: Category });
      if (payload) {
        if (payload.slug) {
          api.where('slug', payload.slug);
        }
      }
      commit('categories', await api.get());
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'categories.browse', { root: true });
    }
  },
  async read({ dispatch, getters, commit }, payload) {
    dispatch('wait/start', 'categories.read', { root: true });
    var category = getters['category'](payload.id);
    if (category) { // already read
      return category;
    }

    dispatch('wait/start', 'categories.read', { root: true });
    try {
      var api = new JSONAPI({ source: Category });
      var result = await api.get(payload.id);
      commit('category', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    } finally {
      dispatch('wait/end', 'categories.read', { root: true });
    }
  },
  async save({ commit }, category) {
    try {
      var api = new JSONAPI({ source: Category });
      var result = await api.save(category);
      commit('category', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
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
