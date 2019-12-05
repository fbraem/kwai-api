const teamsAsOptions = (state) => {
  let teams = [];
  if (state.all) {
    teams = state.all.map((team) => ({
      value: team.id,
      text: team.name
    }));
  }
  return teams;
};

const team = (state) => (id) => {
  if (state.all) {
    return state.all.find((team) => team.id === id);
  }
  return null;
};

const members = (state) => (id) => {
  var team = state.all.find((team) => team.id === id);
  if (team) {
    if (team.members) {
      return team.members;
    }
  }
  return null;
};

export const getters = {
  teamsAsOptions,
  team,
  members
};
