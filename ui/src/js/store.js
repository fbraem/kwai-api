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
    activeUser(state) {
        if (state.user) {
            return state.user.data;
        }
        return null;
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
/*
        return new Promise((resolve, reject) => {
            context.commit('loa        Lockr.rm(ACCESSTOKEN_KEY);
        Lockr.rm(REFRESHTOKEN_KEY);
        context.commit('logout');
ding');
            var form = new FormData();
            form.append('grant_type', 'password');
            form.append('client_id', 'clubman');
            form.append('client_secret', 'abc123');
            form.append('username', );
            form.append('password', );
            form.append('scope', 'basic');
            client().withoutAuth().post('api/auth/access_token', {
                data : form
            }).then((response) => {
                Lockr.set(ACCESSTOKEN_KEY, response.data.access_token);
                Lockr.set(REFRESHTOKEN_KEY, response.data.refresh_token);
                context.commit('access', response.data);
                //client().withAuth().get('api/user')
                context.commit('success');
                resolve();
            }).catch((err) => {
                context.commit('error', err);
                reject();
            });
        })
        */
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
