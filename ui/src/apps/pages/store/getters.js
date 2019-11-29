/**
 * Get a page from the list
 */
const page = (state) => (id) => {
  if (state.all) {
    return state.all.find((page) => page.id === id);
  }
  return null;
};

export const getters = {
  page
};
