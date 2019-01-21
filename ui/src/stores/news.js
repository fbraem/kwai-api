import Vue from 'vue';
import config from 'config';

import OAuth from '@/js/oauth';
const oauth = new OAuth();
import JSONAPI from '@/js/JSONAPI';

import Vuex from 'vuex';
Vue.use(Vuex);

import moment from 'moment';

import Story from '@/models/Story';

const state = {
  stories: null,
  error: null,
  meta: null,
  archive: {},
};

const getters = {
  story: (state) => (id) => {
    if (state.stories == null) return null;
    return state.stories.find((story) => story.id === id);
  }
};

const mutations = {
  clear(state) {
    state.meta = null;
    state.stories = null;
    state.error = null;
  },
  stories(state, { meta, data }) {
    state.meta = meta;
    state.stories = data;
    state.error = null;
  },
  story(state, { data }) {
    if (state.stories == null) {
      state.stories = [];
    }
    var index = state.stories.findIndex((s) => s.id === data.id);
    if (index !== -1) {
      Vue.set(state.stories, index, data);
    } else {
      state.stories.push(data);
    }
    state.error = null;
  },
  deleteStory(state, story) {
    state.stories = state.stories.filter((s) => {
      return s.id !== story.id;
    });
  },
  archive(state, data) {
    state.archive = {};
    data.forEach((element) => {
      if (!state.archive[element.year]) {
        state.archive[element.year] = [];
      }
      state.archive[element.year].push({
        monthName: moment.months()[element.month - 1],
        month: element.month,
        year: element.year,
        count: element.count,
      });
    });
    state.error = null;
  },
  error(state, error) {
    state.error = error;
  },
};

const actions = {
  /**
   * Get all news stories
   */
  async browse({ dispatch, commit }, payload) {
    dispatch('wait/start', 'news.browse', { root: true });
    commit('clear');
    var api = new JSONAPI({ source: Story });
    payload = payload || {};
    if (payload.category) {
      api.where('category', payload.category);
    }
    if (payload.year) {
      api.where('year', payload.year);
    }
    if (payload.month) {
      api.where('month', payload.month);
    }
    if (payload.featured) {
      api.where('featured', true);
    }
    if (payload.user) {
      api.where('user', payload.user);
    }
    if (payload.offset || payload.limit) {
      api.paginate(payload);
    }
    let result = await api.get();
    commit('stories', result);
    dispatch('wait/end', 'news.browse', { root: true });
  },

  /**
   * Read a new story.
   */
  async read({ getters, dispatch, commit }, payload) {
    var story = getters['story'](payload.id);
    if (story) { // already read
      return story;
    }

    dispatch('wait/start', 'news.read', { root: true });
    var api = new JSONAPI({ source: Story });
    try {
      var result = await api.get(payload.id);
      commit('story', result);
      return result.data;
    } catch (error) {
      commit('error', error);
    } finally {
      dispatch('wait/end', 'news.read', { root: true });
    }
  },

  /**
   * Save the (new) story
   */
  async save({ commit }, story) {
    try {
      var api = new JSONAPI({ source: Story });
      var result = await api.save(story);
      commit('story', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw error;
    }
  },

  /**
   * Save the (new) content
   */
  async saveContent({ commit }, payload) {
    try {
      var api = new JSONAPI({ source: Story });
      var result = await api.attach(payload.story, payload.content);
      commit('story', result);
      return result.data;
    } catch (error) {
      commit('error', error);
      throw (error);
    }
  },

  /**
   * Delete the story
   */
  async delete({ commit }, payload) {
    try {
      var api = new JSONAPI({ source: Story });
      await api.delete(payload.story);
      commit('deleteStory', { id: payload.story.id });
    } catch (error) {
      commit('error', error);
      throw (error);
    }
  },
  async loadArchive({ dispatch, commit }, payload) {
    dispatch('wait/start', 'news.browse', { root: true });
    var response = await oauth.get(config.api + '/news/archive');
    commit('archive', response.data);
    dispatch('wait/end', 'news.browse', { root: true });
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
