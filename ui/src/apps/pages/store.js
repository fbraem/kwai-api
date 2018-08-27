import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

import Page from './models/Page';

const state = {
    pages : [],
    status : {
        loading : false,
        success : false,
        error : false
    },
    meta : null
};

const getters = {
    pages(state) {
        return state.pages;
    },
    page: (state) => (id) => {
        return state.pages.find((page) => page.id == id);
    },
    loading(state) {
        return state.status.loading;
    },
    success(state) {
        return state.status.success;
    },
    error(state) {
        return state.status.error;
    },
    meta(state) {
        return state.meta;
    }
};

const mutations = {
  pages(state, pages) {
      state.meta = pages.meta();
      state.pages = pages;
  },
  page(state, page) {
      var index = state.pages.findIndex((p) => p.id == page.id);
      if (index != -1) {
          Vue.set(state.pages, index, page);
      } else {
          state.pages.push(page);
      }
  },
  deletePage(state, page) {
      state.pages = state.pages.filter((p) => {
         return page.id != p.id;
      });
  },
  attachContent(state, data) {
      var index = state.pages.findIndex((p) => p.id == data.page.id);
      if (index != -1) {
          if (state.pages[index].contents == null) {
              state.pages[index].contents = [];
          }
          state.pages[index].contents.push(data.content);
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
        const page = new Page();
        if (payload.offset || payload.limit) {
            page.paginate(payload.offset, payload.limit);
        }
        if (payload.category) {
            page.where('category', payload.category);
        }
        if (payload.user) {
            page.where('user', payload.user);
        }
        let pages = await page.get();
        commit('pages', pages);
        commit('success');
    },
    async read({ state, getters, commit, context }, payload) {
        commit('loading');
        var page = getters['page'](payload.id);
        if (page) { // already read
            commit('success');
        }
        else {
            let model = new Page();
            try {
                page = await model.find(payload.id);
                commit('page', page);
                commit('success');
            } catch(error) {
                commit('error', error);
            }
        }
        return page;
    },
    async save({ state, getters, commit, context }, page) {
        try  {
            var newPage = await page.save();
            commit('page', newPage);
            return newPage;
        } catch(error) {
            commit('error', error);
            throw error;
        }
    },
    async attachContent({ state, getters, commit, context }, payload) {
        try {
            var newPage = await payload.page.attach(payload.content);
            commit('page', newPage);
            return newPage;
        }
        catch(error)
        {
            commit('error', error);
            throw(error);
        }
    },
    async delete({ state, getters, commit, context }, payload) {
        commit('loading');
        try {
            await payload.page.delete();
            commit('deletePage', { id : payload.page.id });
            commit('success');
        } catch(error) {
            commit('error', error);
            throw(error);
        }
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
