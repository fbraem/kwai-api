import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import client from '@/js/client';
import JSONAPI from '@/js/JSONAPI';

const state = {
    user : []
};

const getters = {
};

const mutations = {
  user(state, data) {
      state.user = data.user;
  }
};

const actions = {
    install(context, payload) {
        return client().withAuth().post('api/install', {
            data : payload
        }).then((res) => {
            api = new JSONAPI();
            console.log('JOEHOE');
            console.log(api.parse(res.data));
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
