import JSONAPI from '@/js/JSONAPI';
import TrainingDefinition from '@/models/trainings/Definition';

const browse = async({ dispatch, commit }, payload) => {
  dispatch('wait/start', 'training.definitions.browse', { root: true });
  try {
    const api = new JSONAPI({ source: TrainingDefinition });
    commit('definitions', await api.get());
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'training.definitions.browse', { root: true });
  }
};

const save = async({ dispatch, commit }, definition) => {
  try {
    const api = new JSONAPI({ source: TrainingDefinition });
    const result = await api.save(definition);
    commit('definition', result);
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

  var definition = getters['definition'](payload.id);
  if (definition) { // already read
    commit('active', definition);
    return;
  }

  dispatch('wait/start', 'training.definitions.read', { root: true });
  try {
    const api = new JSONAPI({ source: TrainingDefinition });
    const result = await api.get(payload.id);
    commit('active', result.data);
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'training.definitions.read', { root: true });
  }
};

const reset = ({ commit }) => {
  commit('reset');
};

const set = ({ commit, data }) => {
  commit('active', data);
};

const create = ({ commit }) => {
  commit('active', new TrainingDefinition());
};

export const actions = {
  browse,
  read,
  reset,
  save,
  set,
  create
};
