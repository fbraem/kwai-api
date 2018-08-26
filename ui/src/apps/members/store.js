import Vue from 'vue';

import OAuth from '@/js/oauth';
const oauth = new OAuth();
import axios from 'axios';

import Vuex from 'vuex';
Vue.use(Vuex);

import URI from 'urijs';
import moment from 'moment';

const state = {
    members : [],
    status : {
        loading : false,
        success : false,
        error : false
    }
};

const getters = {
    members(state) {
        return state.members;
    },
    member: (state) => (id) => {
        return find(state.members, ['id', id]);
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
  members(state, data) {
      state.members = data.members;
  },
  setMember(state, data) {
      state.members = unionBy([data.member], state.members, 'id');
  },
  deleteMember(state, data) {
      state.members = filter(state.members, (member) => {
         return member.id != member.id;
      });
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
        context.commit('loading');
        var uri = new URI('api/sport/judo/members');
        var offset = payload.offset || 0;
        uri.addQuery('page[offset]', offset);
/*
        if (payload.category) {
            uri.addQuery('filter[category]', payload.category);
        }
        if (payload.year) {
            uri.addQuery('filter[year]', payload.year);
        }
        if (payload.month) {
            uri.addQuery('filter[month]', payload.month);
        }
        if (payload.featured) {
            uri.addQuery('filter[featured]', true);
        }
        if (payload.user) {
            uri.addQuery('filter[user]', payload.user);
        }
*/
        oauth.get(uri.href(), {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var members = api.parse(res.data);
            context.commit('members', {
                members : members.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    read(context, payload) {
        context.commit('loading');
        var member = context.getters['member'](payload.id);
        if (member) { // already read
            context.commit('success');
            return;
        }

        oauth.get('api/members/' + payload.id, {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('setMember', {
                member : result.data
            });
            context.commit('success');
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    create(context, payload) {
        context.commit('loading');
        return oauth.post('api/members', {
            data : payload
        }).then((res) => {
            var api = new JSONAPI();
            var result = api.parse(res.data);
            context.commit('setMember', {
                member : result.data
            });
            context.commit('success');
            return result.data;
        }).catch((error) => {
            context.commit('error', error);
        });
    },
    update(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            oauth.patch('api/members/' + payload.data.id, {
                data : payload
            }).then((res) => {
                var api = new JSONAPI();
                var result = api.parse(res.data);
                context.commit('setMember', {
                    member : result.data
                });
                context.commit('success');
                resolve();
            }).catch((error) => {
                context.commit('error', error);
                reject();
            });
        });
    },
    delete(context, payload) {
        context.commit('loading');
        return new Promise((resolve, reject) => {
            oauth.delete('api/members/' + payload.id)
            .then((res) => {
                context.commit('deleteMember', { id : payload.id });
                context.commit('success');
                resolve();
            }).catch((error) => {
                context.commit('error', error);
                reject();
            });
        });
    },
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
