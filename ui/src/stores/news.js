import Vue from 'vue';
import config from 'config';

import OAuth from '@/js/oauth';
const oauth = new OAuth();

import Vuex from 'vuex';
Vue.use(Vuex);

import moment from 'moment';

import Story from '@/models/Story';

const state = {
    stories : [],
    error : null,
    meta : null,
    archive : {}
};

const getters = {
    stories(state) {
        return state.stories;
    },
    story: (state) => (id) => {
        return state.stories.find((story) => story.id == id);
    },
    error(state) {
        return state.error;
    },
    archive(state) {
        return state.archive;
    },
    meta(state) {
        return state.meta;
    }
};

const mutations = {
  clear(state) {
     state.meta = null;
     state.stories = [];
     state.error = null;
  },
  stories(state, stories) {
      state.meta = stories.meta();
      state.stories = stories;
      state.error = null;
  },
  story(state, story) {
      var index = state.stories.findIndex((s) => s.id == story.id);
      if (index != -1) {
          Vue.set(state.stories, index, story);
      } else {
          state.stories.push(story);
      }
      state.error = null;
  },
  deleteStory(state, story) {
      state.stories = state.stories.filter((s) => {
         return s.id != story.id;
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
      state.error = null;
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
      state.error = null;
  },
  error(state, error) {
      state.error = error;
  }
};

const actions = {
    async browse({ dispatch, commit }, payload) {
        dispatch('wait/start', 'news.browse', { root : true });
        commit('clear');
        payload = payload || {};
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
        if (payload.offset || payload.limit) {
            story.paginate(payload.offset, payload.limit);
        }
        let stories = await story.get();
        commit('stories', stories);
        dispatch('wait/end', 'news.browse', { root : true });
    },
    async read({ getters, dispatch, commit }, payload) {
        var story = getters['story'](payload.id);
        if (story) { // already read
            return story;
        }

        dispatch('wait/start', 'news.read', { root : true });
        let model = new Story();
        try {
            story = await model.find(payload.id);
            commit('story', story);
            dispatch('wait/end', 'news.read', { root : true });
        } catch(error) {
            dispatch('wait/end', 'news.read', { root : true });
            commit('error', error);
        }
        return story;
    },
    async save({ commit }, story) {
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
    async attachContent({ commit }, payload) {
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
    async delete({ commit }, payload) {
        try {
            await payload.story.delete();
            commit('deleteStory', { id : payload.story.id });
        } catch(error) {
            commit('error', error);
            throw(error);
        }
    },
    async loadArchive({ dispatch, commit }, payload) {
        dispatch('wait/start', 'news.browse', { root : true });
        var response = await oauth.get(config.api + '/news/archive');
        commit('archive', response.data);
        dispatch('wait/end', 'news.browse', { root : true });
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
