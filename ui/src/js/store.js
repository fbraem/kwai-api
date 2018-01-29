import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Lockr from 'lockr';

import config from 'config';

import OAuth from '@/js/oauth';
const oauth = new OAuth();

import Model from './model';
import JSONAPI from './JSONAPI';

const state = {
    user : {
        authenticated : oauth.isAuthenticated()
    },
    page : {
        title : config.title,
        facebook : config.facebook,
        subTitle : ""
    },
    status : {
        loading : false,
        success : false,
        error : false
    }
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
    loading(state) {
        return state.status.loading;
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
    loading(state) {
        state.status = {
            loading : true,
            success: false,
            error : false
        };
    },
    success(state) {
        state.status = {
            loading : false,
            success: true,
            error : false
        };
    },
    error(state, payload) {
        state.status = {
            loading : false,
            success: false,
            error : payload
        };
    }
};

const actions = {
    login(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            oauth.login(payload.data.attributes.email, payload.data.attributes.password)
                .then((response) => {
                    context.commit('success');
                    context.commit('authenticated', true);
                    resolve(response);
                })
                .catch((response) => {
                    context.commit('error', response);
                    context.commit('authenticated', false);
                    reject(response);
                });
        });
    },
    logout(context) {
        oauth.logout();
        context.commit('authenticated', false);
    },
    setTitle(context, text) {
        context.commit('title', text);
    },
    setSubTitle(context, text) {
        context.commit('subTitle', text);
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
