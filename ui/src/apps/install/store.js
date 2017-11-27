import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import OAuth from '@/js/oauth';
const oauth = new OAuth();

import JSONAPI from '@/js/JSONAPI';

const state = {
    installed : false,
    status : {
        loading : false,
        success : false,
        error : false
    }
};

const getters = {
};

const mutations = {
  installed(state) {
    state.installed = true;
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
    check(context) {
        context.commit('loading');
        return oauth.get('api/install/check', {
        }).then((res) => {
            context.commit('success');
        }).catch((error) => {
            if (error.response.status == 403) {
                context.commit('success');
                context.commit('installed');
            } else {
                context.commit('error', error.response.statusText);
            }
        });
    },
    install(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            return oauth.post('api/install', {
                data : payload
            }).then((res) => {
                context.commit('success');
                resolve();
            }).catch((error) => {
                context.commit('error', error);
                reject();
            });
        });
    }
};

export default {
    namespaced : true,
    state : state,
    getters : getters,
    mutations : mutations,
    actions : actions,
    modules: {
    }
};
