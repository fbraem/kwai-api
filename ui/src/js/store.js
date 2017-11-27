import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Lockr from 'lockr';

import OAuth from '@/js/oauth';
const oauth = new OAuth();

import Model from './model';
import JSONAPI from './JSONAPI';

const state = {
    user : {
        authenticated : oauth.isAuthenticated()
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
    loading(state) {
        return state.status.loading;
    }
};

const mutations = {
    authenticated(state, sw) {
        state.user.authenticated = sw;
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
        return oauth.login(payload.data.attributes.email, payload.data.attributes.password)
            .then((response) => {
                context.commit('success');
                context.commit('authenticated', true);
            })
            .catch((response) => {
                context.commit('error', response);
                context.commit('authenticated', false);
            });
    },
    logout(context) {
        oauth.logout();
        context.commit('authenticated', false);
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
