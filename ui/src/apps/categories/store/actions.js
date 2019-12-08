import JSONAPI from '@/js/JSONAPI';

import Category from '@/models/Category';

export async function browse({ dispatch, commit }, payload) {
  dispatch('wait/start', 'categories.browse', { root: true });
  try {
    var api = new JSONAPI({ source: Category });
    if (payload) {
      if (payload.app) {
        api.where('app', payload.app);
      }
    }
    commit('categories', await api.get());
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'categories.browse', { root: true });
  }
}

async function read({ dispatch, getters, commit }, { id }) {
  var category = getters['category'](id);
  if (category) { // already read
    return category;
  }

  dispatch('wait/start', 'categories.read', { root: true });
  try {
    var api = new JSONAPI({ source: Category });
    var result = await api.get(id);
    commit('category', result);
    return result.data;
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'categories.read', { root: true });
  }
}

async function readApp({ dispatch, getters, commit }, { app }) {
  var category = getters['categoryApp'](app);
  if (category) { // already read
    return category;
  }

  // dispatch('wait/start', 'categories.read', { root: true });
  try {
    var api = new JSONAPI({ source: Category });
    api.where('app', app);
    var result = await api.get();
    commit('category', { data: result.data[0]});
    return result.data[0];
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    // dispatch('wait/end', 'categories.read', { root: true });
  }
}

async function save({ commit }, category) {
  try {
    var api = new JSONAPI({ source: Category });
    var result = await api.save(category);
    commit('category', result);
    return result.data;
  } catch (error) {
    commit('error', error);
    throw error;
  }
}

function reset({ commit }) {
  commit('reset');
}

function create({ commit}) {
  commit('active', new Category);
}

export const actions = {
  browse,
  read,
  readApp,
  save,
  reset,
  create
};
