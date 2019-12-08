import JSONAPI from '@/js/JSONAPI';
import TeamCategory from '@/models/TeamCategory';

/**
 * Get all team category
 */
const browse = async({ dispatch, commit }, payload) => {
  dispatch('wait/start', 'team_category.browse', { root: true });
  try {
    const api = new JSONAPI({ source: TeamCategory });
    commit('team_categories', await api.get());
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'team_category.browse', { root: true });
  }
};

/**
 * Save a team category
 */
const save = async({ commit }, teamCategory) => {
  try {
    var api = new JSONAPI({ source: TeamCategory });
    var result = await api.save(teamCategory);
    commit('team_category', result);
  } catch (error) {
    commit('error', error);
    throw error;
  }
};

const read = async({ dispatch, getters, commit, state }, payload) => {
  // Don't read it again when it is active ...
  if (state.active?.id === payload.id) {
    return;
  }

  var teamCategory = getters['category'](payload.id);
  if (teamCategory) { // already read
    commit('active', teamCategory);
  }

  dispatch('wait/start', 'team_category.read', { root: true });
  try {
    var api = new JSONAPI({ source: TeamCategory });
    var result = await api.get(payload.id);
    commit('active', result.data);
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'team_category.read', { root: true });
  }
};

const reset = ({ commit }) => {
  commit('reset');
};

/**
 * When a category was read in another instance of this module, set can be
 * used to make it available in the current instance.
 */
const set = ({ commit }, teamCategory) => {
  commit('active', teamCategory);
};

function create({ commit}) {
  commit('active', new TeamCategory());
}

export const actions = {
  browse,
  read,
  save,
  reset,
  set,
  create
};
