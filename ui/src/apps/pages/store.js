import Vue from 'vue';

import OAuth from '@/js/oauth';
const oauth = new OAuth();
import axios from 'axios';

import Vuex from 'vuex';
Vue.use(Vuex);

import find from 'lodash/find';
import filter from 'lodash/filter';
import unionBy from 'lodash/unionBy';

import URI from 'urijs';
import moment from 'moment';

import JSONAPI from '@/js/JSONAPI';

const state = {
    pages : [],
    status : {
        loading : false,
        success : false,
        error : false
    },
    archive : {}
};

const getters = {
    pages(state) {
        return state.pages;
    },
    page: (state) => (id) => {
        return find(state.pages, ['id', id]);
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
  pages(state, data) {
      state.pages = data.pages;
  },
  setPage(state, data) {
      state.pages = unionBy([data.page], state.page, 'id');
  },
  deletePage(state, data) {
      state.pages = filter(state.pages, (page) => {
         return page.id != data.id;
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
    browse(context, payload) {
        context.commit('loading');
        var uri = new URI('api/pages');
        var offset = payload.offset || 0;
        uri.addQuery('page[offset]', offset);
        if (payload.category) {
            uri.addQuery('filter[category]', payload.category);
        }

        oauth.get(uri.href(), {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var pages = api.parse(res.data);
            context.commit('pages', {
                pages : pages.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    read(context, payload) {
        context.commit('loading');
        var page = context.getters['page'](payload.id);
        if (page) { // already read
            context.commit('success');
            return;
        }

        oauth.get('api/pages/' + payload.id, {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('setPage', {
                page : result.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    create(context, payload) {
        context.commit('loading');
        return oauth.post('api/pages', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('setPage', {
                page : result.data
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
            oauth.patch('api/pages/' + payload.data.id, {
                data : payload
            }).then((res) => {
                var api = new JSONAPI();
                var result = api.parse(res.data);
                context.commit('setPage', {
                    page : result.data
                });
                context.commit('success');
                resolve();
            }).catch((error) => {
                context.commit('error', error);
                reject();
            });
        });
    },
    delete(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            oauth.delete('api/pages/' + payload.id)
            .then((res) => {
                context.commit('deletePage', { id : payload.id });
                context.commit('success');
                resolve();
            }).catch((error) => {
                context.commit('error', error);
                reject();
            });
        });
    },
    uploadImage(context, payload) {
        return oauth.post('/api/pages/image/' + payload.page.id, {
            data : payload.formData
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
