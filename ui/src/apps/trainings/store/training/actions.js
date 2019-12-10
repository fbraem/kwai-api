import JSONAPI from '@/js/JSONAPI';
import Training from '@/models/trainings/Training';
import Presence from '@/models/trainings/Presence';

const browse = async({ dispatch, commit }, payload) => {
  const api = new JSONAPI({ source: Training });
  if (payload.year) {
    api.where('year', payload.year);
    if (payload.month) {
      api.where('month', payload.month);
    }
  }
  if (payload.coach) {
    api.where('coach', payload.coach);
  }
  dispatch('wait/start', 'training.browse', { root: true });
  try {
    commit('trainings', await api.get());
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'training.browse', { root: true });
  }
};

const save = async({ dispatch, commit }, training) => {
  try {
    const api = new JSONAPI({ source: Training });
    const result = await api.save(training);
    commit('training', result);
    return result.data;
  } catch (error) {
    commit('error', error);
    throw error;
  }
};

const read = async({ dispatch, getters, commit }, { id, cache = true }) => {
  if (cache) {
    var training = getters['training'](id);
    if (training) { // already read
      commit('error', null); // Reset error
      return training;
    }
  }

  dispatch('wait/start', 'training.read', { root: true });
  try {
    const api = new JSONAPI({ source: Training });
    const result = await api.get(id);
    commit('training', result);
    return result.data;
  } catch (error) {
    commit('error', error);
    dispatch('wait/end', 'training.read', { root: true });
    throw error;
  } finally {
    dispatch('wait/end', 'training.read', { root: true });
  }
};

const createAll = async({commit, state}, trainings) => {
  try {
    const api = new JSONAPI({ source: Training });
    const result = await api.save(trainings);
    commit('training', result);
    return result.data;
  } catch (error) {
    commit('error', error);
    throw error;
  }
};

const updatePresences = async({commit, state}, {training, presences}) => {
  try {
    const api = new JSONAPI({ source: Presence });
    api.path(training.id);
    const result = await api.save(presences);
    commit('setPresences', result);
    return result.data;
  } catch (error) {
    commit('error', error);
    throw error;
  }
};

const reset = ({ commit }) => {
  commit('reset');
};

const create = ({ commit }) => {
  commit('active', new Training);
};

export const actions = {
  browse,
  read,
  save,
  reset,
  createAll,
  updatePresences,
  create
};
