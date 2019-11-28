const training = (state) => (id) => {
  if (state.events) {
    return state.events.find((event) => event.id === id);
  }
  return null;
};

export const getters = {
  training
};
