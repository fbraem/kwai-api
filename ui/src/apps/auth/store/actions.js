import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import config from 'config';

import { http, http_auth } from '@/js/http';

import JSONAPI from '@/js/JSONAPI';
import User from '@/models/users/User';

async function login({ commit, dispatch }, payload) {
  dispatch('wait/start', 'auth.login', { root: true });
  try {
    const form = {
      grant_type: 'password',
      client_id: config.clientId,
      username: payload.email,
      password: payload.password,
      scope: 'basic'
    };
    const json = await http
      .url('/auth/access_token')
      .formData(form)
      .post()
      .json()
    ;
    commit('login', json);
  } catch (error) {
    commit('error', error);
    throw (error);
  } finally {
    dispatch('wait/end', 'auth.login', { root: true });
  }
};

async function user({commit, dispatch}) {
  dispatch('wait/start', 'users.read', { root: true });
  try {
    var api = new JSONAPI({ source: User });
    var result = await api.path('auth').get();
    commit('user', result);
    return result.data;
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'users.read', { root: true });
  }
};

async function logout({ commit, state }) {
  const form = {
    refresh_token: state.tokenStore.refresh_token
  };
  try {
    await http_auth.url('auth/logout').formData(form).post();
  } catch (error) {
    console.log(error);
  }
  commit('logout');
};

async function refresh({ commit, state }, failedRequest) {
  if (state.tokenStore.refresh_token) {
    const form = {
      grant_type: 'refresh_token',
      client_id: config.clientId,
      refresh_token: state.tokenStore.refresh_token
    };
    const json = await http
      .url('auth/access_token')
      .formData(form)
      .post()
      .json()
    ;
    commit('login', {
      access_token: json.access_token,
      refresh_token: json.refresh_token
    });
  } else {
    throw failedRequest;
  }
}

export const actions = {
  login,
  user,
  logout,
  refresh,
};
