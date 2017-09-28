import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import client from '@/js/client';
import JSONAPI from '@/js/JSONAPI';

const state = {
    stories : {},
    categories : []
};

const getters = {
    story: (state) => (id) => {
        return state.stories[id];
    }
};

const mutations = {
  stories(state, data) {
      data.stories.map((story) => {
          Vue.set(state.stories, story.id, story);
      });
  },
  story(state, data) {
      Vue.set(state.stories, data.id, data);
  },
  addStory(state, story) {
      Vue.set(state.stories, story.id, story);
  },
  categories(state, data) {
      state.categories = data.categories;
  }
};

const actions = {
    browse(context, payload) {
        return client().withoutAuth().get('api/news/stories', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var stories = api.parse(res.data);
            context.commit('stories', {
                stories : stories.data
            });
        });
    },
    read(context, payload) {
        return client().withoutAuth().get('api/news/stories/' + payload.id, {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var story = api.parse(res.data);
            context.commit('story', story.data);
        })
    },
    create(context, payload) {
        return client().withAuth().post('api/news/stories', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var story = api.parse(res.data);
            context.commit('addStory', {
                story : story
            });
        });
    },
    update(context, payload) {
        return client().withAuth().patch('api/news/stories/' + payload.data.id, {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var story = api.parse(res.data);
            context.commit('story', {
                story : story.data
            });
        });
    },
    getCategories(context, payload) {
        return client().withoutAuth().get('api/news/categories', {
        }).then((res) => {
            var api = new JSONAPI();
            var categories = api.parse(res.data);
            context.commit('categories', {
                categories : categories.data
            })
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
