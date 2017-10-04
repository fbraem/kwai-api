import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Lockr from 'lockr';
import client from './client';
import Model from './model';
import JSONAPI from './JSONAPI';

const USER_KEY = 'user';

const state = {
    user : Lockr.get(USER_KEY, null),
    status : {
        loading : false,
        success : false,
        error : false
    }
};

const getters = {
    activeUser(state) {
        if (state.user) {
            return state.user.data;
        }
        return null;
    },
    isLoggedIn(state) {
        return state.user && state.user.meta.jwt != null;
    },
    loading(state) {
        return state.status.loading;
    }
};

const mutations = {
    login(state, data) {
        state.user = data.user;
    },
    logout(state) {
        state.user = null;
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
        return new Promise((resolve, reject) => {
            context.commit('loading');
            client().withoutAuth().post('api/auth/login', {
                data : payload
            }).then((response) => {
                var api = new JSONAPI();
                var user = api.parse(response.data);
                Lockr.set(USER_KEY, user);
                context.commit('login', {
                    user : user
                });
                context.commit('success');
                resolve();
            }).catch((err) => {
                context.commit('error', err);
                reject();
            });
        })
    },
    logout(context) {
        Lockr.rm(USER_KEY);
        context.commit('logout');
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
