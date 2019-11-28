/**
 * Gets a member from the store
 */
const member = (state) => (id) => {
  if (state.all == null) return null;
  return state.all.find((member) => member.id === id);
};

export const getters = {
  member
};
