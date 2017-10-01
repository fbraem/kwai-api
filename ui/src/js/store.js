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
};

const getters = {
    activeUser : state => state.user.data,
    isLoggedIn : state => state.user && state.user.meta.jwt != null
};

const mutations = {
    login(state, data) {
        state.user = data.user;
    },
    logout(state) {
        state.user = null;
    }
};

const actions = {
    login(context, payload) {
        return client().withoutAuth().post('api/auth/login', {
            data : payload
        }).then((response) => {
            var api = new JSONAPI();
            var user = api.parse(response.data);

            Lockr.set(USER_KEY, user);

            context.commit('login', {
                user : user
            });
        }).catch((err) => {
            return Promise.reject(err);
        });
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
