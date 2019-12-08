/**
 * Get a category from the list
 */
const category = (state) => (id) => {
  if (state.all) {
    return state.all.find((c) => c.id === id);
  }
  return null;
};

/**
 * Return categories as items for a select
 */
const categoriesAsOptions = (state) => {
  let categories = [];
  if (state.all) {
    categories = state.all.map((c) => ({
      value: c.id,
      text: c.name }
    ));
  }
  return categories;
};

export const getters = {
  category,
  categoriesAsOptions
};
