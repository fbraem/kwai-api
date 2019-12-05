import JSONAPI from '@/js/JSONAPI';
import Team from '@/models/Team';
import Member from '@/models/Member';

const browse = async({ dispatch, commit }, payload) => {
  dispatch('wait/start', 'teams.browse', { root: true });
  try {
    var api = new JSONAPI({ source: Team });
    let result = await api.get();
    commit('teams', result);
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'teams.browse', { root: true });
  }
};

const save = async({ dispatch, commit }, team) => {
  try {
    var api = new JSONAPI({ source: Team });
    var result = await api.save(team);
    commit('team', result);
    return result.data;
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

  var team = getters['team'](payload.id);
  if (team) { // already read
    commit('active', team);
    return;
  }

  dispatch('wait/start', 'teams.read', { root: true });
  try {
    var api = new JSONAPI({ source: Team });
    var result = await api.get(payload.id);
    commit('active', result.data);
    return result.data;
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'teams.read', { root: true });
  }
};

const getMembers = async({ dispatch, commit, state, getters }, payload) => {
  if (state.active?.id === payload.id && state.active.members) {
    return;
  }

  var team = getters['team'](payload.id);
  if (team && team.members) { // already read
    commit('active', team);
    return;
  }

  dispatch('wait/start', 'teams.members', { root: true });
  try {
    const api = new JSONAPI({ source: Team });
    const result = await api.with(['members']).get(payload.id);
    commit('active', result.data);
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'teams.members', { root: true });
  }
};

const addMembers = async({ getters, commit }, payload) => {
  var team = getters['team'](payload.id);
  try {
    const api = new JSONAPI({source: Team, target: Member});
    let result = await api.attach(team, payload.members);
    commit('setMembers', {
      id: payload.id,
      members: result.data,
    });
  } catch (error) {
    commit('error', error);
    console.log(error);
  }
};

const deleteMembers = async({ getters, commit }, payload) => {
  var team = getters['team'](payload.id);
  try {
    const api = new JSONAPI({source: Team, target: Member});
    let result = await api.detach(team, payload.members);
    commit('setMembers', {
      id: payload.id,
      members: result.data,
    });
  } catch (error) {
    commit('error', error);
    console.log(error);
  }
};

const availableMembers = async({ dispatch, commit }, payload) => {
  commit('clearAvailableMembers');
  dispatch('wait/start', 'teams.availableMembers', { root: true });

  var api = new JSONAPI({ source: Team, target: Member});
  if (payload.filter) {
    if (payload.filter.start_age) {
      api.where('start_age', '>=' + payload.filter.start_age);
    }
    if (payload.filter.end_age) {
      api.where('end_age', '<=' + payload.filter.end_age);
    }
    if (payload.filter.gender) {
      api.where('gender', payload.filter.gender);
    }
  }
  try {
    var members = await api.custom({
      id: payload.id,
      path: 'available_members'
    });
    commit('availableMembers', members);
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'teams.availableMembers', { root: true });
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
  getMembers,
  addMembers,
  deleteMembers,
  availableMembers,
  reset,
  set,
};
