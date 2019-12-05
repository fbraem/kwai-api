import JSONAPI from '@/js/JSONAPI';
import TeamType from '@/models/TeamType';

/**
 * Get all team types
 */
const browse = async({ dispatch, commit }, payload) => {
  dispatch('wait/start', 'team_types.browse', { root: true });
  try {
    const api = new JSONAPI({ source: TeamType });
    commit('types', await api.get());
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'team_types.browse', { root: true });
  }
};

/**
 * Save a type
 */
const save = async({ commit }, type) => {
  try {
    var api = new JSONAPI({ source: TeamType });
    var result = await api.save(type);
    commit('type', result);
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

  var type = getters['type'](payload.id);
  if (type) { // already read
    commit('active', type);
  }

  dispatch('wait/start', 'team_types.read', { root: true });
  try {
    var api = new JSONAPI({ source: TeamType });
    var result = await api.get(payload.id);
    commit('active', result.data);
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'team_types.read', { root: true });
  }
};

const reset = ({ commit }) => {
  commit('reset');
};

/**
 * When a type was read in another instance of this module, set can be
 * used to make it available in the current instance.
 */
const set = ({ commit }, type) => {
  commit('active', type);
};

export const actions = {
  browse,
  read,
  save,
  reset,
  set
};
