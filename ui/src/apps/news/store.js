import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import _ from 'lodash';

import client from '@/js/client';
import JSONAPI from '@/js/JSONAPI';

const state = {
    stories : [],
    categories : [],
    status : {
        loading : false,
        success : false,
        error : false
    }
};

const getters = {
    stories(state) {
        return state.stories;
    },
    story: (state) => (id) => {
        return _.find(state.stories, ['id', id]);
    },
    loading(state) {
        return state.status.loading;
    }
};

const mutations = {
  stories(state, data) {
      state.stories = data.stories;
  },
  modifyStory(state, data) {
      state.stories = _.unionBy([data.story], state.stories, 'id');
  },
  addStory(state, data) {
      state.stories.unshift(data.story);
  },
  categories(state, data) {
      state.categories = data.categories;
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
        client().withoutAuth().get('api/news/stories', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var stories = api.parse(res.data);
            context.commit('stories', {
                stories : stories.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    read(context, payload) {
        context.commit('loading');
        var story = context.getters['story'](payload.id);
        if (story) { // already read
            context.commit('success');
            return;
        }

        client().withoutAuth().get('api/news/stories/' + payload.id, {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('addStory', {
                story : result.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    create(context, payload) {
        context.commit('loading');
        return client().withAuth().post('api/news/stories', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('addStory', {
                story : result.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    update(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            client().withAuth().patch('api/news/stories/' + payload.data.id, {
                data : payload
            }).then((res) => {
                var api = new JSONAPI();
                var result = api.parse(res.data);
                context.commit('modifyStory', {
                    story : result.data
                });
                context.commit('success');
                resolve();
            }).catch((error) => {
                context.commit('error', error);
                reject();
            });
        });
    },
    getCategories(context, payload) {
        return new Promise((resolve, reject) => {
            client().withoutAuth().get('api/news/categories', {
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
