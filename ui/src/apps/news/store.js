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
    stories : [],
    categories : [],
    status : {
        loading : false,
        success : false,
        error : false
    },
    archive : {}
};

const getters = {
    stories(state) {
        return state.stories;
    },
    story: (state) => (id) => {
        return _.find(state.stories, ['id', id]);
    },
    categories(state) {
        return state.categories;
    },
    category: (state) => (id) => {
        return _.find(state.categories, ['id', id]);
    },
    loading(state) {
        return state.status.loading;
    },
    archive(state) {
        return state.archive;
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
  addCategory(state, data) {
      state.categories.unshift(data.category);
  },
  archive(state, data) {
      state.archive = {};
      data.forEach((element) => {
          if (! state.archive[element.year]) {
              state.archive[element.year] = [];
          }
          state.archive[element.year].push({
              monthName : moment.months()[element.month - 1],
              month : element.month,
              year : element.year,
              count : element.count
          });
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

        var uri = new URI('api/news/stories');
        var offset = payload.offset || 0;
        uri.addQuery('page[offset]', offset);
        if (payload.category) {
            uri.addQuery('filter[category]', payload.category);
        }
        if (payload.year) {
            uri.addQuery('filter[year]', payload.year);
        }
        if (payload.month) {
            uri.addQuery('filter[month]', payload.month);
        }

        oauth.get(uri.href(), {
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

        oauth.get('api/news/stories/' + payload.id, {
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
        return oauth.post('api/news/stories', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('addStory', {
                story : result.data
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
            oauth.patch('api/news/stories/' + payload.data.id, {
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
    createCategory(context, payload) {
        context.commit('loading');
        return oauth.post('api/news/categories', {
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
    },
    updateCategory(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            oauth.patch('api/news/categories/' + payload.data.id, {
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
    readCategory(context, payload) {
        context.commit('loading');
        var category = context.getters['category'](payload.id);
        if (category) { // already read
            context.commit('success');
            return;
        }

        oauth.get('api/news/categories/' + payload.id, {
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
    },
    getCategories(context, payload) {
        return new Promise((resolve, reject) => {
            oauth.get('api/news/categories', {
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
    uploadImage(context, payload) {
        //return axios.post('/api/news/image/' + payload.story.id, payload.formData);
        return oauth.post('/api/news/image/' + payload.story.id, {
            data : payload.formData
        });
    },
    embeddImage(context, payload) {
        return oauth.post('/api/news/embedded_image/' + payload.story.id, {
            data : payload.formData
        });
    },
    loadArchive(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            oauth.get('api/news/archive', {
            }).then((res) => {
                context.commit('success');
                context.commit('archive', res.data);
            }).catch((error) => {
                context.commit('error', error);
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
