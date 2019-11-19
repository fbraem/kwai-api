/**
 * Get story from the list
 */
export const story = (state) => (id) => {
  if (state.all == null) return null;
  return state.all.find((story) => story.id === id);
};

export const getters = {
  story
};
