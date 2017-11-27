import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import OAuth from '@/js/oauth';
const oauth = new OAuth();

import JSONAPI from '@/js/JSONAPI';

const state = {
};

const getters = {
};

const mutations = {
};

const actions = {
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
