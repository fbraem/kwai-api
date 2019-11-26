import Vue from 'vue';
import moment from 'moment';
import { state as initialize } from './state';

/**
 * Reset state
 */
function reset(state) {
  Object.assign(state, initialize());
};

/**
 * Set stories
 */
function stories(state, { meta, data }) {
  state.meta = meta;
  state.all = data;
  state.error = null;
};

/**
 * Change/Add a story in the list
 */
function story(state, { data }) {
  state.error = null;
  if (state.all == null) {
    state.all = [];
  }
  var index = state.all.findIndex((s) => s.id === data.id);
  if (index !== -1) {
    Vue.set(state.all, index, data);
  } else {
    state.all.push(data);
  }
};

/**
 * Removes a story from the list
 */
function deleteStory(state, story) {
  state.all = state.all.filter((s) => {
    return s.id !== story.id;
  });
};

/**
 * Fill the archive with month, year and number of stories in that month.
 */
function archive(state, data) {
  state.archive = {};
  data.forEach((element) => {
    if (!state.archive[element.year]) {
      state.archive[element.year] = [];
    }
    state.archive[element.year].push({
      monthName: moment.months()[element.month - 1],
      month: element.month,
      year: element.year,
      count: element.count,
    });
  });
  state.error = null;
};

/**
 * Set the error
 */
function error(state, error) {
  state.error = error;
};

/**
 * Mutations
 */
export const mutations = {
  reset,
  stories,
  story,
  deleteStory,
  archive,
  error
};
