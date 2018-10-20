import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Lockr from 'lockr';

import config from 'config';

import OAuth from '@/js/oauth';
const oauth = new OAuth();

const state = {
    user : {
        authenticated : oauth.isAuthenticated()
    },
    page : {
        title : config.title,
        facebook : config.facebook,
        subTitle : ""
    },
    error : null
};

const getters = {
    user(state) {
        return state.user;
    },
    title(state) {
        return state.page.title;
    },
    subTitle(state) {
        return state.page.subTitle;
    },
    facebook(state) {
        return state.page.facebook;
    },
    error(state) {
        return state.error;
    }
};

const mutations = {
    authenticated(state, sw) {
        state.user.authenticated = sw;
    },
    title(state, text) {
        state.page.title = text;
    },
    subTitle(state, text) {
        state.page.subTitle = text;
    },
    error(state, payload) {
        error = payload;
    }
};

const actions = {
    async login({ commit, dispatch }, payload) {
        dispatch('wait/start', 'auth.login', { root : true });
        try {
            var response = await oauth.login(payload.email, payload.password);
            commit('authenticated', true);
        } catch(error) {
            dispatch('wait/end', 'auth.login', { root : true });
            commit('error', error);
            commit('authenticated', false);
            throw(error);
        }
        dispatch('wait/end', 'auth.login', { root : true });
    },
    async logout({ commit }) {
        await oauth.logout();
        commit('authenticated', false);
    },
    setTitle({ commit }, text) {
        commit('title', text);
    },
    setSubTitle({ commit }, text) {
        commit('subTitle', text);
    }
};

export default new Vuex.Store({
    namespaced : true,
    state : state,
    getters : getters,
    mutations : mutations,
    actions : actions,
    modules: {
    }
});
