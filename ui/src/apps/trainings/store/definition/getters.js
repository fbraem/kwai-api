const definition = (state) => (id) => {
  if (state.all) {
    return state.all.find((def) => def.id === id);
  }
  return null;
};

export const getters = {
  definition
};
