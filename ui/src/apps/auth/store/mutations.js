function login(state, { access_token, refresh_token }) {
  state.tokenStore.setTokens(access_token, refresh_token);
  state.error = null;
}

function user(state, { data }) {
  let rules = [];
  if (data.abilities) {
    for (let ability of data.abilities) {
      for (let rule of ability.rules) {
        rules.push({
          actions: rule.action.name,
          subject: rule.subject.name
        });
      }
    }
  }
  state.localStorage.set(data, rules);
  state.error = null;
}

function logout(state) {
  state.tokenStore.clear();
  state.localStorage.clear();
  state.error = null;
}

function error(state, error) {
  state.error = error;
}

export const mutations = {
  login,
  user,
  logout,
  error
};
