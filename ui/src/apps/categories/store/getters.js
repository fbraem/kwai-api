/**
 * Gets a category from the list
 */
const category = (state) => (id) => {
  if (state.all) {
    return state.all.find((category) => category.id === id);
  }
  return null;
};

/**
 * Gets the category for the given app
 */
export const categoryApp = (state) => (app) => {
  if (state.all) {
    return state.all.find((category) => category.app === app);
  }
  return null;
};

/**
 * Returns categories for use in a select
 */
export const categoriesAsOptions = (state) => {
  var categories = state.all;
  if (categories) {
    categories = categories.map((category) => ({
      value: category.id,
      text: category.name }
    ));
  } else {
    categories = [];
  }
  return categories;
};

export const getters = {
  category,
  categoryApp,
  categoriesAsOptions
};
