import Vue from 'vue';

import OAuth from '@/js/oauth';
const oauth = new OAuth();
import axios from 'axios';

import Vuex from 'vuex';
Vue.use(Vuex);

import filter from 'lodash/filter';

import URI from 'urijs';
import moment from 'moment';

import Story from './models/Story';

const state = {
    stories : [],
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
        return state.stories.find((story) => story.id == id);
    },
    loading(state) {
        return state.status.loading;
    },
    success(state) {
        return state.status.success;
    },
    error(state) {
        return state.status.error;
    },
    archive(state) {
        return state.archive;
    }
};

const mutations = {
  stories(state, stories) {
      state.stories = stories;
  },
  story(state, story) {
      var index = state.stories.findIndex((s) => s.id == story.id);
      if (index != -1) {
          Vue.set(state.stories, index, story);
      } else {
          state.stories.push(story);
      }
  },
  addStory(state, story) {
      state.stories.push(story);
  },
  deleteStory(state, data) {
      state.stories = filter(state.stories, (story) => {
         return story.id != data.id;
      });
  },
  attachContent(state, data) {
      var index = state.stories.findIndex((s) => s.id == data.story.id);
      if (index != -1) {
          if (state.stories[index].contents == null) {
              state.stories[index].contents = [];
          }
          state.stories[index].contents.push(data.content);
      }
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
    async browse(context, payload) {
        context.commit('loading');
        const story = new Story();
        const fetchStories = async () => {
            if (payload.category) {
                story.where('category', payload.category);
            }
            if (payload.year) {
                story.where('year', payload.year);
            }
            if (payload.month) {
                story.where('month', payload.month);
            }
            if (payload.featured) {
                story.where('featured', true);
            }
            if (payload.user) {
                story.where('user', payload.user);
            }
            let stories = await story.get();
            context.commit('stories', stories);
        };
        story.call(fetchStories);
        context.commit('success');
    },
    async read(context, payload) {
        context.commit('loading');
        var story = context.getters['story'](payload.id);
        if (story) { // already read
            context.commit('success');
            return;
        }

        let model = new Story();
        const fetchStory = async () => {
            var story = await model.find(payload.id);
            context.commit('story', story);
        }
        model.call(fetchStory);
        context.commit('success');
    },
    async save(context, story) {
        var newStory = null;
        const create = async () => {
            newStory = await story.save();
            context.commit('story', newStory);
        }
        await story.call(create)
            .catch((error) => {
                context.commit('error', error);
                throw(error);
            });
        return newStory;
    },
    async attachContent(context, payload) {
        var newStory = null;
        const create = async () => {
            newStory = await payload.story.attach(payload.content);
            context.commit('story', newStory);
        }
        await payload.story.call(create)
            .catch((error) => {
                context.commit('error', error);
                throw(error);
            });
        return newStory;
    },
    delete(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            oauth.delete('api/news/stories/' + payload.id)
            .then((res) => {
                context.commit('deleteStory', { id : payload.id });
                context.commit('success');
                resolve();
            }).catch((error) => {
                context.commit('error', error);
                reject();
            });
        });
    },
    async loadArchive(context, payload) {
        context.commit('loading');
        var response = await oauth.get('api/news/archive');
        context.commit('archive', response.data);
        context.commit('success');
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
