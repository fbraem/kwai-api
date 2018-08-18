import Vue from 'vue';

import OAuth from '@/js/oauth';
const oauth = new OAuth();

import Vuex from 'vuex';
Vue.use(Vuex);

import Category from './models/Category';

const state = {
    categories : [],
    status : {
        loading : false,
        success : false,
        error : false
    }
};

const getters = {
    categories(state) {
        return state.categories;
    },
    category: (state) => (id) => {
        return state.categories.find((category) => category.id == id);
    },
    loading(state) {
        return state.status.loading;
    },
    success(state) {
        return state.status.success;
    },
    error(state) {
        return state.status.error;
    }
};

const mutations = {
  categories(state, categories) {
    state.categories = categories;
  },
  category(state, category) {
    var index = state.categories.findIndex((c) => c.id == category.id);
    if (index != -1) {
        Vue.set(state.categories, index, category);
    } else {
        state.categories.push(category);
    }
  },
  deleteCategory(state, category) {
    state.categories = state.categories.filter((c) => {
           return category.id != c.id;
    });
  },
  loading(state) {
      state.status = {
          loading : true,
          success: false,
          error : false
      };
  },
  success(state) {
      state.status = {
          loading : false,
          success: true,
          error : false
      };
  },
  error(state, payload) {
      state.status = {
          loading : false,
          success: false,
          error : payload
      };
  }
};

const actions = {
    async browse({ state, getters, commit, context }, payload) {
        commit('loading');
        const category = new Category();
        let categories = await category.get();
        commit('categories', categories);
        commit('success');
    },
    async read({ state, getters, commit, context }, payload) {
        commit('loading');
        var category = getters['category'](payload.id);
        if (category) { // already read
            commit('success');
            return category;
        }

        let model = new Category();
        try {
            category = await model.find(payload.id);
            commit('category', category);
            commit('success');
        } catch(error) {
            console.log(error);
            commit('error', error);
        }
        return category;
    },
    async save({ state, getters, commit, context }, category) {
        var newCategory = null;
        try  {
            newCategory = await category.save();
            commit('category', newCategory);
            return newCategory;
        } catch(error) {
            commit('error', error);
            throw error;
        }
    }
};

export default {
    namespaced : true,
    state : state,
    getters : getters,
    mutations : mutations,
    actions : actions,
    modules: {
    }
};
