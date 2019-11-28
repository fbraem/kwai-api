/**
 * Vuex store for members
 */
import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

import JSONAPI from '@/js/JSONAPI';
import Member from '@/models/Member';

/**
 * Get all members
 */
async function browse({ dispatch, commit }, payload = {}) {
  dispatch('wait/start', 'members.browse', { root: true });

  var api = new JSONAPI({ source: Member });
  if (payload.name) {
    api.where('name', payload.name);
  }
  if (payload.active) {
    api.where('active', payload.active);
  }

  try {
    let result = await api.get();
    commit('members', result);
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'members.browse', { root: true });
  }
}

/**
 * Read a member.
 */
async function read({ getters, dispatch, commit, state }, payload) {
  // Don't read the same member again
  if (state.active?.id === payload.id) {
    return;
  }

  // Check if the member was already present in the list
  var member = getters['member'](payload.id);
  if (member) { // already read
    commit('active', member);
    return;
  }

  dispatch('wait/start', 'members.read', { root: true });
  var api = new JSONAPI({ source: Member });
  try {
    var result = await api.get(payload.id);
    commit('member', result);
    commit('active', result.data);
  } catch (error) {
    commit('error', error);
  } finally {
    dispatch('wait/end', 'members.read', { root: true });
  }
}

async function readTeams({ getters, state, dispatch, commit }, payload) {
  // Don't read the same member again ...
  if (state.active?.id === payload.id && state.active.teams) {
    return;
  }

  // Check if the member is already available in the list
  var member = getters['member'](payload.id);
  if (member && member.teams) { // already read
    commit('active', member);
    return;
  }

  dispatch('wait/start', 'members.read', { root: true });
  var api = new JSONAPI({ source: Member });
  try {
    var result = await api.with('teams').get(payload.id);
    commit('member', result); // Commit it with teams
    commit('active', result.data);
  } catch (error) {
    commit('error', error);
  } finally {
    dispatch('wait/end', 'members.read', { root: true });
  }
}

function reset({ commit }) {
  commit('reset');
}

function set({ commit }, member) {
  commit('active', { data: member });
}

export const actions = {
  browse,
  read,
  readTeams,
  reset,
  set
};
