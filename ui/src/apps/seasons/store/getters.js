const seasonsAsOptions = (state) => {
  var seasons = state.all;
  if (seasons) {
    seasons = seasons.map((season) => ({
      value: season.id,
      text: season.name }
    ));
  } else {
    seasons = [];
  }
  return seasons;
};

const season = (state) => (id) => {
  if (state.all) {
    return state.all.find((s) => s.id === id);
  }
  return null;
};

export const getters = {
  seasonsAsOptions,
  season
};
