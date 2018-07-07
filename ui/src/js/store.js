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
    async login(context, payload) {
        context.commit('loading');
        try {
            var response = await oauth.login(payload.email, payload.password);
            context.commit('success');
            context.commit('authenticated', true);
        } catch(error) {
            context.commit('error', error);
            context.commit('authenticated', false);
            throw(error);
        }
    },
    async logout(context) {
        await oauth.logout();
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
