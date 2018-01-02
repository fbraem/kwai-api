import Vue from 'vue';

import OAuth from '@/js/oauth';
const oauth = new OAuth();
import axios from 'axios';

import Vuex from 'vuex';
Vue.use(Vuex);

import _ from 'lodash';
import URI from 'urijs';
import moment from 'moment';

import JSONAPI from '@/js/JSONAPI';

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
        return _.find(state.categories, ['id', id]);
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
  categories(state, data) {
      state.categories = data.categories;
  },
  addCategory(state, data) {
      state.categories.unshift(data.category);
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
    browse(context, payload) {
        return new Promise((resolve, reject) => {
            oauth.get('api/categories', {
            }).then((res) => {
                var api = new JSONAPI();
                var categories = api.parse(res.data);
                context.commit('categories', {
                    categories : categories.data
                });
                resolve();
            }).catch((error) => {
                reject();
            });
        });
    },
    create(context, payload) {
        context.commit('loading');
        return oauth.post('api/categories', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('addCategory', {
                category : result.data
            });
            context.commit('success');
            return result.data;
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    update(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            oauth.patch('api/categories/' + payload.data.id, {
                data : payload
            }).then((res) => {
                var api = new JSONAPI();
                var result = api.parse(res.data);
                context.commit('modifyCategory', {
                    category : result.data
                });
                context.commit('success');
                resolve();
            }).catch((error) => {
                context.commit('error', error);
                reject();
            });
        });
    },
    read(context, payload) {
        context.commit('loading');
        var category = context.getters['category'](payload.id);
        if (category) { // already read
            context.commit('success');
            return;
        }

        oauth.get('api/categories/' + payload.id, {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('addCategory', {
                category : result.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
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
