import { http_api } from '@/js/http';
import JSONAPI from '@/js/JSONAPI';

import Story from '@/models/Story';

/**
 * Get all news stories
 */
async function browse({ dispatch, commit }, payload) {
  dispatch('wait/start', 'news.browse', { root: true });
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
}

/**
 * Read a new story.
 */
async function read({ getters, dispatch, commit, state }, payload) {
  // Don't read it again when it is active ...
  if (state.active?.id === payload.id) {
    return;
  }

  var story = getters['story'](payload.id);
  if (story) { // already read
    commit('active', story);
    return;
  }

  dispatch('wait/start', 'news.read', { root: true });
  var api = new JSONAPI({ source: Story });
  try {
    var result = await api.get(payload.id);
    commit('active', result.data);
    return result.data;
  } catch (error) {
    commit('error', error);
  } finally {
    dispatch('wait/end', 'news.read', { root: true });
  }
}

/**
 * Save the (new) story
 */
async function save({ commit }, story) {
  try {
    var api = new JSONAPI({ source: Story });
    var result = await api.save(story);
    commit('story', result);
    console.log(result);
    return result.data;
  } catch (error) {
    commit('error', error);
    throw error;
  }
}

/**
 * Delete the story
 */
async function remove({ commit }, payload) {
  try {
    var api = new JSONAPI({ source: Story });
    await api.delete(payload.story);
    commit('deleteStory', { id: payload.story.id });
  } catch (error) {
    commit('error', error);
    throw (error);
  }
}

/**
 * Load archive
 */
async function loadArchive({ dispatch, commit }, payload) {
  dispatch('wait/start', 'news.browse', { root: true });
  const json = await http_api.url('news/archive').get().json();
  commit('archive', json);
  dispatch('wait/end', 'news.browse', { root: true });
}

/**
 * When a story was read in another instance of this module, set can be
 * used to make it available in the current instance.
 */
function set({ commit }, story) {
  commit('story', { data: story });
}

/**
 * Reset the state
 */
function reset({ commit }) {
  commit('reset');
}

function create({ commit}) {
  commit('active', new Story());
}

export const actions = {
  browse,
  read,
  save,
  remove,
  loadArchive,
  set,
  reset,
  create
};
