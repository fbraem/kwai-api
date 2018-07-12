import Vue from 'vue';

import OAuth from '@/js/oauth';
const oauth = new OAuth();

import Vuex from 'vuex';
Vue.use(Vuex);

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
  deleteStory(state, data) {
      state.stories = state.stories.filter((story) => {
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
    async browse({ state, getters, commit, context }, payload) {
        commit('loading');
        const story = new Story();
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
        commit('stories', stories);
        commit('success');
    },
    async read({ state, getters, commit, context }, payload) {
        commit('loading');
        var story = getters['story'](payload.id);
        if (story) { // already read
            commit('success');
            return story;
        }

        let model = new Story();
        try {
            story = await model.find(payload.id);
            console.log(story);
            commit('story', story);
            commit('success');
        } catch(error) {
            commit('error', error);
        }
        return story;
    },
    async save({ state, getters, commit, context }, story) {
        var newStory = null;
        try  {
            newStory = await story.save();
            commit('story', newStory);
            return newStory;
        } catch(error) {
            commit('error', error);
            throw error;
        }
    },
    async attachContent({ state, getters, commit, context }, payload) {
        try {
            var newStory = await payload.story.attach(payload.content);
            commit('story', newStory);
            return newStory;
        }
        catch(error)
        {
            commit('error', error);
            throw(error);
        }
    },
    async delete({ state, getters, commit, context }, payload) {
        commit('loading');
        try {
            await payload.story.delete();
            commit('deleteStory', { id : payload.story.id });
            commit('success');
        } catch(error) {
            commit('error', error);
            throw(error);
        }
    },
    async loadArchive({ state, getters, commit, context }, payload) {
        commit('loading');
        var response = await oauth.get('api/news/archive');
        commit('archive', response.data);
        commit('success');
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
