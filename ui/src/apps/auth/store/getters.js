function authenticated(state) {
  return state.tokenStore.access_token != null;
}

function guest(state) {
  return state.tokenStore.access_token === null;
}

export const getters = {
  authenticated,
  guest
};
