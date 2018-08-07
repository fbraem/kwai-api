import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import OAuth from '@/js/oauth';
const oauth = new OAuth();

//import find from 'lodash/find';
//import unionBy from 'lodash/unionBy';
import URI from 'urijs';

import JSONAPI from '@/js/JSONAPI';

const state = () => {
    return {
        users : [],
        stories : [],
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
        return find(state.users, ['id', id]);
    },
    stories(state) {
        return state.stories;
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
  users(state, data) {
      state.users = data.users;
  },
  setUser(state, data) {
      state.users = unionBy([data.user], state.users, 'id');
  },
  stories(state, data) {
      state.stories = data.stories;
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
    browse(context, payload) {
        return oauth.get('api/users', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var users = api.parse(res.data);
            context.commit('users', {
                users : users.data
            });
        });
    },
    read(context, payload) {
        context.commit('loading');
        var user = context.getters['user'](payload.id);
        if (user) { // already read
            context.commit('success');
            return;
        }

        oauth.get('api/users/' + payload.id, {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('setUser', {
                user : result.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
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
