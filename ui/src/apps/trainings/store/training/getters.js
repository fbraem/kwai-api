const training = (state) => (id) => {
  if (state.all) {
    return state.all.find((training) => training.id === id);
  }
  return null;
};

export const getters = {
  training
};
