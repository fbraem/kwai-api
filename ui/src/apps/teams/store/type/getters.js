/**
 * Get a type from the list
 */
const type = (state) => (id) => {
  if (state.all) {
    return state.all.find((type) => type.id === id);
  }
  return null;
};

/**
 * Return types as items for a select
 */
const typesAsOptions = (state) => {
  let types = [];
  if (state.all) {
    types = state.all.map((type) => ({
      value: type.id,
      text: type.name }
    ));
  }
  return types;
};

export const getters = {
  type,
  typesAsOptions
};
