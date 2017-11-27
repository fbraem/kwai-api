import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import OAuth from '@/js/oauth';
const oauth = new OAuth();

import JSONAPI from '@/js/JSONAPI';

const state = {
    users : []
};

const getters = {
};

const mutations = {
  users(state, data) {
      state.users = data.users;
  }
};

const actions = {
    read(context, payload) {
        return oauth.get('api/users', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var users = api.parse(res.data);
            context.commit('users', {
                users : users.data
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
