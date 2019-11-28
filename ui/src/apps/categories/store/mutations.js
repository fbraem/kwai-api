import Vue from 'vue';

/**
 * Set categories
 */
function categories(state, { meta, data }) {
  state.all = data;
  state.meta = meta;
  state.error = null;
}

/**
 * Set category
 */
function category(state, { data }) {
  if (!state.all) state.all = [];
  var index = state.all.findIndex((c) => c.id === data.id);
  if (index !== -1) {
    Vue.set(state.all, index, data);
  } else {
    state.all.push(data);
  }
  state.error = null;
}

function deleteCategory(state, category) {
  state.all = state.all.filter((c) => {
    return category.id !== c.id;
  });
  state.error = null;
}

function error(state, error) {
  state.error = error;
}

function reset(state) {
  Object.assign(state, state());
}

/**
 * Mutations
 */
export const mutations = {
  categories,
  category,
  deleteCategory,
  error,
  reset,
};
