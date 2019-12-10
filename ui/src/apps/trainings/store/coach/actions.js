import JSONAPI from '@/js/JSONAPI';
import TrainingCoach from '@/models/trainings/Coach';

const browse = async({ dispatch, commit }, payload) => {
  dispatch('wait/start', 'training.coaches.browse', { root: true });
  try {
    var api = new JSONAPI({ source: TrainingCoach });
    api.sort('name');
    commit('coaches', await api.get());
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'training.coaches.browse', { root: true });
  }
};

const save = async({ dispatch, commit }, coach) => {
  try {
    var api = new JSONAPI({ source: TrainingCoach });
    var result = await api.save(coach);
    commit('coach', result);
    return result.data;
  } catch (error) {
    commit('error', error);
    throw error;
  }
};

const read = async({ dispatch, getters, commit, state }, payload) => {
  // Don't read it again when it is active ...
  if (state.active?.id === payload.id) {
    return;
  }

  const coach = getters['coach'](payload.id);
  if (coach) { // already read
    commit('active', coach);
    return;
  }

  dispatch('wait/start', 'training.coaches.read', { root: true });
  try {
    var api = new JSONAPI({ source: TrainingCoach });
    var result = await api.get(payload.id);
    commit('active', result.data);
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'training.coaches.read', { root: true });
  }
};

const reset = ({ commit }) => {
  commit('reset');
};

export const actions = {
  browse,
  read,
  save,
  reset
};
