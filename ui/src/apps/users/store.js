import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import User from './models/User';

const state = () => {
    return {
        users : [],
        status : {
            loading : false,
            success : false,
            error : false
        }
    };
};

const getters = {
    users(state) {
        return state.users;
    },
    user: (state) => (id) => {
        return state.users.find((user) => user.id == id);
    },
    loading(state) {
        return state.status.loading;
    },
    success(state) {
        return state.status.success;
    },
    error(state) {
        return state.status.error;
    }
};

const mutations = {
  users(state, users) {
      state.users = users;
  },
  user(state, user) {
      var index = state.users.findIndex((u) => u.id == user.id);
      if (index != -1) {
          Vue.set(state.users, index, user);
      } else {
          state.users.push(user);
      }
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
    async browse({ state, getters, commit, context }, payload) {
        commit('loading');
        const user = new User();
        let users = await user.get();
        commit('users', users);
        commit('success');
    },
    async read({ state, getters, commit, context }, payload) {
        commit('loading');
        var user = getters['user'](payload.id);
        if (user) { // already read
            commit('success');
        } else {
            let model = new User();
            try {
                user = await model.find(payload.id);
                commit('user', user);
                commit('success');
            } catch(error) {
                commit('error', error);
            }
        }
        return user;
    },
    browseNews(context, payload) {
        context.commit('loading');
        var uri = new URI();
        uri.segment(['api', 'users', 'news', payload.id]);
        var offset = payload.offset || 0;
        uri.addQuery('page[offset]', offset);

        oauth.get(uri.href(), {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var stories = api.parse(res.data);
            context.commit('stories', {
                stories : stories.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
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
