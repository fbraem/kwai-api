import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Lockr from 'lockr';
import client from './client';
import Model from './model';
import JSONAPI from './JSONAPI';

import OAuth from './oauth';

const state = {
    oauth : new OAuth(),
    status : {
        loading : false,
        success : false,
        error : false
    }
};

const getters = {
    oauth(state) {
        return state.oauth;
    },
    isLoggedIn(state) {
        return state.oauth.isAuthenticated();
    },
    loading(state) {
        return state.status.loading;
    }
};

const mutations = {
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
        return context.state.oauth.login(payload.data.attributes.email, payload.data.attributes.password)
            .then((response) => {
                context.commit('success');
            })
            .catch((response) => {
                context.commit('error', response);
            });
    },
    logout(context) {
        context.state.oauth.logout();
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
