const coach = (state) => (id) => {
  if (state.all) {
    return state.all.find((coach) => coach.id === id);
  }
  return null;
};

export const getters = {
  coach
};
