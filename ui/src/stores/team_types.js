import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import TeamType from '@/models/TeamType';

const state = {
    types : null,
    error : null
};

const getters = {
    types(state) {
        return state.types;
    },
    type: (state) => (id) => {
        if (state.types) {
            return state.types.find((type) => type.id == id);
        }
        return null;
    },
    error(state) {
        return state.error;
    }
};

const mutations = {
  types(state, types) {
      state.types = types;
      state.error = null;
  },
  type(state, type) {
      if (state.types == null) {
          state.types = [];
      }
      var index = state.types.findIndex((t) => t.id == type.id);
      if (index != -1) {
          Vue.set(state.types, index, type);
      } else {
          state.types.push(type);
      }
      state.error = null;
  },
  error(state, data) {
      state.error = data;
  }
};

const actions = {
    async browse({ dispatch, commit }, payload) {
        dispatch('wait/start', 'team_types.browse', { root : true });
        const type = new TeamType();
        try {
            commit('types', await type.get());
            dispatch('wait/end', 'team_types.browse', { root : true });
        } catch(error) {
            commit('error', error);
            dispatch('wait/end', 'team_types.browse', { root : true });
            throw error;
        }
    },
    async save({ dispatch, commit }, type) {
        var newType = null;
        try  {
            newType = await type.save();
            commit('type', newType);
            return newType;
        } catch(error) {
            commit('error', error);
            throw error;
        }
    },
    async read({ dispatch, getters, commit }, payload) {
        var type = getters['type'](payload.id);
        if (type) { // already read
            return type;
        }

        dispatch('wait/start', 'team_types.read', { root : true });
        let model = new TeamType();
        try {
            type = await model.find(payload.id);
            commit('type', type);
            dispatch('wait/end', 'team_types.read', { root : true });
        } catch(error) {
            commit('error', error);
            dispatch('wait/end', 'team_types.read', { root : true });
            throw error;
        }
        return type;
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
