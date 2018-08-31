import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import User from './models/User';

const state = () => {
    return {
        users : [],
        invitations : [],
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
    invitations(state) {
        return state.invitations;
    },
    invitationByToken : (state) => (token) => {
        return state.invitations.find((invitation) => invitation.token == token);
    },
    invitationById: (state) => (id) => {
        return state.invitations.find((invitation) => invitation.id == id);
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
    async readInvitationByToken({ state, getters, commit, context }, payload) {
        commit('loading');
        var invitation = getters['invitationByToken'](payload.token);
        if (invitation) { // already read
            commit('success');
        } else {
            let model = new UserInvitation();
            try {
                model.where('token', payload.token);
                var invitations = await model.get();
                if (invitation.length > 0) {
                    invitation = invitation[0];
                    commit('invitation', invitation);
                }
                commit('success');
            } catch(error) {
                commit('error', error);
            }
        }
        return invitation;
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
