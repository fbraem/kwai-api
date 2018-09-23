import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Member from './models/Member';

const state = {
    members : [],
    error : null
};

const getters = {
    members(state) {
        return state.members;
    },
    member: (state) => (id) => {
        return find(state.members, ['id', id]);
    },
    error(state) {
        return state.error;
    }
};

const mutations = {
  members(state, members) {
      state.members = members;
      state.error = null;
  },
  member(state, member) {
      var index = state.members.findIndex((m) => m.id == member.id);
      if (index != -1) {
          Vue.set(state.members, index, member);
      } else {
          state.members.push(member);
      }
      state.error = null;
  }
};

const actions = {
    async browse({ dispatch, commit }, payload) {
        dispatch('wait/start', 'members.browse', { root : true });
        const member = new Member();
        try {
            let members = await member.get();
            commit('members', members);
            dispatch('wait/end', 'members.browse', { root : true });
        } catch(error) {
            commit('error', error);
            dispatch('wait/end', 'members.browse', { root : true });
            throw error;
        }

/*
        dispatch('wait/start', 'members.browse', { root : true });
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
*/
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
