/**
 * Gets a category from the list
 */
const category = (state) => (id) => {
  if (state.categories) {
    return state.categories.find((category) => category.id === id);
  }
  return null;
};

/**
 * Gets the category for the given app
 */
export const categoryApp = (state) => (app) => {
  if (state.categories) {
    return state.categories.find((category) => category.app === app);
  }
  return null;
};

/**
 * Returns categories for use in a select
 */
export const categoriesAsOptions = (state) => {
  var categories = state.categories;
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
